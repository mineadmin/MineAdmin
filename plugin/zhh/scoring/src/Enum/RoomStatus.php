<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 房間狀態（方案 §5.2 scoring_room.status）。
 */

namespace Plugin\Zhh\Scoring\Enum;

final class RoomStatus
{
    /**
     * 等待中（已建房，尚無對局）
     */
    public const WAITING = 0;

    /**
     * 進行中（首次計分後）
     */
    public const PLAYING = 1;

    /**
     * 已結算
     */
    public const SETTLED = 2;
}
