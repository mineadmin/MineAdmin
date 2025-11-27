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
use App\Model\Permission\User;
use App\Repository\Permission\DepartmentRepository;
use Hyperf\Database\Model\Collection;

class CreatedByIdsExecute extends AbstractExecutable
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
        if ($policyType->isDeptSelf()) {
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
        if ($policyType->isCustomFunc()) {
            $departmentList = $this->loadCustomFunc($policy);
        }
        $ids = [
            $this->getUser()->id,
        ];
        if ($policyType->isSelf()) {
            return $ids;
        }

        if ($policyType->isNotCustomFunc() && $policyType->isNotCustomDept()) {
            /**
             * @var Collection|Department[] $leaderDepartmentList
             */
            $leaderDepartmentList = $this->getUser()->department()->get();
            foreach ($leaderDepartmentList as $department) {
                if ($policyType->isDeptSelf()) {
                    // @phpstan-ignore-next-line
                    $this->getUser()->newQuery()
                        ->whereHas('department', static function ($query) use ($department) {
                            $query->where('id', $department->id);
                            // @phpstan-ignore-next-line
                        })->get()->each(static function (User $user) use (&$ids) {
                            $ids[] = $user->id;
                        });
                }
                if ($policyType->isDeptTree()) {
                    // @phpstan-ignore-next-line
                    $department->getFlatChildren()->each(function (Department $department) use (&$ids) {
                        $this->getUser()->newQuery()
                            ->whereHas('department', static function ($query) use ($department) {
                                $query->where('id', $department->id);
                                // @phpstan-ignore-next-line
                            })->get()->each(static function (User $user) use (&$ids) {
                                $ids[] = $user->id;
                            });
                    });
                }
            }
            /**
             * @var Collection|Position[] $positionList
             */
            $positionList = $this->getUser()->position()->get();
            foreach ($positionList as $position) {
                if ($policyType->isDeptSelf()) {
                    // @phpstan-ignore-next-line
                    $position->department()->get()->each(function (Department $department) use (&$ids) {
                        $this->getUser()->newQuery()
                            ->whereHas('department', static function ($query) use ($department) {
                                $query->where('id', $department->id);
                                // @phpstan-ignore-next-line
                            })->get()->each(static function (User $user) use (&$ids) {
                                $ids[] = $user->id;
                            });
                    });
                }
                if ($policyType->isDeptTree()) {
                    // @phpstan-ignore-next-line
                    $position->department()->get()->each(function (Department $department) use (&$ids) {
                        $department->getFlatChildren()->each(function (Department $department) use (&$ids) {
                            $this->getUser()->newQuery()
                                ->whereHas('department', static function ($query) use ($department) {
                                    $query->where('id', $department->id);
                                    // @phpstan-ignore-next-line
                                })->get()->each(static function (User $user) use (&$ids) {
                                    $ids[] = $user->id;
                                });
                        });
                    });
                }
            }

            if (! empty($departmentList)) {
                foreach ($departmentList as $department) {
                    if ($policyType->isDeptSelf()) {
                        // @phpstan-ignore-next-line
                        $this->getUser()->newQuery()
                            ->whereHas('department', static function ($query) use ($department) {
                                $query->where('id', $department->id);
                                // @phpstan-ignore-next-line
                            })->get()->each(static function (User $user) use (&$ids) {
                                $ids[] = $user->id;
                            });
                    }
                    if ($policyType->isDeptTree()) {
                        // @phpstan-ignore-next-line
                        $department->getFlatChildren()->each(function (Department $department) use (&$ids) {
                            $this->getUser()->newQuery()
                                ->whereHas('department', static function ($query) use ($department) {
                                    $query->where('id', $department->id);
                                    // @phpstan-ignore-next-line
                                })->get()->each(static function (User $user) use (&$ids) {
                                    $ids[] = $user->id;
                                });
                        });
                    }
                }
            }
        }
        return array_unique($ids);
    }
}
