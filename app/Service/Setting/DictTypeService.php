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

namespace App\Service\Setting;

use App\Repository\Setting\DictDataRepository;
use App\Repository\Setting\DictTypeRepository;
use App\Service\IService;
use Hyperf\DbConnection\Annotation\Transactional;

final class DictTypeService extends IService
{
    public function __construct(
        protected readonly DictTypeRepository $repository,
        protected readonly DictDataRepository $dictDataRepository
    ) {}

    /**
     * 删除指定字典类型及其相关字典数据.
     *
     * @param int $id 字典类型的 ID
     * @param bool $force 是否硬删除
     * @return bool 删除操作是否成功
     */
    #[Transactional]
    public function deleteDictTypeAndData(int $id, bool $force = true): bool
    {
        return $this->deleteById($id, $force) && $this->dictDataRepository->deleteByDictTypeId($id, $force);
    }
}
