<?php
declare(strict_types = 1);
namespace Mine\Abstracts;


use Mine\Traits\ServiceTrait;

class AbstractService
{
    use ServiceTrait;

    public $mapper;
}