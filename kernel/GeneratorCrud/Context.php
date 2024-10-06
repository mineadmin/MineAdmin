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

namespace Mine\GeneratorCrud;

use Hyperf\Collection\Collection;
use Mine\GeneratorCrud\Entity\TableEntity;

final class Context
{
    public function __construct(
        private Collection $config,
        private Collection $processors,
        private Collection $extra,
        private TableEntity $table,
        private Collection $entities
    ) {}

    public function getConfig(): Collection
    {
        return $this->config;
    }

    public function getTable(): TableEntity
    {
        return $this->table;
    }

    public function getExtra(): Collection
    {
        return $this->extra;
    }

    public function getProcessors(): Collection
    {
        return $this->processors;
    }

    public function getEntities(): Collection
    {
        return $this->entities;
    }
}
