<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiskZone extends Model
{
    use HasFactory; // ← Tambahkan ini

    protected $fillable = [
        'name', 'description', 'zone_type', 'polygon'
    ];

    // Polygon disimpan sebagai binary → cast ke WKT/WKB jika perlu
    protected $casts = [
        'polygon' => 'array', // atau custom cast (opsional)
    ];

    // Tidak ada relasi otomatis ke notifikasi
    // Karena notifikasi dipicu event, bukan relasi data statis
}
