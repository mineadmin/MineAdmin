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

namespace App\Service\DataCenter;

use App\Repository\DataCenter\DictTypeRepository;
use Mine\Abstracts\AbstractService;

/**
 * 字典类型业务
 * Class LoginLogService.
 */
class DictTypeService extends AbstractService
{
    /**
     * @var DictTypeRepository
     */
    public $repository;

    public function __construct(DictTypeRepository $repository)
    {
        $this->repository = $repository;
    }
}
