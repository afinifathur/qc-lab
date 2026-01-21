<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpectroResult extends Model
{
    use SoftDeletes;

    protected $table = 'spectro_results';

    protected $fillable = [
        'sample_id',
        // unsur kimia (semua nullable)
        'c','si','mn','p','s','cr','ni','mo','cu','co','al','v','n',
    ];

    protected $casts = [
        'c'  => 'float', 'si' => 'float', 'mn' => 'float',
        'p'  => 'float', 's'  => 'float', 'cr' => 'float',
        'ni' => 'float', 'mo' => 'float', 'cu' => 'float',
        'co' => 'float', 'al' => 'float', 'v'  => 'float',
        'n'  => 'float',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}
