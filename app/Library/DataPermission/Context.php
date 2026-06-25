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

namespace App\Library\DataPermission;

use Hyperf\Context\Context as Ctx;

final class Context
{
    private const DEPT_COLUMN_KEY = 'data_permission_dept_column';

    private const CREATED_BY_COLUMN_KEY = 'data_permission_created_by_column';

    private const SCOPE_TYPE_KEY = 'data_permission_scope_type';

    private const ONLY_TABLES_KEY = 'data_permission_only_tables';

    public static function setOnlyTables(?array $tables): void
    {
        Ctx::set(self::ONLY_TABLES_KEY, $tables);
    }

    public static function getOnlyTables(): array
    {
        return Ctx::get(self::ONLY_TABLES_KEY, []);
    }

    public static function getDeptColumn(): string
    {
        return Ctx::get(self::DEPT_COLUMN_KEY, 'dept_id');
    }

    public static function setDeptColumn(string $column = 'dept_id'): void
    {
        Ctx::set(self::DEPT_COLUMN_KEY, $column);
    }

    public static function getCreatedByColumn(): string
    {
        return Ctx::get(self::CREATED_BY_COLUMN_KEY, 'created_by');
    }

    public static function setCreatedByColumn(string $column = 'created_by'): void
    {
        Ctx::set(self::CREATED_BY_COLUMN_KEY, $column);
    }

    public static function setScopeType(ScopeType $scopeType): void
    {
        Ctx::set(self::SCOPE_TYPE_KEY, $scopeType);
    }

    public static function getScopeType(): ScopeType
    {
        return Ctx::get(self::SCOPE_TYPE_KEY, ScopeType::DEPT_CREATED_BY);
    }
}
