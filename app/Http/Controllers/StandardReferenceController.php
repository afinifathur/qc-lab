<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QCSpecificationService;

class StandardReferenceController extends Controller
{
    /**
     * Tampilkan katalog referensi standard & spesifikasi material secara read-only.
     * Sumber data tunggal berasal dari QCSpecificationService (config/qc.php).
     */
    public function index(Request $request)
    {
        $groupedStandards = QCSpecificationService::getMaterialsGroupedByStandard();
        $chemOrder = QCSpecificationService::getChemDisplayOrder();
        $totalGrades = count(QCSpecificationService::getGrades());

        return view('standards.index', [
            'groupedStandards' => $groupedStandards,
            'chemOrder'        => $chemOrder,
            'totalGrades'      => $totalGrades,
        ]);
    }
}
