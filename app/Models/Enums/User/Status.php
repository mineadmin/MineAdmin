<?php

namespace App\Models\Enums\User;

enum Status: int
{
    case Normal = 1;
    case Disable = 2;

    public function isNormal(): bool
    {
        return $this === self::Normal;
    }

    public function isDisable(): bool
    {
        return $this === self::Disable;
    }
}
