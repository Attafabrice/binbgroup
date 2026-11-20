<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'read_at' => 'datetime',
        'handled_at' => 'datetime'
    ];

    public function handler(): BelongsTo { 
        return $this->belongsTo(User::class, 'handled_by'); 
    }
}