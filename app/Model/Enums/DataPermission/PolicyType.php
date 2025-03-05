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

namespace App\Model\Enums\DataPermission;

/**
 * 策略类型（DEPT_SELF, DEPT_TREE, ALL, SELF, CUSTOM_DEPT, CUSTOM_FUNC）.
 */
enum PolicyType: string
{
    case DeptSelf = 'DEPT_SELF';
    case DeptTree = 'DEPT_TREE';
    case All = 'ALL';
    case Self = 'SELF';
    case CustomDept = 'CUSTOM_DEPT';
    case CustomFunc = 'CUSTOM_FUNC';

    public function isDeptSelf(): bool
    {
        return $this === self::DeptSelf;
    }

    public function isDeptTree(): bool
    {
        return $this === self::DeptTree;
    }

    public function isAll(): bool
    {
        return $this === self::All;
    }

    public function isSelf(): bool
    {
        return $this === self::Self;
    }

    public function isCustomDept(): bool
    {
        return $this === self::CustomDept;
    }

    public function isCustomFunc(): bool
    {
        return $this === self::CustomFunc;
    }

    public function isNotDeptSelf(): bool
    {
        return ! $this->isDeptSelf();
    }

    public function isNotDeptTree(): bool
    {
        return ! $this->isDeptTree();
    }

    public function isNotAll(): bool
    {
        return ! $this->isAll();
    }

    public function isNotSelf(): bool
    {
        return ! $this->isSelf();
    }

    public function isNotCustomDept(): bool
    {
        return ! $this->isCustomDept();
    }

    public function isNotCustomFunc(): bool
    {
        return ! $this->isCustomFunc();
    }
}
