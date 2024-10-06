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

namespace Mine\GeneratorCrud\Processor;

use Hyperf\CodeParser\Project;
use Hyperf\View\RenderInterface;
use Mine\GeneratorCrud\Context;
use Mine\GeneratorCrud\Entity\GeneratorEntity;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function __construct(
        protected readonly RenderInterface $render
    ) {}

    public function process(Context $c): GeneratorEntity
    {
        $className = $this->getClassName($c);
        $namespace = $this->getNamespace($c);
        $path = $this->getPath($namespace, $className);
        return new GeneratorEntity($this->generator($c, $path, $namespace, $className));
    }

    abstract protected function generator(Context $c, string $path, string $namespace, string $className): string;

    protected function getPath(string $namespace, string $className): string
    {
        return \Hyperf\Support\with(new Project())->path($namespace . '\\' . $className);
    }

    abstract protected function getNamespace(Context $c): string;

    abstract protected function getClassName(Context $c): string;
}
