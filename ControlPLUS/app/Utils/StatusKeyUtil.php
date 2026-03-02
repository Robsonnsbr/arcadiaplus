<?php

namespace App\Utils;

class StatusKeyUtil
{
    public const DEFAULT_STATUS = 'ATIVO';
    public const MAX_LENGTH = 40;

    /**
     * Normaliza status para formato extensível:
     * - uppercase
     * - sem acento
     * - apenas A-Z, 0-9 e underscore
     */
    public static function normalize(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $status = trim($status);
        if ($status === '') {
            return null;
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status);
        if ($ascii !== false) {
            $status = $ascii;
        }

        $status = strtoupper($status);
        $status = preg_replace('/[^A-Z0-9]+/', '_', $status);
        $status = preg_replace('/_+/', '_', (string)$status);
        $status = trim((string)$status, '_');

        if ($status === '') {
            return null;
        }

        return $status;
    }

    public static function isValid(string $status): bool
    {
        if ($status === '' || strlen($status) > self::MAX_LENGTH) {
            return false;
        }

        return (bool) preg_match('/^[A-Z0-9_]+$/', $status);
    }

    public static function normalizeOrDefault(?string $status): string
    {
        $normalized = self::normalize($status);
        if ($normalized === null || !self::isValid($normalized)) {
            return self::DEFAULT_STATUS;
        }

        return $normalized;
    }
}
