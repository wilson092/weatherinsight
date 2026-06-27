<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'risk_level',
        'min_temperature',
        'max_temperature',
        'color_badge',
        'description',
        'is_active',
    ];
}
