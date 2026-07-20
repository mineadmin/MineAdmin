<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Enum;

enum TerminalStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Success = 'success';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Lost = 'lost';

    public static function terminalValues(): array
    {
        return [
            self::Success->value,
            self::Failed->value,
            self::Cancelled->value,
            self::Expired->value,
            self::Lost->value,
        ];
    }

    public function isTerminal(): bool
    {
        return in_array($this->value, self::terminalValues(), true);
    }
}
