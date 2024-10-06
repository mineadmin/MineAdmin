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

namespace Mine\GeneratorCrud\Entity;

final class TableEntity
{
    public function __construct(
        private string $tableName,
        private string $tableComment,
        private array $columns,
    ) {}

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function getTableComment(): string
    {
        return $this->tableComment;
    }

    public function setTableComment(string $tableComment): void
    {
        $this->tableComment = $tableComment;
    }

    /**
     * @return ColumnEntity[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param ColumnEntity[] $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }
}
