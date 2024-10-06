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

use Mine\GeneratorCrud\Enums\TableColumnType;

final class ColumnEntity
{
    public function __construct(
        private string $columnName,
        private TableColumnType $columnType,
        private string $columnComment,
        private bool $isNullable,
        private bool $isPrimary,
        private bool $isUnique,
        private bool $isAutoIncrement,
    ) {}

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

    public function getColumnType(): TableColumnType
    {
        return $this->columnType;
    }

    public function setColumnType(TableColumnType $columnType): void
    {
        $this->columnType = $columnType;
    }

    public function getColumnComment(): string
    {
        return $this->columnComment;
    }

    public function setColumnComment(string $columnComment): void
    {
        $this->columnComment = $columnComment;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function setIsNullable(bool $isNullable): void
    {
        $this->isNullable = $isNullable;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    public function setIsUnique(bool $isUnique): void
    {
        $this->isUnique = $isUnique;
    }

    public function isAutoIncrement(): bool
    {
        return $this->isAutoIncrement;
    }

    public function setIsAutoIncrement(bool $isAutoIncrement): void
    {
        $this->isAutoIncrement = $isAutoIncrement;
    }
}
