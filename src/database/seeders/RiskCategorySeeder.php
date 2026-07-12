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
            'suhu_minimal' => 0,
            'suhu_maksimal' => 25,
            'color_badge' => 'success',
            'recommendation' => 'Aktivitas di luar ruangan aman dilakukan.',
            'insight' => 'Parameter cuaca saat ini berada dalam kondisi stabil dengan tingkat risiko yang rendah. Kondisi ini mendukung aktivitas luar ruangan tanpa memerlukan tindakan pencegahan khusus.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'Medium Risk',
            'risk_level' => 'medium',
            'suhu_minimal' => 26,
            'suhu_maksimal' => 31,
            'color_badge' => 'warning',
            'recommendation' => 'Kondisi cuaca normal. Tetap jaga hidrasi dan sesuaikan aktivitas dengan kondisi lingkungan.',
            'insight' => 'Beberapa parameter cuaca mulai menunjukkan peningkatan tingkat risiko. Meskipun masih dalam batas yang dapat ditoleransi, pengguna disarankan untuk tetap memperhatikan perubahan kondisi cuaca.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'High Risk',
            'risk_level' => 'high',
            'suhu_minimal' => 31,
            'suhu_maksimal' => null,
            'color_badge' => 'danger',
            'recommendation' => 'Batasi aktivitas di luar ruangan pada siang hari. Gunakan pelindung dari paparan panas dan perbanyak konsumsi air.',
            'insight' => 'Suhu tinggi berpotensi menyebabkan heat stress dan dehidrasi terutama pada aktivitas luar ruangan.',
            'is_active' => true,
        ]);
    }
}