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
        'suhu_minimal',
        'suhu_maksimal',
        'color_badge',
        'recommendation',
        'insight',
        'is_active',
    ];

    public static function forScore(int $score): ?self
    {
        return static::where('suhu_minimal', '<=', $score)
            ->where(function ($query) use ($score) {
                $query->where('suhu_maksimal', '>=', $score)
                    ->orWhereNull('suhu_maksimal');
            })
            ->where('is_active', true)
            ->orderBy('suhu_minimal')
            ->first();
    }
}
