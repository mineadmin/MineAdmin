<?php

namespace App\Service\Permission;

use App\Models\Enums\User\Status;
use App\Models\Permission\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MenuService
{
    public function list(): Collection
    {
        return Menu::query()
            ->with('children')
            ->where('parent_id', 0)
            ->orderBy('sort')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Menu
    {
        return DB::transaction(function () use ($data): Menu {
            $data = $this->normalizeDefaults($data);
            $menu = Menu::query()->create(Arr::except($data, ['btnPermission']));

            $this->createButtonPermissions($menu->id, $data);

            return $menu;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateById(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data): void {
            $menu = Menu::query()->find($id);

            if ($menu === null) {
                return;
            }

            $data = $this->normalizeDefaults($data);
            $menu->update(Arr::except($data, ['btnPermission']));
            $this->syncButtonPermissions($id, $data);
        });
    }

    /**
     * @param  array<int, int>|int  $ids
     */
    public function deleteById(array|int $ids): void
    {
        Menu::destroy($ids);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeDefaults(array $data): array
    {
        foreach (['path', 'component', 'redirect', 'remark'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === null) {
                $data[$key] = '';
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createButtonPermissions(int $parentId, array $data): void
    {
        if (Arr::get($data, 'meta.type') !== 'M' || empty($data['btnPermission'])) {
            return;
        }

        foreach ($data['btnPermission'] as $item) {
            Menu::query()->create([
                'parent_id' => $parentId,
                'name' => $item['code'],
                'sort' => 0,
                'status' => Status::Normal,
                'meta' => [
                    'title' => $item['title'],
                    'i18n' => $item['i18n'],
                    'type' => 'B',
                ],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncButtonPermissions(int $parentId, array $data): void
    {
        if (Arr::get($data, 'meta.type') !== 'M' || ! array_key_exists('btnPermission', $data)) {
            return;
        }

        $existsButtonPermissions = Menu::query()
            ->where('parent_id', $parentId)
            ->where('meta->type', 'B')
            ->pluck('id')
            ->flip()
            ->all();

        foreach ($data['btnPermission'] as $item) {
            if (Arr::get($item, 'type') !== 'B') {
                continue;
            }

            $buttonData = [
                'name' => $item['code'],
                'meta' => [
                    'title' => $item['title'],
                    'i18n' => $item['i18n'],
                    'type' => 'B',
                ],
            ];

            if (! empty($item['id'])) {
                if (array_key_exists($item['id'], $existsButtonPermissions)) {
                    Menu::query()
                        ->whereKey($item['id'])
                        ->where('parent_id', $parentId)
                        ->update($buttonData);
                    unset($existsButtonPermissions[$item['id']]);
                }

                continue;
            }

            Menu::query()->create(array_merge($buttonData, [
                'parent_id' => $parentId,
            ]));
        }

        if ($existsButtonPermissions !== []) {
            $this->deleteById(array_keys($existsButtonPermissions));
        }
    }
}
