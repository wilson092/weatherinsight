<?php

namespace App\Services\Weather;

use App\Models\RiskCategory;
use App\Models\WeatherHistory;
use App\Models\WeatherRule;

class WeatherRuleEngineService
{
    public function analyze(WeatherHistory $weather): array
    {
        $analysis = $this->calculateRiskScore($weather);
        $score = $analysis['score'];
        $triggeredRules = $analysis['triggered_rules'];

        $riskCategory = RiskCategory::where('min_score', '<=', $score)
            ->where(function ($query) use ($score) {
                $query->where('max_score', '>=', $score)
                    ->orWhereNull('max_score');
            })
            ->where('is_active', true)
            ->first();

        if ($riskCategory) {
            $risk = $riskCategory->name;
            $recommendation = $riskCategory->recommendation ?? 'No specific recommendation available.';
            $insight = $riskCategory->insight ?? 'No specific insight available.';
        } else {
            $risk = 'N/A';
            $recommendation = 'No risk category found for the calculated score.';
            $insight = 'No risk category found for the calculated score.';
        }

        return [
            'risk' => $risk,
            'recommendation' => $recommendation,
            'insight' => $insight,
            'score' => $score,
            'triggered_rules' => $triggeredRules,
        ];
    }

    public function calculateRiskScore(WeatherHistory $weather): array
    {
        $score = 0;
        $triggeredRules = [];
        $rules = WeatherRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            $value = $weather->{$rule->rule_type};
            if ($this->isRuleMet($value, $rule)) {
                $score += $rule->score_weight;
                $triggeredRules[] = $rule;
            }
        }

        return [
            'score' => $score,
            'triggered_rules' => $triggeredRules,
        ];
    }

    private function isRuleMet($value, WeatherRule $rule): bool
    {
        if (is_null($value)) {
            return false;
        }

        switch ($rule->operator) {
            case '>':
                return $value > $rule->threshold_value;
            case '<':
                return $value < $rule->threshold_value;
            case '=':
                return $value == $rule->threshold_value;
            case 'between':
                return $value >= $rule->min_value && $value <= $rule->max_value;
            default:
                return false;
        }
    }
}
