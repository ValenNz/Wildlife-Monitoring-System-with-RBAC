<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory; // ← Tambahkan ini
    protected $fillable = [
        'user_id', 'title', 'message', 'type',
        'is_read', 'trigger_id', 'trigger_type'
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
}
