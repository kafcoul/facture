<?php

namespace App\Helpers;

class NumberToWords
{
    private static $units = [
        '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'
    ];

    private static $tens = [
        '', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'
    ];

    /**
     * Convertir un nombre en lettres (français)
     */
    public static function convert(float $number): string
    {
        $number = abs(floor($number));
        
        if ($number == 0) {
            return 'zéro';
        }

        if ($number >= 1000000000000) {
            return 'plus d\'un billion';
        }

        $words = '';

        // Milliards
        if ($number >= 1000000000) {
            $billions = floor($number / 1000000000);
            $words .= self::convertHundreds($billions) . ' milliard' . ($billions > 1 ? 's' : '') . ' ';
            $number %= 1000000000;
        }

        // Millions
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $words .= self::convertHundreds($millions) . ' million' . ($millions > 1 ? 's' : '') . ' ';
            $number %= 1000000;
        }

        // Milliers
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            if ($thousands == 1) {
                $words .= 'mille ';
            } else {
                $words .= self::convertHundreds($thousands) . ' mille ';
            }
            $number %= 1000;
        }

        // Centaines
        if ($number > 0) {
            $words .= self::convertHundreds($number);
        }

        return ucfirst(trim($words));
    }

    private static function convertHundreds(int $number): string
    {
        $words = '';

        if ($number >= 100) {
            $hundreds = floor($number / 100);
            if ($hundreds == 1) {
                $words .= 'cent ';
            } else {
                $words .= self::$units[$hundreds] . ' cents ';
            }
            $number %= 100;
            
            // Règle: "cents" perd son "s" s'il est suivi d'un autre nombre
            if ($number > 0 && $hundreds > 1) {
                $words = rtrim($words, 's ') . ' ';
            }
        }

        if ($number >= 20) {
            $ten = floor($number / 10);
            $unit = $number % 10;

            if ($ten == 7 || $ten == 9) {
                // 70-79 et 90-99
                $words .= self::$tens[$ten] . '-' . self::$units[10 + $unit];
            } else {
                $words .= self::$tens[$ten];
                if ($unit == 1 && $ten != 8) {
                    $words .= '-et-un';
                } elseif ($unit > 0) {
                    $words .= '-' . self::$units[$unit];
                } elseif ($ten == 8) {
                    $words .= 's'; // quatre-vingts
                }
            }
        } elseif ($number > 0) {
            $words .= self::$units[$number];
        }

        return trim($words);
    }

    /**
     * Convertir avec devise
     */
    public static function convertWithCurrency(float $amount, string $currency = 'XOF'): string
    {
        $currencies = [
            'XOF' => 'Francs CFA',
            'EUR' => 'Euros',
            'USD' => 'Dollars',
            'GNF' => 'Francs Guinéens',
            'NGN' => 'Nairas',
            'GHS' => 'Cedis',
            'KES' => 'Shillings Kenyans',
        ];

        $currencyName = $currencies[$currency] ?? $currency;
        
        return self::convert($amount) . ' ' . $currencyName;
    }
}
