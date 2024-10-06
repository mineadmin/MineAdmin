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

final class GeneratorEntity
{
    public function __construct(
        private readonly string $template,
    ) {}

    public function getTemplate(): string
    {
        return $this->template;
    }
}
