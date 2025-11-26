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

namespace App\Library\DataPermission\Attribute;

use App\Library\DataPermission\ScopeType;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class DataScope extends AbstractAnnotation
{
    public function __construct(
        private readonly string $deptColumn = 'dept_id',
        private readonly string $createdByColumn = 'created_by',
        private readonly ScopeType $scopeType = ScopeType::DEPT_CREATED_BY,
        private readonly ?array $onlyTables = null
    ) {}

    public function getOnlyTables(): ?array
    {
        return $this->onlyTables;
    }

    public function getDeptColumn(): string
    {
        return $this->deptColumn;
    }

    public function getCreatedByColumn(): string
    {
        return $this->createdByColumn;
    }

    public function getScopeType(): ScopeType
    {
        return $this->scopeType;
    }
}
