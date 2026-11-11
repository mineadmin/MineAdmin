<?php

declare(strict_types=1);
/**
 * This file is part of the zhh/scoring plugin.
 *
 * 房間數據倉庫（方案 §7.3 建房/鎖行/版本號）。
 */

namespace Plugin\Zhh\Scoring\Repository;

use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Plugin\Zhh\Scoring\Model\Room;
use Plugin\Zhh\Scoring\Repository\Concerns\BatchWrites;

/**
 * Class RoomRepository.
 * @extends IRepository<Room>
 */
final class RoomRepository extends IRepository
{
    use BatchWrites;

    public function __construct(protected readonly Room $model) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'room_no'), static function (Builder $query, $roomNo) {
                $query->where('room_no', $roomNo);
            })
            ->when(Arr::get($params, 'owner_id'), static function (Builder $query, $ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }

    /**
     * 取房間並加行鎖（計分/結算/進出房都在事務內先鎖 room 行，方案 §7.3）。
     */
    public function lockForUpdate(int $id): ?Room
    {
        // @phpstan-ignore-next-line
        return $this->getQuery()->whereKey($id)->lockForUpdate()->first();
    }

    public function findByRoomNo(string $roomNo): ?Room
    {
        // @phpstan-ignore-next-line
        return $this->getQuery()->where('room_no', $roomNo)->first();
    }

    /**
     * 取候選房號中已被佔用的部分（一次查詢，避免「循環內查庫」，方案 §1.3）。
     *
     * @param string[] $roomNos
     * @return string[]
     */
    public function existingRoomNos(array $roomNos): array
    {
        if ($roomNos === []) {
            return [];
        }

        return $this->getQuery()->whereIn('room_no', $roomNos)->pluck('room_no')->toArray();
    }

    /**
     * 按 ID 批量取房間並以 id 為鍵返回（我的房間列表一次補齊，避免循環內查庫）。
     *
     * @param int[] $ids
     * @return array<int, array<string, mixed>>
     */
    public function mapByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $map = [];
        $rows = $this->getQuery()
            ->whereIn('id', $ids)
            ->get(['id', 'room_no', 'owner_id', 'status', 'current_battle_id', 'finished_at', 'created_at']);

        foreach ($rows as $row) {
            $map[(int) $row->id] = $row->toArray();
        }

        return $map;
    }

    /**
     * score_version+1（WebSocket 推送與斷線重連校準用，方案 §10）。
     */
    public function incrementVersion(int $id): void
    {
        $this->getQuery()->whereKey($id)->increment('score_version');
    }
}
