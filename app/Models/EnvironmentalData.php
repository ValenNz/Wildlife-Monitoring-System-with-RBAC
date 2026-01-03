<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← Tambahkan ini

class EnvironmentalData extends Model
{
    use HasFactory; // ← Tambahkan ini

    public $timestamps = false;

    protected $fillable = [
        'device_id', 'temperature', 'humidity', 'pressure',
        'light_level', 'recorded_at', 'received_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // ❌ Tidak ada relasi ke Device (alasan sama seperti TrackingData)
// Relasi ke Device
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    // Relasi ke Animal (via device)
    public function animal()
    {
        return $this->hasOneThrough(Animal::class, Device::class);
    }
    
}
