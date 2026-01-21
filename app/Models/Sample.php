<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sample extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_no','heat_no','batch_no','grade','standard','product_type','size_spec',
        'po_no','customer','process','test_date','machine_spektro','machine_tensile',
        'machine_hardness','overall_result','po_customer','status','created_by','approved_by',
        'approved_at','version'
    ];
    protected $casts = ['test_date'=>'date','approved_at'=>'datetime'];

    public function spectroResult(){ return $this->hasOne(SpectroResult::class); }
    public function tensileTest(){ return $this->hasOne(TensileTest::class); }
    public function hardnessTest(){ return $this->hasOne(HardnessTest::class); }
}
