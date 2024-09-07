<?php

namespace Mine\Kernel\GeneratorCrud;

use Mine\Kernel\GeneratorCrud\Entity\GeneratorEntity;
use Mine\Kernel\GeneratorCrud\Entity\TableEntity;

interface GeneratorInterface
{
    /**
     * @return GeneratorEntity[]
     */
    public function generate(TableEntity $table,array $config = [],array $extra = []): array;

}