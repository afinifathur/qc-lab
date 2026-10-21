<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Sample;
use App\Models\SpectroResult;
use App\Models\TensileTest;
use App\Models\HardnessTest;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

class SampleStandardValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;
    private User $approver;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Operator');
        Role::findOrCreate('Approver');

        $this->operator = User::factory()->create(['email' => 'operator@test.com']);
        $this->operator->assignRole('Operator');

        $this->approver = User::factory()->create(['email' => 'kabagqc@test.com']);
        $this->approver->assignRole('Approver');
    }

    /**
     * Requirement: Seluruh 9 grade (6 lama + 3 baru) diterima oleh validasi dan disimpan sebagai draft.
     */
    public function test_registered_grades_are_accepted_and_stored_as_draft(): void
    {
        $allGrades = \App\Services\QCSpecificationService::getGrades();
        $this->assertCount(9, $allGrades);

        foreach ($allGrades as $grade) {
            $heatNo = 'HEAT-' . substr(preg_replace('/[^A-Za-z0-9]/', '', $grade), 0, 10);
            $response = $this->actingAs($this->operator)
                ->post(route('samples.store'), [
                    'grade'        => $grade,
                    'product_type' => 'Flange',
                    'heat_no'      => $heatNo,
                    'test_date'    => '2026-10-21',
                    'c'            => 0.04,
                    'cr'           => 17.5,
                    'ni'           => 10.5,
                ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('samples.index'));

            $sample = Sample::where('heat_no', $heatNo)->first();
            $this->assertNotNull($sample, "Sample untuk grade {$grade} gagal disimpan");
            $this->assertSame($grade, $sample->grade);
            $this->assertNotEmpty($sample->standard);
        }
    }

    /**
     * Requirement 4: Grade yang tidak dikenal atau tidak terdaftar ditolak validasi.
     */
    public function test_unregistered_grade_is_rejected_by_validation(): void
    {
        $response = $this->actingAs($this->operator)
            ->post(route('samples.store'), [
                'grade'        => 'INVALID_GRADE_XYZ',
                'product_type' => 'Flange',
                'heat_no'      => 'HEAT-FAIL',
                'test_date'    => '2026-10-21',
            ]);

        $response->assertSessionHasErrors(['grade']);
        $this->assertDatabaseMissing('samples', ['heat_no' => 'HEAT-FAIL']);
    }

    /**
     * Requirement: PDF berhasil dirender untuk seluruh 9 grade pada kedua template dan tepat 1 halaman A4.
     */
    public function test_pdf_report_renders_successfully_for_all_nine_grades_and_both_templates_single_page(): void
    {
        $allGrades = \App\Services\QCSpecificationService::getGrades();

        foreach ($allGrades as $grade) {
            $expectedStd = \App\Services\QCSpecificationService::getStandardForGrade($grade);
            $heatNo = 'H-' . substr(preg_replace('/[^A-Za-z0-9]/', '', $grade), 0, 10);

            $sample = Sample::create([
                'grade'        => $grade,
                'standard'     => $expectedStd,
                'product_type' => 'Flange',
                'heat_no'      => $heatNo,
                'status'       => 'APPROVED',
                'report_no'    => '2026/PDF/' . uniqid() . '_' . $grade,
                'approved_at'  => '2026-10-21 10:00:00',
            ]);

            SpectroResult::create([
                'sample_id' => $sample->id,
                'c'         => 0.045,
                'cr'        => 17.50,
                'ni'        => 10.20,
            ]);

            TensileTest::create([
                'sample_id' => $sample->id,
                'ys_mpa'    => 220,
                'uts_mpa'   => 500,
                'elong_pct' => 35,
            ]);

            HardnessTest::create([
                'sample_id' => $sample->id,
                'method'    => 'HB',
                'avg_value' => 150,
            ]);

            // 1. Test via HTTP route (reports.pdf)
            $response = $this->actingAs($this->operator)
                ->get(route('reports.pdf', $sample));

            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');

            // 2. Test kedua template dan verifikasi tepat 1 halaman A4 serta Option A footer
            foreach (['reports.pdf', 'pdf.report'] as $viewName) {
                $limits = \App\Services\QCSpecificationService::getLimitsForGrade($grade);
                $eval = \App\Services\QCSpecificationService::evaluateSample($sample);

                $pdf = Pdf::loadView($viewName, [
                    'sample'        => $sample,
                    'isPreview'     => false,
                    'stampUser'     => 'tester',
                    'stampTime'     => '2026-10-21 10:00:00',
                    'stampId'       => 'test-uuid-1234',
                    'logoData'      => null,
                    'signatureData' => null,
                    'stampImgData'  => null,
                    'standard'      => $sample->standard,
                    'limits'        => $limits,
                    'eval'          => $eval,
                ])->setPaper('A4');

                $output = $pdf->output();
                $canvas = $pdf->getCanvas();
                $pageCount = $canvas->get_page_count();

                $this->assertSame(
                    1,
                    $pageCount,
                    "PDF untuk grade '{$grade}' pada template '{$viewName}' melebihi 1 halaman (Page count: {$pageCount})"
                );

                // Verifikasi Footer Opsi A ter-render spesifik untuk sampel
                $renderedHtml = view($viewName, [
                    'sample'        => $sample,
                    'isPreview'     => false,
                    'stampUser'     => 'tester',
                    'stampTime'     => '2026-10-21 10:00:00',
                    'stampId'       => 'test-uuid-1234',
                    'logoData'      => null,
                    'signatureData' => null,
                    'stampImgData'  => null,
                    'standard'      => $sample->standard,
                    'limits'        => $limits,
                    'eval'          => $eval,
                ])->render();

                $expectedFooterOptionA = "{$expectedStd} ({$grade})";
                $this->assertStringContainsString(
                    $expectedFooterOptionA,
                    $renderedHtml,
                    "Footer Opsi A tidak ditemukan untuk grade {$grade} pada template {$viewName}"
                );
            }
        }
    }

    /**
     * Requirement: Submenu Standard Reference bersifat read-only dan menampilkan seluruh 6 standard & 9 grade.
     */
    public function test_standard_references_submenu_is_strictly_read_only_and_displays_all_standards(): void
    {
        // 1. Guest diarahkan ke login
        $guestRes = $this->get(route('standard-references.index'));
        $guestRes->assertRedirect(route('login'));

        // 2. Authenticated user dapat mengakses view
        $authRes = $this->actingAs($this->operator)->get(route('standard-references.index'));
        $authRes->assertStatus(200);

        // 3. Menampilkan seluruh 6 nama standard
        $expectedStandards = [
            'ASTM A351',
            'ASTM A959',
            'JIS G 5121',
            'JIS G4403',
            'BS EN 10213',
            'BS EN 10088-1',
        ];
        foreach ($expectedStandards as $std) {
            $authRes->assertSee($std);
        }

        // 4. Menampilkan seluruh 9 nama grade
        $expectedGrades = \App\Services\QCSpecificationService::getGrades();
        foreach ($expectedGrades as $grade) {
            $authRes->assertSee($grade);
        }

        // 5. Verifikasi template standards/index tidak memiliki form manipulasi apa pun (strictly read-only)
        $bladeSource = file_get_contents(resource_path('views/standards/index.blade.php'));
        $this->assertStringNotContainsString('<form', $bladeSource);
        $this->assertStringNotContainsString('<input', $bladeSource);
        $this->assertStringNotContainsString('method="POST"', $bladeSource);
        $this->assertStringNotContainsString('method="post"', $bladeSource);

        // Verifikasi dalam full page response hanya ada 1 form, yaitu form logout milik layout
        $content = $authRes->getContent();
        $this->assertSame(1, substr_count($content, '<form '));
        $this->assertStringContainsString(route('logout'), $content);

        // 6. Verifikasi HTTP POST / PUT / DELETE ke endpoint ditolak (405 Method Not Allowed)
        $this->actingAs($this->operator)->post(route('standard-references.index'))->assertStatus(405);
        $this->actingAs($this->operator)->put(route('standard-references.index'))->assertStatus(405);
        $this->actingAs($this->operator)->delete(route('standard-references.index'))->assertStatus(405);
    }

    /**
     * Requirement 10: Workflow submit dan approval tetap berjalan dengan baik.
     */
    public function test_workflow_submission_and_approval_process(): void
    {
        // 1. Operator membuat sample
        $sample = Sample::create([
            'grade'        => 'CF8',
            'standard'     => 'ASTM A351',
            'product_type' => 'Flange',
            'heat_no'      => 'H-APPROVAL-TEST',
            'status'       => 'DRAFT',
        ]);

        SpectroResult::create(['sample_id' => $sample->id, 'c' => 0.05, 'cr' => 19.0, 'ni' => 9.5]);
        TensileTest::create(['sample_id' => $sample->id, 'ys_mpa' => 210, 'uts_mpa' => 490, 'elong_pct' => 36]);
        HardnessTest::create(['sample_id' => $sample->id, 'avg_value' => 160]);

        // 2. Submit draft -> SUBMITTED
        $submitRes = $this->actingAs($this->operator)
            ->post(route('samples.submit', $sample));
        $submitRes->assertSessionHas('ok');

        $sample->refresh();
        $this->assertSame('SUBMITTED', $sample->status);
        $this->assertNotNull($sample->report_no);

        // 3. Approver approve -> APPROVED
        $approveRes = $this->actingAs($this->approver)
            ->post(route('approvals.approve', $sample));
        $approveRes->assertSessionHas('ok');

        $sample->refresh();
        $this->assertSame('APPROVED', $sample->status);
        $this->assertNotNull($sample->approved_at);
    }
}
