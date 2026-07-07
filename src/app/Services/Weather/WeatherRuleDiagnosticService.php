<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;
use App\Models\WeatherRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WeatherRuleDiagnosticService
{
    public function __construct(
        private readonly WeatherRuleEngineService $ruleEngine,
    ) {}

    /**
     * Comprehensive diagnostic report for weather rule evaluation
     * Returns an Array with detailed information about rule matching
     */
    public function diagnose(WeatherHistory $weather): array
    {
        $allActiveRules = WeatherRule::where('is_active', true)->get();
        $analysis = $this->ruleEngine->analyze($weather);
        
        $diagnosticReport = [
            'weather_data' => [
                'city' => $weather->city,
                'temperature' => $weather->temperature,
                'humidity' => $weather->humidity,
                'pressure' => $weather->pressure,
                'wind_speed' => $weather->wind_speed,
                'recorded_at' => $weather->recorded_at?->toIso8601String(),
            ],
            'rules_in_database' => [],
            'rule_evaluation_detail' => [],
            'triggered_rules' => collect($analysis['triggered_rules'])->map(function ($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'rule_type' => $rule->rule_type,
                    'operator' => $rule->operator,
                    'threshold' => $rule->threshold_value,
                    'min_value' => $rule->min_value,
                    'max_value' => $rule->max_value,
                    'score_weight' => $rule->score_weight,
                ];
            })->toArray(),
            'analysis_summary' => [
                'total_score' => $analysis['score'],
                'risk_level' => $analysis['risk_level'],
                'risk_category_name' => $analysis['risk_category']?->name ?? 'N/A',
                'triggered_count' => count($analysis['triggered_rules']),
            ],
        ];

        // List all rules with evaluation detail
        $rulesByType = $allActiveRules->groupBy('rule_type');
        foreach ($rulesByType as $type => $rules) {
            $actualValue = $weather->{$type};
            
            $diagnosticReport['rules_in_database'][$type] = [
                'actual_value' => $actualValue,
                'rules' => $rules->map(function ($rule) use ($actualValue) {
                    $isMet = $this->isRuleMet($actualValue, $rule);
                    return [
                        'id' => $rule->id,
                        'name' => $rule->name,
                        'operator' => $rule->operator,
                        'threshold' => $rule->threshold_value,
                        'min_value' => $rule->min_value,
                        'max_value' => $rule->max_value,
                        'is_met' => $isMet,
                        'evaluation' => $this->formatEvaluation($actualValue, $rule),
                    ];
                })->toArray(),
            ];
        }

        // Log the complete diagnostic
        Log::info('Weather Rule Diagnostic Report', $diagnosticReport);

        return $diagnosticReport;
    }

    /**
     * Verify that a specific weather condition triggers the expected rule
     */
    public function verifyRuleMatch(
        float $value,
        string $ruleType,
        string $expectedRuleName
    ): bool {
        $rule = WeatherRule::where('rule_type', $ruleType)
            ->where('name', $expectedRuleName)
            ->where('is_active', true)
            ->first();

        if (!$rule) {
            Log::warning('Rule not found', [
                'type' => $ruleType,
                'name' => $expectedRuleName,
            ]);
            return false;
        }

        $isMet = $this->isRuleMet($value, $rule);
        
        Log::info('Rule verification', [
            'rule' => $expectedRuleName,
            'type' => $ruleType,
            'value' => $value,
            'operator' => $rule->operator,
            'threshold' => $rule->threshold_value,
            'matches' => $isMet,
        ]);

        return $isMet;
    }

    private function isRuleMet($value, $rule): bool
    {
        if (is_null($value)) {
            return false;
        }

        return match ($rule->operator) {
            '>' => $value > $rule->threshold_value,
            '>=' => $value >= $rule->threshold_value,
            '<' => $value < $rule->threshold_value,
            '<=' => $value <= $rule->threshold_value,
            '=' => $value == $rule->threshold_value,
            'between' => $value >= $rule->min_value && $value <= $rule->max_value,
            default => false,
        };
    }

    private function formatEvaluation($value, $rule): string
    {
        if ($rule->operator === 'between') {
            return "{$value} between {$rule->min_value} and {$rule->max_value}";
        }

        return "{$value} {$rule->operator} {$rule->threshold_value}";
    }
}
