<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportNoService
{
    public static function next(): string
    {
        $now = now();
        $prefix = 'QC/'.$now->format('Y').'/'.$now->format('m').'/';
        $max = DB::table('samples')->where('report_no', 'like', $prefix.'%')->max('report_no');
        $seq = 1;
        if ($max) {
            $seq = (int)substr($max, strrpos($max, '/')+1) + 1;
        }
        return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
    }
}
