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

namespace App\System\Service;

use App\System\Mapper\SystemApiMapper;
use Mine\Abstracts\AbstractService;

/**
 * 接口表服务类.
 */
class SystemApiService extends AbstractService
{
    /**
     * @var SystemApiMapper
     */
    public $mapper;

    public function __construct(SystemApiMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取字段列.
     */
    public function getColumnListByApiId(string $id): array
    {
        return $this->mapper->getColumnListByApiId($id);
    }
}
