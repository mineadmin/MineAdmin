<?php

namespace Mine\Kernel\GeneratorCrud\Entity;

use SplFileInfo;

final class GeneratorEntity
{
    public function __construct(
        private readonly string $template,
    ){}

    public function getTemplate(): string
    {
        return $this->template;
    }

}