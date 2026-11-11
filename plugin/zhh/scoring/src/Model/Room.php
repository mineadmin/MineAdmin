<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 房間模型（方案 §5.2 scoring_room）。
 */

namespace Plugin\Zhh\Scoring\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 房間ID,主鍵
 * @property string $room_no 房號
 * @property int $owner_id 房主
 * @property int $status 狀態:0=等待中,1=進行中,2=已結算
 * @property int $member_limit 人數上限
 * @property int $current_round 當前輪次
 * @property null|int $current_battle_id 當前進行中的對局ID
 * @property int $score_version 分數版本號
 * @property null|Carbon $finished_at 結束時間
 */
final class Room extends Model
{
    use SoftDeletes;

    protected ?string $table = 'scoring_room';

    /**
     * @var string[]
     */
    protected array $fillable = [
        'id',
        'room_no',
        'owner_id',
        'status',
        'member_limit',
        'current_round',
        'current_battle_id',
        'score_version',
        'finished_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array<string, string>
     */
    protected array $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'status' => 'integer',
        'member_limit' => 'integer',
        'current_round' => 'integer',
        'current_battle_id' => 'integer',
        'score_version' => 'integer',
        'finished_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
