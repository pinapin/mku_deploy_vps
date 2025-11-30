<?php

namespace App\Http\Helpers;

class Formatter
{
    static function convertToRoman($number)

    {
        $romans = [
            "01" => 'I',
            "02" => 'II',
            "03" => 'III',
            "04" => 'IV',
            "05" => 'V',
            "06" => 'VI',
            "07" => 'VII',
            "08" => 'VIII',
            "09" => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$number] ?? $number;
    }

    static function convertNumberToWords($number)
    {
        $ones = array(
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
            'dua belas',
            'tiga belas',
            'empat belas',
            'lima belas',
            'enam belas',
            'tujuh belas',
            'delapan belas',
            'sembilan belas'
        );

        $tens = array('', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh');

        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
        } elseif ($number < 1000) {
            return $ones[intval($number / 100)] . ' ratus ' . self::convertNumberToWords($number % 100);
        } elseif ($number < 1000000) {
            return self::convertNumberToWords(intval($number / 1000)) . ' ribu ' . self::convertNumberToWords($number % 1000);
        }

        return $number; // fallback untuk angka yang lebih besar
    }

    static function date($date)
    {
        return $date->format('d-m-Y');
    }
}
