<?php

namespace App\Models;

use App\Models\Animal;
use App\Models\Device;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackingData extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'device_id', 'latitude', 'longitude', 'altitude',
        'speed', 'heading', 'accuracy', 'recorded_at', 'received_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // ✅ Relasi ke Device
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    // ✅ Relasi ke Animal via Device (hasOneThrough)
    public function animal()
    {
        return $this->hasOneThrough(Animal::class, Device::class);

    }

     public function environmentalData()
    {
        return $this->hasOne(EnvironmentalData::class, 'device_id', 'device_id');
    }
}
