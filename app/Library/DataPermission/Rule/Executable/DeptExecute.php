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

namespace App\Library\DataPermission\Rule\Executable;

use App\Model\Permission\Department;
use App\Model\Permission\Position;
use App\Repository\Permission\DepartmentRepository;
use Hyperf\Database\Model\Collection;

class DeptExecute extends AbstractExecutable
{
    public function execute(): ?array
    {
        $policy = $this->getPolicy();
        $policyType = $policy->policy_type;
        if ($policyType->isAll()) {
            return null;
        }
        /*
         * @var Department[]|Collection $departmentList
         */
        if ($policyType->isCustomDept()) {
            $departmentList = $this->getRepository(DepartmentRepository::class)->getByIds($policy->value);
        }
        if ($policyType->isCustomFunc()) {
            $departmentList = $this->loadCustomFunc($policy);
        }
        if ($policyType->isDeptSelf() || $policyType->isSelf()) {
            $departmentList = $this->getUser()->department()->get();
        }
        if ($policyType->isDeptTree()) {
            $departmentList = collect();
            /**
             * @var Collection|Department[] $currentList
             */
            $currentList = $this->getUser()->department()->get();
            foreach ($currentList as $item) {
                $departmentList = $departmentList->merge($item->getFlatChildren());
            }
        }
        if (empty($departmentList)) {
            return null;
        }
        $departmentList = $departmentList->merge($this->getUser()->dept_leader()->get());
        /**
         * @var Collection|Position[] $positionList
         */
        $positionList = $this->getUser()->position()->get();
        foreach ($positionList as $position) {
            $departmentList = $departmentList->merge($position->department()->get());
        }
        return $departmentList->pluck('id')->toArray();
    }
}
