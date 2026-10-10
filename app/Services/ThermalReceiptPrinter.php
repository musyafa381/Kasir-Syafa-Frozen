<?php

namespace App\Services;

class ThermalReceiptPrinter
{
    /**
     * Mengubah angka nominal menjadi kalimat terbilang Bahasa Indonesia.
     */
    public static function terbilang(float|int $angka): string
    {
        $angka = abs((int) $angka);
        $baca = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];

        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return self::terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return self::terbilang((int) ($angka / 10)) . ' puluh ' . self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'seratus ' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return self::terbilang((int) ($angka / 100)) . ' ratus ' . self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::terbilang((int) ($angka / 1000)) . ' ribu ' . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::terbilang((int) ($angka / 1000000)) . ' juta ' . self::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return self::terbilang((int) ($angka / 1000000000)) . ' milyar ' . self::terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            return self::terbilang((int) ($angka / 1000000000000)) . ' triliun ' . self::terbilang(fmod($angka, 1000000000000));
        }

        return (string) $angka;
    }
}
