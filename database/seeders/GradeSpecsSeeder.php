<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSpecsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // 304 (umum)
            ['304','ASTM A351','C', null, 0.080, '%wt'],
            ['304','ASTM A351','Si', null, 1.000, '%wt'],
            ['304','ASTM A351','Mn', null, 2.000, '%wt'],
            ['304','ASTM A351','P',  null, 0.045, '%wt'],
            ['304','ASTM A351','S',  null, 0.030, '%wt'],
            ['304','ASTM A351','Cr', 18.000, 20.000, '%wt'],
            ['304','ASTM A351','Ni', 8.000,  10.500, '%wt'],
            ['304','ASTM A351','N',  null, 0.100, '%wt'],
            ['304','ASTM A351','YS', 205.00, null, 'MPa'],
            ['304','ASTM A351','UTS',515.00, null, 'MPa'],
            ['304','ASTM A351','Elong',40.00, null, '%'],
            ['304','ASTM A351','HB', null, 187.00, 'HB'],

            // 316 (umum)
            ['316','ASTM A351','C', null, 0.080, '%wt'],
            ['316','ASTM A351','Si', null, 1.000, '%wt'],
            ['316','ASTM A351','Mn', null, 2.000, '%wt'],
            ['316','ASTM A351','P',  null, 0.045, '%wt'],
            ['316','ASTM A351','S',  null, 0.030, '%wt'],
            ['316','ASTM A351','Cr', 16.000, 18.000, '%wt'],
            ['316','ASTM A351','Ni', 10.000, 14.000, '%wt'],
            ['316','ASTM A351','Mo', 2.000,  3.000,  '%wt'],
            ['316','ASTM A351','N',  null, 0.100, '%wt'],
            ['316','ASTM A351','YS', 205.00, null, 'MPa'],
            ['316','ASTM A351','UTS',515.00, null, 'MPa'],
            ['316','ASTM A351','Elong',40.00, null, '%'],
            ['316','ASTM A351','HB', null, 187.00, 'HB'],
        ];

        foreach ($rows as $r) {
            DB::table('grade_specs')->updateOrInsert(
                ['grade'=>$r[0], 'standard'=>$r[1], 'property_key'=>$r[2]],
                ['min_val'=>$r[3], 'max_val'=>$r[4], 'unit'=>$r[5], 'updated_at'=>now(), 'created_at'=>now()]
            );
        }
    }
}
