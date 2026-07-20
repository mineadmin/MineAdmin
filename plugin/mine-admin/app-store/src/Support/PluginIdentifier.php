<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Support;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;

final class PluginIdentifier
{
    private const IDENTIFIER_PATTERN = '/\A[a-zA-Z0-9][a-zA-Z0-9_-]*\/[a-zA-Z0-9][a-zA-Z0-9_-]*\z/';

    private const VERSION_PATTERN = '/\A[0-9A-Za-z._+\-]+\z/';

    public function normalize(mixed $identifier): string
    {
        if (! is_string($identifier)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '插件标识不能为空');
        }

        $identifier = trim($identifier);
        if (
            ! preg_match(self::IDENTIFIER_PATTERN, $identifier)
            || str_contains($identifier, '..')
            || str_contains($identifier, '\\')
            || str_contains($identifier, "\0")
        ) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '插件标识格式不正确');
        }

        return $identifier;
    }

    public function normalizeVersion(mixed $version, bool $required = false): ?string
    {
        if ($version === null || $version === '') {
            if ($required) {
                throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '插件版本不能为空');
            }
            return null;
        }

        if (! is_string($version)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '插件版本格式不正确');
        }

        $version = trim($version);
        if (! preg_match(self::VERSION_PATTERN, $version)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '插件版本格式不正确');
        }

        return $version;
    }

    public function vendorAndName(string $identifier): array
    {
        return explode('/', $this->normalize($identifier), 2);
    }
}
