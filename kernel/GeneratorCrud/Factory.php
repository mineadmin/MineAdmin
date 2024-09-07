<?php

namespace Mine\Kernel\GeneratorCrud;

final class Factory
{
    public static function app(): GeneratorInterface
    {
        return \Hyperf\Support\make(Application::class);
    }
}