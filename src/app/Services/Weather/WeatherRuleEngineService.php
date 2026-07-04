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
        // Get all active rules and group them by type. This allows us to process
        // rules for the same weather parameter (e.g., high pressure vs. low pressure)
        // and ensure only the most relevant one is triggered.
        $rulesByType = WeatherRule::where('is_active', true)->get()->groupBy('rule_type');

        foreach ($rulesByType as $type => $rules) {
            $value = $weather->{$type};
            if (is_null($value)) {
                continue;
            }

            $matchedRule = null;
            // For each type, find the first rule that matches. This prevents duplicate
            // scoring for the same metric (e.g. triggering both "High Pressure" and "Low Pressure").
            foreach ($rules as $rule) {
                if ($this->isRuleMet($value, $rule)) {
                    $matchedRule = $rule;
                    break; // Stop at the first match for this type
                }
            }

            if ($matchedRule) {
                Log::info('Rule triggered', [
                    'rule' => $matchedRule->name,
                    'type' => $type,
                    'value' => $value,
                    'threshold' => $matchedRule->threshold_value,
                    'score_added' => $matchedRule->score_weight,
                ]);
                $score += $matchedRule->score_weight;
                $triggeredRules[] = $matchedRule;
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
