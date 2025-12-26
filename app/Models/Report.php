<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory; // ← Tambahkan ini
    protected $fillable = [
        'title', 'content', 'report_type', 'generated_by',
        'generated_at', 'period_start', 'period_end', 'metadata'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'metadata' => 'array',
    ];

    // Relasi
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
