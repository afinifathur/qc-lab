<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'stamp_id','user_id','user_name','user_role','action',
        'entity_type','entity_id','route','method','ip','user_agent','meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
