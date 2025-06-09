<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Service\Permission;

use App\Model\Permission\Menu;
use App\Repository\Permission\MenuRepository;
use App\Service\IService;

/**
 * @extends IService<Menu>
 */
final class MenuService extends IService
{
    public function __construct(
        protected readonly MenuRepository $repository
    ) {}

    public function getRepository(): MenuRepository
    {
        return $this->repository;
    }

    public function create(array $data): Menu
    {
        /**
         * @var Menu $model
         */
        $model = parent::create($data);
        if ($data['meta']['type'] === 'M' && ! empty($data['btnPermission'])) {
            foreach ($data['btnPermission'] as $item) {
                $this->repository->create([
                    'parent_id' => $model->id,
                    'name' => $item['code'],
                    'sort' => 0,
                    'status' => 1,
                    'meta' => [
                        'title' => $item['title'],
                        'i18n' => $item['i18n'],
                        'type' => 'B',
                    ],
                ]);
            }
        }
        return $model;
    }

    public function updateById(mixed $id, array $data): mixed
    {
        $model = parent::updateById($id, $data);
        if ($model && $data['meta']['type'] === 'M' && isset($data['btnPermission'])) {
            $existsBtnPermissions = array_flip($this->repository->getQuery()
                ->where('parent_id', $id)
                ->whereJsonContains('meta->type', 'B')
                ->pluck('id')
                ->toArray());

            if (! empty($data['btnPermission'])) {
                foreach ($data['btnPermission'] as $item) {
                    if (! empty($item['type']) && $item['type'] === 'B') {
                        $data = [
                            'name' => $item['code'],
                            'meta' => [
                                'title' => $item['title'],
                                'i18n' => $item['i18n'],
                                'type' => 'B',
                            ],
                        ];
                        if (! empty($item['id'])) {
                            $this->repository->updateById($item['id'], $data);
                            unset($existsBtnPermissions[$item['id']]);
                        } else {
                            $data['parent_id'] = $id;
                            $this->repository->create($data);
                        }
                    }
                }
            }

            if (! empty($existsBtnPermissions)) {
                $this->deleteById(array_keys($existsBtnPermissions));
            }
        }
        return $model;
    }
}
