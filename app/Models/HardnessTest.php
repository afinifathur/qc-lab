<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HardnessTest extends Model
{
    use SoftDeletes;

    protected $table = 'hardness_tests';

    protected $fillable = [
        'sample_id',
        'method',     // contoh: HB
        'avg_value',  // nilai rata-rata
    ];

    protected $casts = [
        'avg_value' => 'float',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}
