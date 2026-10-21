<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\QCSpecificationService;
use App\Models\Sample;
use App\Models\SpectroResult;
use App\Models\TensileTest;
use App\Models\HardnessTest;

class QCSpecificationServiceTest extends TestCase
{
    /**
     * Requirement: Seluruh 9 grade (6 lama + 3 baru) terdaftar.
     */
    public function test_all_nine_grades_are_registered(): void
    {
        $expectedLegacy = ['CF8', 'CF8M', 'SCS13A', 'SCS14A', '1.4308', '1.4408'];
        $expectedNew = ['316 (S31600)', 'SUS316', '1.4401'];
        $expectedAll = array_merge($expectedLegacy, $expectedNew);

        $actual = QCSpecificationService::getGrades();

        $this->assertEquals($expectedAll, $actual);
        foreach ($expectedAll as $grade) {
            $this->assertTrue(QCSpecificationService::isValidGrade($grade));
        }
        $this->assertFalse(QCSpecificationService::isValidGrade('UNKNOWN_GRADE'));
        $this->assertFalse(QCSpecificationService::isValidGrade('304')); // 304 adalah alias, bukan key grade
    }

    /**
     * Requirement 2: Nilai spesifikasi keenam grade lama tidak berubah (identik dengan baseline).
     */
    public function test_baseline_limits_of_all_six_grades_are_strictly_preserved(): void
    {
        // 1. CF8
        $cf8 = QCSpecificationService::getLimitsForGrade('CF8');
        $this->assertSame([null, 0.08], $cf8['chem']['c']);
        $this->assertSame([null, 2.00], $cf8['chem']['si']);
        $this->assertSame([null, 1.50], $cf8['chem']['mn']);
        $this->assertSame([null, 0.04], $cf8['chem']['p']);
        $this->assertSame([null, 0.04], $cf8['chem']['s']);
        $this->assertSame([18.00, 21.00], $cf8['chem']['cr']);
        $this->assertSame([8.00, 11.00], $cf8['chem']['ni']);
        $this->assertSame([null, 0.50], $cf8['chem']['mo']);
        $this->assertSame([null, null], $cf8['chem']['cu']);
        $this->assertSame([205, null], $cf8['mech']['ys_mpa']);
        $this->assertSame([485, null], $cf8['mech']['uts_mpa']);
        $this->assertSame([35, null], $cf8['mech']['elong_pct']);
        $this->assertSame([null, 183], $cf8['mech']['hb']);
        $this->assertSame('ASTM A351', QCSpecificationService::getStandardForGrade('CF8'));

        // 2. CF8M
        $cf8m = QCSpecificationService::getLimitsForGrade('CF8M');
        $this->assertSame([null, 0.08], $cf8m['chem']['c']);
        $this->assertSame([null, 1.50], $cf8m['chem']['si']);
        $this->assertSame([null, 1.50], $cf8m['chem']['mn']);
        $this->assertSame([null, 0.04], $cf8m['chem']['p']);
        $this->assertSame([null, 0.04], $cf8m['chem']['s']);
        $this->assertSame([18.00, 21.00], $cf8m['chem']['cr']);
        $this->assertSame([9.00, 11.00], $cf8m['chem']['ni']);
        $this->assertSame([2.00, 3.00], $cf8m['chem']['mo']);
        $this->assertSame([null, null], $cf8m['chem']['cu']);
        $this->assertSame([205, null], $cf8m['mech']['ys_mpa']);
        $this->assertSame([485, null], $cf8m['mech']['uts_mpa']);
        $this->assertSame([30, null], $cf8m['mech']['elong_pct']);
        $this->assertSame([null, 183], $cf8m['mech']['hb']);
        $this->assertSame('ASTM A351', QCSpecificationService::getStandardForGrade('CF8M'));

        // 3. SCS13A
        $scs13a = QCSpecificationService::getLimitsForGrade('SCS13A');
        $this->assertSame([null, 0.08], $scs13a['chem']['c']);
        $this->assertSame([null, 2.00], $scs13a['chem']['si']);
        $this->assertSame([null, 1.50], $scs13a['chem']['mn']);
        $this->assertSame([null, 0.04], $scs13a['chem']['p']);
        $this->assertSame([null, 0.04], $scs13a['chem']['s']);
        $this->assertSame([18.00, 21.00], $scs13a['chem']['cr']);
        $this->assertSame([8.00, 11.00], $scs13a['chem']['ni']);
        $this->assertArrayNotHasKey('mo', $scs13a['chem']);
        $this->assertSame([205, null], $scs13a['mech']['ys_mpa']);
        $this->assertSame([480, null], $scs13a['mech']['uts_mpa']);
        $this->assertSame([33, null], $scs13a['mech']['elong_pct']);
        $this->assertSame([null, 183], $scs13a['mech']['hb']);
        $this->assertSame('JIS G 5121', QCSpecificationService::getStandardForGrade('SCS13A'));

        // 4. SCS14A
        $scs14a = QCSpecificationService::getLimitsForGrade('SCS14A');
        $this->assertSame([null, 0.08], $scs14a['chem']['c']);
        $this->assertSame([null, 1.50], $scs14a['chem']['si']);
        $this->assertSame([null, 1.50], $scs14a['chem']['mn']);
        $this->assertSame([null, 0.04], $scs14a['chem']['p']);
        $this->assertSame([null, 0.04], $scs14a['chem']['s']);
        $this->assertSame([18.00, 21.00], $scs14a['chem']['cr']);
        $this->assertSame([9.00, 12.00], $scs14a['chem']['ni']);
        $this->assertSame([2.00, 3.00], $scs14a['chem']['mo']);
        $this->assertSame([205, null], $scs14a['mech']['ys_mpa']);
        $this->assertSame([480, null], $scs14a['mech']['uts_mpa']);
        $this->assertSame([33, null], $scs14a['mech']['elong_pct']);
        $this->assertSame([null, 183], $scs14a['mech']['hb']);
        $this->assertSame('JIS G 5121', QCSpecificationService::getStandardForGrade('SCS14A'));

        // 5. 1.4308
        $g14308 = QCSpecificationService::getLimitsForGrade('1.4308');
        $this->assertSame([null, 0.07], $g14308['chem']['c']);
        $this->assertSame([null, 1.50], $g14308['chem']['si']);
        $this->assertSame([null, 1.50], $g14308['chem']['mn']);
        $this->assertSame([null, 0.04], $g14308['chem']['p']);
        $this->assertSame([null, 0.03], $g14308['chem']['s']);
        $this->assertSame([18.00, 20.00], $g14308['chem']['cr']);
        $this->assertSame([8.00, 11.00], $g14308['chem']['ni']);
        $this->assertSame([null, 0.50], $g14308['chem']['cu']);
        $this->assertArrayNotHasKey('mo', $g14308['chem']);
        $this->assertSame([200, null], $g14308['mech']['ys_mpa']);
        $this->assertSame([440, 640], $g14308['mech']['uts_mpa']);
        $this->assertSame([30, null], $g14308['mech']['elong_pct']);
        $this->assertSame([null, 183], $g14308['mech']['hb']);
        $this->assertSame('BS EN 10213', QCSpecificationService::getStandardForGrade('1.4308'));

        // 6. 1.4408
        $g14408 = QCSpecificationService::getLimitsForGrade('1.4408');
        $this->assertSame([null, 0.07], $g14408['chem']['c']);
        $this->assertSame([null, 1.50], $g14408['chem']['si']);
        $this->assertSame([null, 1.50], $g14408['chem']['mn']);
        $this->assertSame([null, 0.04], $g14408['chem']['p']);
        $this->assertSame([null, 0.03], $g14408['chem']['s']);
        $this->assertSame([18.00, 20.00], $g14408['chem']['cr']);
        $this->assertSame([9.00, 12.00], $g14408['chem']['ni']);
        $this->assertSame([2.00, 2.50], $g14408['chem']['mo']);
        $this->assertSame([null, 0.50], $g14408['chem']['cu']);
        $this->assertSame([210, null], $g14408['mech']['ys_mpa']);
        $this->assertSame([440, 640], $g14408['mech']['uts_mpa']);
        $this->assertSame([30, null], $g14408['mech']['elong_pct']);
        $this->assertSame([null, 183], $g14408['mech']['hb']);
        $this->assertSame('BS EN 10213', QCSpecificationService::getStandardForGrade('1.4408'));
    }

    /**
     * Requirement 5, 6, 7: Evaluasi batas minimum, maksimum, nilai di luar batas, nilai kosong, dan tanpa batas.
     */
    public function test_judge_evaluation_logic_exhaustively(): void
    {
        // Parameter dengan batas rentang [min, max], misal Cr: [18.00, 21.00]
        $this->assertSame('FAIL', QCSpecificationService::judge(17.99, 18.00, 21.00), 'Nilai di bawah minimum harus FAIL');
        $this->assertSame('PASS', QCSpecificationService::judge(18.00, 18.00, 21.00), 'Nilai tepat pada minimum harus PASS');
        $this->assertSame('PASS', QCSpecificationService::judge(19.50, 18.00, 21.00), 'Nilai di antara minimum dan maksimum harus PASS');
        $this->assertSame('PASS', QCSpecificationService::judge(21.00, 18.00, 21.00), 'Nilai tepat pada maksimum harus PASS');
        $this->assertSame('FAIL', QCSpecificationService::judge(21.01, 18.00, 21.00), 'Nilai di atas maksimum harus FAIL');

        // Parameter dengan batas maks saja [null, 0.08], misal C: [null, 0.08]
        $this->assertSame('PASS', QCSpecificationService::judge(0.02, null, 0.08));
        $this->assertSame('PASS', QCSpecificationService::judge(0.08, null, 0.08));
        $this->assertSame('FAIL', QCSpecificationService::judge(0.081, null, 0.08));

        // Parameter dengan batas min saja [205, null], misal YS: [205, null]
        $this->assertSame('FAIL', QCSpecificationService::judge(204.9, 205, null));
        $this->assertSame('PASS', QCSpecificationService::judge(205.0, 205, null));
        $this->assertSame('PASS', QCSpecificationService::judge(350.0, 205, null));

        // Parameter tanpa batas min dan max [null, null], misal Cu pada CF8
        $this->assertSame('—', QCSpecificationService::judge(0.25, null, null));
        $this->assertSame('—', QCSpecificationService::judge(null, null, null));

        // Nilai kosong / null
        $this->assertSame('—', QCSpecificationService::judge(null, 18.00, 21.00));
        $this->assertSame('—', QCSpecificationService::judge('', 18.00, 21.00));
    }

    /**
     * Requirement 8: Evaluator mengevaluasi sample dengan konsisten (chemical & mechanical).
     */
    public function test_evaluate_sample_structure_and_behavior(): void
    {
        $sample = new Sample(['grade' => 'CF8']);
        $spectro = new SpectroResult([
            'c'  => 0.035, // PASS (max 0.08)
            'cr' => 19.50, // PASS (18 - 21)
            'ni' => 9.20,  // PASS (8 - 11)
            'mo' => 0.60,  // FAIL (max 0.50)
        ]);
        $tensile = new TensileTest([
            'ys_mpa'    => 250, // PASS (min 205)
            'uts_mpa'   => 520, // PASS (min 485)
            'elong_pct' => 40,  // PASS (min 35)
        ]);
        $hardness = new HardnessTest([
            'avg_value' => 160, // PASS (max 183)
        ]);

        $sample->setRelation('spectroResult', $spectro);
        $sample->setRelation('tensileTest', $tensile);
        $sample->setRelation('hardnessTest', $hardness);

        $eval = QCSpecificationService::evaluateSample($sample);

        $this->assertSame('PASS', $eval['chemical']['c']['status']);
        $this->assertSame('PASS', $eval['chemical']['cr']['status']);
        $this->assertSame('FAIL', $eval['chemical']['mo']['status']);
        $this->assertTrue($eval['has_failures']);
        $this->assertFalse($eval['overall_pass']);

        $this->assertSame('PASS', $eval['mechanical']['ys_mpa']['status']);
        $this->assertSame('PASS', $eval['mechanical']['uts_mpa']['status']);
        $this->assertSame('PASS', $eval['mechanical']['hb']['status']);
    }

    /**
     * Requirement: Verifikasi spesifikasi kimia dan mekanik 3 grade baru.
     */
    public function test_three_new_grades_specifications_and_mappings(): void
    {
        // 1. 316 (S31600) - ASTM A959 (Mech mapped to CF8M)
        $g316 = QCSpecificationService::getLimitsForGrade('316 (S31600)');
        $this->assertSame('ASTM A959', QCSpecificationService::getStandardForGrade('316 (S31600)'));
        $this->assertSame([null, 0.08], $g316['chem']['c']);
        $this->assertSame([null, 1.00], $g316['chem']['si']);
        $this->assertSame([null, 2.00], $g316['chem']['mn']);
        $this->assertSame([null, 0.045], $g316['chem']['p']);
        $this->assertSame([null, 0.030], $g316['chem']['s']);
        $this->assertSame([16.00, 18.00], $g316['chem']['cr']);
        $this->assertSame([10.00, 14.00], $g316['chem']['ni']);
        $this->assertSame([2.00, 3.00], $g316['chem']['mo']);
        $this->assertArrayNotHasKey('n', $g316['chem']);
        $this->assertArrayNotHasKey('ti', $g316['chem']);
        $this->assertArrayNotHasKey('nb', $g316['chem']);
        $this->assertSame([205, null], $g316['mech']['ys_mpa']);
        $this->assertSame([485, null], $g316['mech']['uts_mpa']);
        $this->assertSame([30, null], $g316['mech']['elong_pct']);
        $this->assertSame([null, 183], $g316['mech']['hb']);

        // 2. SUS316 - JIS G4403 (Mech mapped to SCS14A)
        $sus316 = QCSpecificationService::getLimitsForGrade('SUS316');
        $this->assertSame('JIS G4403', QCSpecificationService::getStandardForGrade('SUS316'));
        $this->assertSame([null, 0.08], $sus316['chem']['c']);
        $this->assertSame([null, 1.00], $sus316['chem']['si']);
        $this->assertSame([null, 2.00], $sus316['chem']['mn']);
        $this->assertSame([null, 0.045], $sus316['chem']['p']);
        $this->assertSame([null, 0.030], $sus316['chem']['s']);
        $this->assertSame([16.00, 18.00], $sus316['chem']['cr']);
        $this->assertSame([10.00, 14.00], $sus316['chem']['ni']);
        $this->assertSame([2.00, 3.00], $sus316['chem']['mo']);
        $this->assertArrayNotHasKey('n', $sus316['chem']);
        $this->assertArrayNotHasKey('ti', $sus316['chem']);
        $this->assertArrayNotHasKey('nb', $sus316['chem']);
        $this->assertSame([205, null], $sus316['mech']['ys_mpa']);
        $this->assertSame([480, null], $sus316['mech']['uts_mpa']);
        $this->assertSame([33, null], $sus316['mech']['elong_pct']);
        $this->assertSame([null, 183], $sus316['mech']['hb']);

        // 3. 1.4401 - BS EN 10088-1 (Mech mapped to 1.4408)
        $g14401 = QCSpecificationService::getLimitsForGrade('1.4401');
        $this->assertSame('BS EN 10088-1', QCSpecificationService::getStandardForGrade('1.4401'));
        $this->assertSame([null, 0.07], $g14401['chem']['c']);
        $this->assertSame([null, 1.00], $g14401['chem']['si']);
        $this->assertSame([null, 2.00], $g14401['chem']['mn']);
        $this->assertSame([null, 0.045], $g14401['chem']['p']);
        $this->assertSame([null, 0.015], $g14401['chem']['s']);
        $this->assertSame([16.50, 18.50], $g14401['chem']['cr']);
        $this->assertSame([10.00, 13.00], $g14401['chem']['ni']);
        $this->assertSame([2.00, 2.50], $g14401['chem']['mo']);
        $this->assertArrayNotHasKey('n', $g14401['chem']);
        $this->assertArrayNotHasKey('ti', $g14401['chem']);
        $this->assertArrayNotHasKey('nb', $g14401['chem']);
        $this->assertSame([210, null], $g14401['mech']['ys_mpa']);
        $this->assertSame([440, 640], $g14401['mech']['uts_mpa']);
        $this->assertSame([30, null], $g14401['mech']['elong_pct']);
        $this->assertSame([null, 183], $g14401['mech']['hb']);
    }

    /**
     * Requirement: Helper methods return valid definitions.
     */
    public function test_helper_methods_and_grouped_materials(): void
    {
        $order = QCSpecificationService::getChemDisplayOrder();
        $this->assertCount(12, $order);
        $this->assertSame(['c', 'si', 'mn', 'p', 's', 'cr', 'ni', 'mo', 'cu', 'co', 'al', 'v'], $order);

        $this->assertSame('Carbon (C)', QCSpecificationService::getChemLabel('c'));
        $this->assertSame('Yield Strength', QCSpecificationService::getMechLabel('ys_mpa'));
        $this->assertSame('MPa', QCSpecificationService::getMechUnit('ys_mpa'));

        $grouped = QCSpecificationService::getMaterialsGroupedByStandard();
        $this->assertArrayHasKey('ASTM A351', $grouped);
        $this->assertArrayHasKey('ASTM A959', $grouped);
        $this->assertArrayHasKey('JIS G 5121', $grouped);
        $this->assertArrayHasKey('JIS G4403', $grouped);
        $this->assertArrayHasKey('BS EN 10213', $grouped);
        $this->assertArrayHasKey('BS EN 10088-1', $grouped);
    }
}
