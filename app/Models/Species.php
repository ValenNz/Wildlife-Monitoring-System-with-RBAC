<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;

    protected $fillable = [
        'common_name',
        'scientific_name',
        'conservation_status',
    ];

    // Relasi ke Animals
    public function animals()
    {
        return $this->hasMany(Animal::class);
    }
}
