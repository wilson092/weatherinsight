<?php

namespace App\Services\Weather;

use App\Models\RiskCategory;
use App\Models\WeatherHistory;
use App\Models\WeatherRule;
use Illuminate\Support\Facades\Log;

class WeatherRuleEngineService
{
    public function analyze(WeatherHistory $weather): array
    {
        $analysis = $this->calculateRiskScore($weather);
        $score = $analysis['score'];
        $triggeredRules = $analysis['triggered_rules'];

        $riskCategory = RiskCategory::forScore($score);

        if ($riskCategory) {
            $risk = $riskCategory->name;
            $riskLevel = $riskCategory->risk_level;
            $recommendation = $riskCategory->recommendation ?? 'No specific recommendation available.';
            $insight = $riskCategory->insight ?? 'No specific insight available.';
        } else {
            $risk = 'N/A';
            $riskLevel = 'unknown';
            $recommendation = 'No risk category found for the calculated score.';
            $insight = 'No risk category found for the calculated score.';
        }

        return [
            'risk' => $risk,
            'risk_level' => $riskLevel,
            'risk_category' => $riskCategory,
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
            $matched = $this->isRuleMet($value, $rule);

            Log::info([
                'rule' => $rule->name,
                'operator' => $rule->operator,
                'threshold' => $rule->threshold_value,
                'actual' => $value,
                'matched' => $matched,
            ]);

            if ($matched) {
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
            case '>=':
                return $value >= $rule->threshold_value;
            case '<':
                return $value < $rule->threshold_value;
            case '<=':
                return $value <= $rule->threshold_value;
            case '=':
                return $value == $rule->threshold_value;
            case 'between':
                return $value >= $rule->min_value && $value <= $rule->max_value;
            default:
                return false;
        }
    }
}
