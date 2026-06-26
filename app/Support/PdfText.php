<?php

namespace App\Support;

class PdfText
{
    public static function html(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return '—';
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if (self::containsArabic($text)) {
            return '<span lang="ar" dir="rtl" class="arabic-text">' . $escaped . '</span>';
        }

        return $escaped;
    }

    public static function containsArabic(string $text): bool
    {
        return (bool) preg_match(
            '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u',
            $text
        );
    }
}
