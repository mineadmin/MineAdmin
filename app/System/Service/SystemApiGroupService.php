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

use App\System\Mapper\SystemApiGroupMapper;
use App\System\Model\SystemApiGroup;
use Mine\Abstracts\AbstractService;

/**
 * api接口分组业务
 * Class SystemApiGroupService.
 */
class SystemApiGroupService extends AbstractService
{
    /**
     * @var SystemApiGroupMapper
     */
    public $mapper;

    public function __construct(SystemApiGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取分组列表 无分页.
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        $params['select'] = 'id, name';
        $params['status'] = SystemApiGroup::ENABLE;
        return parent::getList($params, $isScope);
    }
}
