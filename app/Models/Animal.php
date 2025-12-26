<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'species', 'sex', 'birth_date', 'tag_id', 'notes'
    ];

    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
