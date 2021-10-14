<?php

declare(strict_types=1);
namespace Mine\Generator;

interface CodeGenerator
{
    public function generator();

    public function preview();
}