<?php

declare(strict_types = 1);
namespace App\System\Service;


use App\System\Mapper\SystemDeptMapper;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;

class SystemDeptService extends AbstractService
{
    /**
     * @var SystemDeptMapper
     */
    public $mapper;

    public function __construct(SystemDeptMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        $params = array_merge(['orderBy' => 'sort', 'orderType' => 'desc'], $params);
        return parent::getTreeList($params, $isScope);
    }

    /**
     * 获取部门领导列表
     * @param array|null $params
     * @return array
     */
    public function getLeaderList(?array $params = null): array
    {
        return $this->mapper->getLeaderList($params);
    }

    /**
     * 新增部门领导
     * @param array $data
     * @return bool
     */
    public function addLeader(array $data): bool
    {
        $users = [];
        foreach ($data['users'] as $item) {
            $users[] = array_merge(['created_at' => date('Y-m-d H:i:s')], $item);
        }
        return $this->mapper->addLeader((int) $data['id'], $users);
    }

    /**
     * 删除部门领导
     * @param array $data
     * @return bool
     */
    public function delLeader(array $data): bool
    {
        $users = [];
        foreach ($data['ids'] as $id) {
            $users[] = [ 'user_id' => $id ];
        }
        return $this->mapper->delLeader((int) $data['id'], $users);
    }

    /**
     * 获取前端选择树
     * @return array
     */
    public function getSelectTree(): array
    {
        return $this->mapper->getSelectTree();
    }

    /**
     * 新增部门
     * @param array $data
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save(array $data): int
    {
        return $this->mapper->save($this->handleData($data));
    }

    /**
     * 更新部门
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(int $id, array $data): bool
    {
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 处理数据
     * @param $data
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function handleData($data): array
    {
        if (isset($data['id']) && $data['id'] == $data['parent_id']) {
            throw new NormalStatusException(t('system.parent_dept_error'), 500);
        }

        $pid = $data['parent_id'] ?? 0;

        if ($pid === 0) {
            $data['level'] = $data['parent_id'] = '0';
        } else {
            $data['level'] = $this->read($data['parent_id'])->level . ',' . $data['parent_id'];
        }

        return $data;
    }

    /**
     * 真实删除部门
     * @param array $ids
     * @return array|null
     */
    public function realDel(array $ids): ?array
    {
        // 跳过的部门
        $ctuIds = [];
        if (count($ids)) foreach ($ids as $id) {
            if (!$this->checkChildrenExists( (int) $id)) {
                $this->mapper->realDelete([$id]);
            } else {
                $ctuIds[] = $id;
            }
        }
        return count($ctuIds) ? $this->mapper->getDeptName($ctuIds) : null;
    }

    /**
     * 检查子部门是否存在
     * @param int $id
     * @return bool
     */
    public function checkChildrenExists(int $id): bool
    {
        return $this->mapper->checkChildrenExists($id);
    }
}