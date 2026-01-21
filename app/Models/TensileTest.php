<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TensileTest extends Model
{
    use SoftDeletes;

    protected $table = 'tensile_tests';

    protected $fillable = [
        'sample_id',
        'ys_mpa',      // Yield Strength (MPa)
        'uts_mpa',     // Ultimate Tensile Strength (MPa)
        'elong_pct',   // Elongation (%)
    ];

    protected $casts = [
        'ys_mpa'    => 'float',
        'uts_mpa'   => 'float',
        'elong_pct' => 'float',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}
