<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 房間 Swagger Schema（方案 §7.1）——僅用於接口文檔。
 */

namespace Plugin\Zhh\Scoring\Schema;

use Hyperf\Swagger\Annotation as OA;

#[
    OA\Schema(
        description: '房間資料',
        example: '{"id":1,"room_no":"AB23CD","owner_id":2,"status":1,"member_limit":8,"current_round":3,"current_battle_id":5,"score_version":12}'
    )
]
final class RoomSchema
{
    #[OA\Property(property: 'id', description: '房間ID', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'room_no', description: '房號', type: 'string', example: 'AB23CD')]
    public string $room_no;

    #[OA\Property(property: 'owner_id', description: '房主用戶ID', type: 'integer', example: 2)]
    public int $owner_id;

    #[OA\Property(property: 'status', description: '狀態:0=等待中,1=進行中,2=已結算', type: 'integer', example: 1)]
    public int $status;

    #[OA\Property(property: 'member_limit', description: '人數上限', type: 'integer', example: 8)]
    public int $member_limit;

    #[OA\Property(property: 'current_round', description: '當前輪次', type: 'integer', example: 3)]
    public int $current_round;

    #[OA\Property(property: 'current_battle_id', description: '當前進行中的對局ID', type: 'integer', nullable: true)]
    public ?int $current_battle_id;

    #[OA\Property(property: 'score_version', description: '分數版本號（斷線重連校準用）', type: 'integer', example: 12)]
    public int $score_version;
}
