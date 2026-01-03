<?php
// app/Models/Incident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'user_id', // Pelapor
        'title',
        'severity',
        'status',
        'animal_id', // Satwa terlibat
        'assigned_to', // Petugas yang ditugaskan
        'location', // Koordinat lokasi insiden
        'latitude',
        'longitude',
        'resolved_at',
        'resolution_notes',
        'created_at',
        'updated_at'
    ];

    // Relasi ke User (pelapor)
    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User (penugasan)
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relasi ke Animal
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    // Relasi ke RiskZone (opsional, jika insiden terjadi di zona tertentu)
    public function riskZone()
    {
        return $this->belongsTo(RiskZone::class, 'risk_zone_id');
    }
}
