<?php

namespace App\Services\Weather;

class CityNormalizer
{
    /**
     * Kata arah mata angin / administratif yang umum menempel di nama
     * kota besar Indonesia tapi TIDAK dikenal OpenWeatherMap sebagai
     * kota tersendiri (mis. "Jakarta Utara", "Jakarta Selatan").
     */
    protected static array $directionalWords = [
        'Utara', 'Selatan', 'Barat', 'Timur', 'Pusat',
    ];

    /**
     * Whitelist: kombinasi "<Kota> <Arah>" yang justru MEMANG nama
     * kabupaten/kota resmi tersendiri dan valid di OpenWeatherMap,
     * sehingga tidak boleh ikut di-strip.
     *
     * key dan value dalam bentuk lowercase untuk pembandingan.
     */
    protected static array $exceptions = [
        'bandung barat',   // Kabupaten Bandung Barat, valid di OWM
        'lombok timur',    // Kabupaten Lombok Timur, valid di OWM
        'lombok barat',    // Kabupaten Lombok Barat, valid di OWM
        'lombok utara',    // Kabupaten Lombok Utara, valid di OWM
        'kalimantan utara', // Provinsi, jarang di-search tapi jaga-jaga
        'sumatera utara',
        'sumatera barat',
        'sulawesi utara',
        'sulawesi barat',
        'sulawesi selatan',
        'sulawesi tengah',
        'nusa tenggara barat',
        'nusa tenggara timur',
        'jawa barat',
        'jawa timur',
        'jawa tengah',
        'kalimantan barat',
        'kalimantan selatan',
        'kalimantan timur',
        'papua barat',
    ];

    /**
     * Normalisasi nama kota: buang kata arah mata angin di akhir nama
     * (mis. "Jakarta Utara" -> "Jakarta"), kecuali kombinasi tersebut
     * ada di whitelist pengecualian.
     */
    public static function normalize(string $rawCity): string
    {
        $trimmed = trim($rawCity);

        if ($trimmed === '') {
            return $rawCity;
        }

        if (in_array(mb_strtolower($trimmed), self::$exceptions, true)) {
            return $trimmed;
        }

        $pattern = '/\s+(' . implode('|', array_map('preg_quote', self::$directionalWords)) . ')$/iu';
        $stripped = preg_replace($pattern, '', $trimmed);
        $stripped = trim($stripped);

        return $stripped !== '' ? $stripped : $trimmed;
    }
}