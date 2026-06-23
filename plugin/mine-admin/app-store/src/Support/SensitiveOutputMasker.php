<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Support;

final class SensitiveOutputMasker
{
    private const MAX_LINE_LENGTH = 8192;

    private const KEYWORDS = [
        'APP_KEY',
        'JWT_SECRET',
        'DB_PASSWORD',
        'REDIS_PASSWORD',
        'ACCESS_TOKEN',
        'SECRET',
        'TOKEN',
        'PASSWORD',
        'PRIVATE_KEY',
    ];

    public function mask(string $line): string
    {
        $line = str_replace("\0", '', $line);
        if (strlen($line) > self::MAX_LINE_LENGTH) {
            $line = substr($line, 0, self::MAX_LINE_LENGTH) . '... [truncated]';
        }

        foreach (self::KEYWORDS as $keyword) {
            $line = preg_replace('/(' . preg_quote($keyword, '/') . '\s*[=:]\s*)([^\s]+)/i', '$1******', $line) ?? $line;
        }

        return preg_replace('#(https?://)([^/\s:@]+):([^/\s@]+)@#i', '$1******:******@', $line) ?? $line;
    }
}
