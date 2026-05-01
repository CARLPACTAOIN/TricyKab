<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_user_id',
        'actor_type',
        'actor_name',
        'object_type',
        'object_id',
        'action',
        'previous_state_json',
        'new_state_json',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'previous_state_json' => 'array',
        'new_state_json' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
