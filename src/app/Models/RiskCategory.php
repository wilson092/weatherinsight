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
        'min_score',
        'max_score',
        'color_badge',
        'recommendation',
        'insight',
        'is_active',
    ];

    public static function forScore(int $score): ?self
    {
        return static::where('min_score', '<=', $score)
            ->where(function ($query) use ($score) {
                $query->where('max_score', '>=', $score)
                    ->orWhereNull('max_score');
            })
            ->where('is_active', true)
            ->orderBy('min_score')
            ->first();
    }
}
