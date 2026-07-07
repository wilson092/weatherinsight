<?php

namespace App\Console\Commands;

use App\Models\WeatherHistory;
use App\Services\Weather\WeatherRuleDiagnosticService;
use Illuminate\Console\Command;

class DiagnoseWeatherRules extends Command
{
    protected $signature = 'weather:diagnose {--city=New York} {--temperature= } {--humidity= } {--pressure= } {--wind-speed= }';
    protected $description = 'Diagnose weather rules matching for a specific city or weather scenario';

    public function handle(WeatherRuleDiagnosticService $diagnosticService): int
    {
        $city = $this->option('city');

        // If specific weather values provided, create a temporary weather record
        if ($this->option('temperature') !== null) {
            $weather = new WeatherHistory([
                'city' => $city,
                'temperature' => (float)$this->option('temperature'),
                'humidity' => (float)($this->option('humidity') ?? 70),
                'pressure' => (float)($this->option('pressure') ?? 1010),
                'wind_speed' => (float)($this->option('wind-speed') ?? 5),
                'weather_main' => 'Test',
                'weather_description' => 'Diagnostic test',
                'weather_icon' => '01d',
            ]);

            $this->info("=== Diagnosing Weather Rules (Test Data) ===\n");
        } else {
            // Use latest real data for the city
            $weather = WeatherHistory::where('city', $city)->latest()->first();

            if (!$weather) {
                $this->error("No weather history found for city: {$city}");
                return Command::FAILURE;
            }

            $this->info("=== Diagnostic Report for {$city} ===\n");
        }

        $report = $diagnosticService->diagnose($weather);

        // Display weather data
        $this->info('📊 WEATHER DATA:');
        $this->line("  City: {$report['weather_data']['city']}");
        $this->line("  Temperature: {$report['weather_data']['temperature']}°C");
        $this->line("  Humidity: {$report['weather_data']['humidity']}%");
        $this->line("  Pressure: {$report['weather_data']['pressure']} hPa");
        $this->line("  Wind Speed: {$report['weather_data']['wind_speed']} m/s");
        $this->line('');

        // Display rules and evaluations
        $this->info('📋 RULES EVALUATION:');
        foreach ($report['rules_in_database'] as $type => $typeData) {
            $this->line("\n  {$type} (Actual Value: {$typeData['actual_value']}):");
            foreach ($typeData['rules'] as $rule) {
                $status = $rule['is_met'] ? '✅ MATCHES' : '❌ no match';
                $ruleDisplay = $rule['name'] . ' (' . $rule['operator'] . ' ' . ($rule['threshold'] ?? $rule['min_value'] . '-' . $rule['max_value']) . ')';
                $this->line("    [{$status}] {$ruleDisplay}");
                $this->line("              → {$rule['evaluation']}");
            }
        }

        // Display triggered rules
        $this->line('');
        $this->info('🔔 TRIGGERED RULES:');
        if (empty($report['triggered_rules'])) {
            $this->line('  (None)');
        } else {
            foreach ($report['triggered_rules'] as $rule) {
                $this->line("  ✓ {$rule['name']} (type: {$rule['rule_type']}, +{$rule['score_weight']} points)");
            }
        }

        // Display analysis summary
        $this->line('');
        $this->info('📈 ANALYSIS SUMMARY:');
        $this->line("  Total Score: {$report['analysis_summary']['total_score']}/100");
        $this->line("  Risk Level: {$report['analysis_summary']['risk_level']}");
        $this->line("  Risk Category: {$report['analysis_summary']['risk_category_name']}");
        $this->line("  Rules Triggered: {$report['analysis_summary']['triggered_count']}");

        return Command::SUCCESS;
    }
}
