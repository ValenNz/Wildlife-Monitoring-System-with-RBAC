<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskZone extends Model
{
    use HasFactory; // ← Tambahkan ini

    protected $fillable = [
        'name', 'description', 'zone_type', 'polygon',         'created_by' // FK ke users (jika ada)
    ];

    // Polygon disimpan sebagai binary → cast ke WKT/WKB jika perlu
    protected $casts = [
        'polygon' => 'array', // atau custom cast (opsional)
    ];

    public function animals()
    {
        return $this->belongsToMany(Animal::class, 'animal_risk_zones')
                    ->withPivot('entered_at', 'exited_at')
                    ->withTimestamps();
    }
    // Tidak ada relasi otomatis ke notifikasi
    // Karena notifikasi dipicu event, bukan relasi data statis

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'trigger');
    }

    // Relasi ke User (pembuat zona)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    

}
