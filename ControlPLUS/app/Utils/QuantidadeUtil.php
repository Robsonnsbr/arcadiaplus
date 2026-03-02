<?php

namespace App\Utils;

class QuantidadeUtil
{
    public const SCALE = 4;
    public const FACTOR = 10000;

    public static function toUnits($value): int
    {
        $raw = trim((string)$value);
        if ($raw === '') {
            return 0;
        }

        if (strpos($raw, ',') !== false && strpos($raw, '.') !== false) {
            $lastComma = strrpos($raw, ',');
            $lastDot = strrpos($raw, '.');
            if ($lastComma !== false && $lastDot !== false && $lastComma > $lastDot) {
                $raw = str_replace('.', '', $raw);
                $raw = str_replace(',', '.', $raw);
            } else {
                $raw = str_replace(',', '', $raw);
            }
        } elseif (strpos($raw, ',') !== false) {
            $raw = str_replace(',', '.', $raw);
        }

        $negative = substr($raw, 0, 1) === '-';
        $raw = ltrim($raw, '+-');
        $parts = explode('.', $raw, 2);

        $integer = preg_replace('/\D/', '', $parts[0] ?? '');
        $fraction = preg_replace('/\D/', '', $parts[1] ?? '');

        $integer = $integer === '' ? '0' : $integer;
        $fraction = substr(str_pad($fraction, self::SCALE, '0'), 0, self::SCALE);

        $units = ((int)$integer * self::FACTOR) + (int)$fraction;

        return $negative ? (-1 * $units) : $units;
    }

    public static function fromUnits(int $units): string
    {
        $negative = $units < 0;
        $abs = abs($units);
        $integer = intdiv($abs, self::FACTOR);
        $fraction = $abs % self::FACTOR;
        $value = sprintf('%d.%04d', $integer, $fraction);

        return $negative ? ('-' . $value) : $value;
    }
}

