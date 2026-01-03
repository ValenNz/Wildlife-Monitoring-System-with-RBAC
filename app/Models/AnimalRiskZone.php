<?php
// app/Models/AnimalRiskZone.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalRiskZone extends Model
{
    protected $table = 'animal_risk_zones'; // Nama tabel pivot

    protected $fillable = [
        'animal_id',
        'risk_zone_id',
        'entered_at',
        'exited_at',
        'duration_minutes',
        'is_currently_in_zone'
    ];

    // Relasi ke Animal
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    // Relasi ke RiskZone
    public function riskZone()
    {
        return $this->belongsTo(RiskZone::class);
    }
}
