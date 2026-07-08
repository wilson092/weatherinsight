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
        // 1. Composite Score Analysis for "Weather Risk Assessment"
        $assessmentAnalysis = $this->getCompositeScoreAnalysis($weather);

        // 2. Temperature-based Analysis for "Weather Recommendation"
        $recommendationAnalysis = $this->getTemperatureBasedAnalysis($weather);

        return [
            'assessment' => $assessmentAnalysis,
            'recommendation' => $recommendationAnalysis,
        ];
    }

    /**
     * Analyzes weather based on a composite score from all active rules.
     */
    private function getCompositeScoreAnalysis(WeatherHistory $weather): array
    {
        $analysis = $this->calculateRiskScore($weather);
        $rawScore = $analysis['score'];
        $maxPossibleScore = $analysis['max_score'];
        $triggeredRules = $analysis['triggered_rules'];
        $allRules = $analysis['all_rules'];

        $riskCategory = RiskCategory::forScore($rawScore);

        $risk = $riskCategory->name ?? 'N/A';
        $riskLevel = $riskCategory->risk_level ?? 'unknown';
        $recommendation = $riskCategory->recommendation ?? 'No specific recommendation available.';
        $insight = $riskCategory->insight ?? 'No risk category found for the calculated score.';
        
        $displayScore = ($maxPossibleScore > 0) ? round(($rawScore / $maxPossibleScore) * 100) : 0;

        return [
            'risk' => $risk,
            'risk_level' => $riskLevel,
            'risk_category' => $riskCategory,
            'recommendation' => $recommendation,
            'insight' => $insight,
            'score' => $rawScore,
            'display_score' => $displayScore,
            'max_score' => $maxPossibleScore,
            'triggered_rules' => $triggeredRules,
            'all_rules' => $allRules,
            'weather_data' => $weather,
        ];
    }

    /**
     * Analyzes weather based solely on the temperature parameter.
     */
    private function getTemperatureBasedAnalysis(WeatherHistory $weather): array
    {
        $temperature = $weather->temperature;
        
        // This logic is now dedicated to the recommendation component.
        // It uses the RiskCategory table dynamically, interpreting the score range as a temperature range.
        $riskCategory = RiskCategory::forScore($temperature);

        if (!$riskCategory) {
             return [
                'risk' => 'Not Set',
                'risk_level' => 'unknown',
                'recommendation' => 'No risk category is configured for the current temperature.',
                'insight' => 'Please check the Risk Categories in the admin panel to ensure a valid range exists for the current temperature.',
                'weather_data' => $weather,
            ];
        }

        return [
            'risk' => $riskCategory->name,
            'risk_level' => $riskCategory->risk_level,
            'recommendation' => $riskCategory->recommendation,
            'insight' => $riskCategory->insight,
            'weather_data' => $weather,
        ];
    }

    /**
     * Calculates the composite risk score from all active weather rules.
     */
    public function calculateRiskScore(WeatherHistory $weather): array
    {
        $score = 0;
        $maxScore = 0;
        $triggeredRules = [];
        // Explicitly order by ID to ensure consistent ordering
        $allActiveRules = WeatherRule::where('is_active', true)
            ->orderBy('id')
            ->get();
        $maxScore = $allActiveRules->sum('score_weight');

        $rulesByType = $allActiveRules->groupBy('rule_type');

        // Map rule types to the correct WeatherHistory model properties
        $propertyMap = [
            'temperature' => 'temperature',
            'humidity' => 'humidity',
            'pressure' => 'pressure',
            'wind' => 'wind_speed',
            // Add other mappings here if new rule types are created
        ];

        // Debug: Log all weather attributes
        Log::debug('weather_analysis_start', [
            'city' => $weather->city ?? 'unknown',
            'temperature' => $weather->temperature,
            'humidity' => $weather->humidity,
            'pressure' => $weather->pressure,
            'wind_speed' => $weather->wind_speed,
            'total_rules' => count($allActiveRules),
            'rule_types' => $rulesByType->keys()->toArray(),
        ]);

        foreach ($rulesByType as $type => $rules) {
            // Use the property map to get the correct attribute name
            $property = $propertyMap[$type] ?? null;
            if (!$property) {
                Log::warning("No property mapping found for rule type: {$type}");
                continue;
            }
            
            $value = $weather->{$property};
            
            Log::debug("checking_type_{$type}", [
                'property_used' => $property,
                'current_value' => $value,
                'is_null' => is_null($value),
                'rules_for_type' => $rules->count(),
            ]);

            if (is_null($value)) {
                continue;
            }

            // Evaluate all rules for the type, not just the first one.
            foreach ($rules as $ruleIndex => $rule) {
                $isMet = $this->isRuleMet($value, $rule);
                
                Log::debug("rule_evaluation", [
                    'type' => $type,
                    'rule_index' => $ruleIndex,
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'rule_operator' => $rule->operator,
                    'rule_threshold' => $rule->threshold_value,
                    'actual_value' => $value,
                    'is_met' => $isMet,
                ]);

                if ($isMet) {
                    $score += $rule->score_weight;
                    $triggeredRules[] = $rule;
                    Log::info('Rule triggered', [
                        'rule' => $rule->name,
                        'type' => $type,
                        'value' => $value,
                        'threshold' => $rule->threshold_value,
                        'operator' => $rule->operator,
                        'score_added' => $rule->score_weight,
                    ]);
                    // Removed the 'break' to allow multiple rules of the same type to be triggered
                }
            }
        }

        Log::debug('analysis_complete', [
            'final_score' => $score,
            'max_score' => $maxScore,
            'triggered_rules_count' => count($triggeredRules),
            'triggered_rule_names' => collect($triggeredRules)->pluck('name')->toArray(),
        ]);

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'triggered_rules' => $triggeredRules,
            'all_rules' => $allActiveRules,
        ];
    }

    /**
     * Checks if a given value meets the condition of a specific rule.
     */
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