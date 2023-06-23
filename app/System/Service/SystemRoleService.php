<?php

declare(strict_types = 1);
namespace App\System\Service;


use App\System\Mapper\SystemRoleMapper;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\DependProxy;
use Mine\Exception\NormalStatusException;
use Mine\Interfaces\serviceInterface\RoleServiceInterface;

#[DependProxy(values: [ RoleServiceInterface::class ])]
class SystemRoleService extends AbstractService implements RoleServiceInterface
{
    public $mapper;

    public function __construct(SystemRoleMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 获取角色列表，并过滤掉超管角色
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        $params['filterAdminRole'] = true;
        return parent::getList($params, $isScope);
    }

    public function save(array $data): int
    {
        if ($this->mapper->checkRoleCode($data['code'])) {
            throw new NormalStatusException(t('system.rolecode_exists'));
        }
        return $this->mapper->save($data);
    }

    /**
     * 通过角色获取菜单
     * @param int $id
     * @return array
     */
    public function getMenuByRole(int $id): array
    {
        return $this->mapper->getMenuIdsByRoleIds(['ids' => $id]);
    }

    /**
     * 通过code获取角色名称
     * @param string $code
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function findNameByCode(string $code): string
    {
        if (strlen($code) < 1) {
            return t('system.undefined_role');
        }
        $name = $this->mapper->findNameByCode($code);
        return $name ?? t('system.undefined_role');
    }

    /**
     * 通过角色获取部门
     * @param int $id
     * @return array
     */
    public function getDeptByRole(int $id): array
    {
        return $this->mapper->getDeptIdsByRoleIds(['ids' => $id]);
    }

    /**
     * 更新角色信息
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $data);
    }
}