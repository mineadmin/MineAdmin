<?php

namespace Mine\Kernel\GeneratorCrud;

use Hyperf\Collection\Collection;
use Mine\Kernel\GeneratorCrud\Entity\TableEntity;
use Nette\Utils\ArrayList;

final class Context
{
    public function __construct(
        private Collection $config,
        private Collection $processors,
        private Collection $extra,
        private TableEntity $table,
        private Collection $entities
    ){}

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