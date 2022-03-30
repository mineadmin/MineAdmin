<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemApiGroupMapper;
use App\System\Model\SystemApiGroup;
use Mine\Abstracts\AbstractService;

/**
 * api接口分组业务
 * Class SystemApiGroupService
 * @package App\System\Service
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
     * 获取分组列表 无分页
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        $params['select'] = 'id, name';
        $params['status'] = SystemApiGroup::ENABLE;
        return parent::getList($params, $isScope);
    }
}