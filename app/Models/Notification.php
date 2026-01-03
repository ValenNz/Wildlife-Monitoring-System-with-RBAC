<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory; // ← Tambahkan ini
    protected $fillable = [
 'user_id', // Penerima
        'trigger_id', // ID entitas pemicu (animal, device, zone, dll)
        'trigger_type', // Jenis entitas pemicu (App\Models\Animal, dll)
        'type', // 'risk_zone_entry', 'device_alert'
        'message',
        'is_read',
        'read_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic-like (manual karena tidak pakai Laravel's morph)
    // Contoh: dapatkan entitas pemicu jika tipe diketahui
    public function getTriggerEntity()
    {
        if ($this->trigger_type === 'tracking' && $this->trigger_id) {
            return TrackingData::find($this->trigger_id);
        }
        return null;
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'related_animal_id');
    }

    public function zone()
    {
        return $this->belongsTo(RiskZone::class, 'related_zone_id');
    }

    public function device()
    {
        return $this->belongsTo(TrackingData::class, 'related_device_id');
    }
}
