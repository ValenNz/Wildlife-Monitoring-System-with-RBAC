<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    use HasFactory;
protected $primaryKey = 'id'; // atau 'animal_id' jika memang itu primary key
    protected $fillable = [
        'name', 'species', 'sex', 'birth_date', 'tag_id', 'notes','species_id','created_by' // Jika ada kolom ini
    ];

    public function device()
    {
        return $this->hasOne(Device::class, 'animal_id', 'animal_id');
    }

    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id', 'species_id');
    }

    public function riskZones()
    {
        return $this->belongsToMany(RiskZone::class, 'animal_risk_zones')
                    ->withPivot('entered_at', 'exited_at')
                    ->withTimestamps();
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'trigger');
    }

    public function trackingData()
    {
        return $this->hasManyThrough(TrackingData::class, Device::class);
    }
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

}

