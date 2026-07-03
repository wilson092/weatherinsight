<?php

namespace Database\Seeders;

use App\Models\RiskCategory;
use Illuminate\Database\Seeder;

class RiskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RiskCategory::create([
            'name' => 'Low Risk',
            'risk_level' => 'low',
            'min_score' => 0,
            'max_score' => 10,
            'color_badge' => 'success',
            'recommendation' => 'Aktivitas di luar ruangan aman untuk dilakukan.',
            'insight' => 'Kondisi cuaca ideal dengan risiko minimal.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'Medium Risk',
            'risk_level' => 'medium',
            'min_score' => 11,
            'max_score' => 25,
            'color_badge' => 'warning',
            'recommendation' => 'Disarankan untuk waspada. Pertimbangkan untuk mengurangi aktivitas di luar ruangan.',
            'insight' => 'Beberapa kondisi cuaca dapat meningkatkan risiko. Periksa detail untuk informasi lebih lanjut.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'High Risk',
            'risk_level' => 'high',
            'min_score' => 26,
            'max_score' => null,
            'color_badge' => 'danger',
            'recommendation' => 'Hindari aktivitas di luar ruangan jika tidak perlu. Ambil tindakan pencegahan ekstra.',
            'insight' => 'Kondisi cuaca saat ini berpotensi berbahaya. Prioritaskan keselamatan.',
            'is_active' => true,
        ]);
    }
}