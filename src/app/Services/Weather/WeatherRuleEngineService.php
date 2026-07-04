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
        $rawScore = $analysis['score'];
        $maxPossibleScore = $analysis['max_score'];
        $triggeredRules = $analysis['triggered_rules'];
        $allRules = $analysis['all_rules']; // Get all rules for context

        // Normalize the score to a 0-100 scale
        $normalizedScore = ($maxPossibleScore > 0) ? ($rawScore / $maxPossibleScore) * 100 : 0;

        $riskCategory = RiskCategory::forScore($normalizedScore);

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
            'score' => round($normalizedScore),
            'max_score' => 100, // The gauge max is now always 100
            'triggered_rules' => $triggeredRules,
            'all_rules' => $allRules, // Pass all rules for detailed display
            'weather_data' => $weather, // Pass the weather data itself
        ];
    }

    public function calculateRiskScore(WeatherHistory $weather): array
    {
        $score = 0;
        $maxScore = 0;
        $triggeredRules = [];
        $allActiveRules = WeatherRule::where('is_active', true)->get();
        $maxScore = $allActiveRules->sum('score_weight');

        $rulesByType = $allActiveRules->groupBy('rule_type');

        foreach ($rulesByType as $type => $rules) {
            $value = $weather->{$type};
            if (is_null($value)) {
                continue;
            }

            $matchedRule = null;
            // For each type, find the first rule that matches.
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
            'max_score' => $maxScore,
            'triggered_rules' => $triggeredRules,
            'all_rules' => $allActiveRules,
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
