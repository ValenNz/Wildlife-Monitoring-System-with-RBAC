<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory; // ← Tambahkan ini

    public $timestamps = false;

    protected $fillable = [
        'level', 'message', 'context'
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];
}
