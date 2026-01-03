<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← Tambahkan ini

class Device extends Model
{
        use HasFactory; // ← Tambahkan ini

    protected $fillable = [
        'device_id', 'type', 'status', 'battery_level', 'last_seen', 'animal_id'
    ];

    // Relasi
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function trackingData()
    {
        return $this->hasMany(TrackingData::class);
    }

    // ❌ TIDAK buat relasi ke TrackingData/EnvironmentalData
    // Karena data sangat besar → query langsung via model tersebut
}
