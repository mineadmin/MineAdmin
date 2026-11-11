<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 後台房間管理（方案 §7.4）：統一查詢入口——房間列表（含對局數/流水數）+ 房間詳情聚合（成員/流水/對局快照/清算）。
 * 所有列表均為「一次查詢 + 內存合併」，不在循環內操作數據庫。
 */

namespace Plugin\Zhh\Scoring\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Service\IService;
use Plugin\Zhh\Scoring\Model\Room;
use Plugin\Zhh\Scoring\Repository\BattleMemberRepository;
use Plugin\Zhh\Scoring\Repository\BattleRepository;
use Plugin\Zhh\Scoring\Repository\RecordRepository;
use Plugin\Zhh\Scoring\Repository\RoomMemberRepository;
use Plugin\Zhh\Scoring\Repository\RoomRepository;
use Plugin\Zhh\Scoring\Repository\SettleTransferRepository;

/**
 * @extends IService<Room>
 */
final class AdminRoomService extends IService
{
    public function __construct(
        protected readonly RoomRepository $repository,
        protected readonly RoomMemberRepository $roomMemberRepository,
        protected readonly RecordRepository $recordRepository,
        protected readonly BattleRepository $battleRepository,
        protected readonly BattleMemberRepository $battleMemberRepository,
        protected readonly SettleTransferRepository $settleTransferRepository
    ) {}

    /**
     * 房間列表：分頁 + 行內對局數/流水數（兩次 GROUP BY 聚合，方案 §7.4）。
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function pageWithCounters(array $params, int $page, int $size): array
    {
        $result = $this->repository->page($params, $page, $size);

        $roomIds = [];
        foreach ($result['list'] as $row) {
            $roomIds[] = (int) $row['id'];
        }

        $battleCounts = $this->battleRepository->countByRoomIds($roomIds);
        $recordCounts = $this->recordRepository->countByRoomIds($roomIds);

        $list = [];
        foreach ($result['list'] as $row) {
            $roomId = (int) $row['id'];
            $row['battle_count'] = (int) ($battleCounts[$roomId] ?? 0);
            $row['record_count'] = (int) ($recordCounts[$roomId] ?? 0);
            $list[] = $row;
        }

        return ['list' => $list, 'total' => (int) $result['total']];
    }

    /**
     * 房間詳情聚合：房間 + 成員 + 逐筆流水 + 對局（含快照與清算方案）（方案 §7.4）。
     *
     * @return array<string, mixed>
     */
    public function detail(int $roomId): array
    {
        $room = $this->repository->findById($roomId);
        if (! $room instanceof Room) {
            throw new BusinessException(ResultCode::NOT_FOUND, '房間不存在');
        }

        $battles = $this->battleRepository->listByRoom($roomId);

        $battleIds = [];
        foreach ($battles as $battle) {
            $battleIds[] = (int) $battle->id;
        }

        return [
            'room' => $room->toArray(),
            'members' => $this->roomMemberRepository->listByRoom($roomId)->toArray(),
            'records' => $this->recordRepository->listByRoom($roomId)->toArray(),
            'battles' => $battles->toArray(),
            'battle_members' => $this->battleMemberRepository->listByBattleIds($battleIds)->toArray(),
            'transfers' => $this->settleTransferRepository->listByRoom($roomId)->toArray(),
        ];
    }
}
