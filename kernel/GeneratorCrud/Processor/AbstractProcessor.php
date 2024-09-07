<?php

namespace Mine\Kernel\GeneratorCrud\Processor;

use Hyperf\CodeParser\Project;
use Mine\Kernel\GeneratorCrud\Context;
use Mine\Kernel\GeneratorCrud\Entity\GeneratorEntity;

abstract class AbstractProcessor implements ProcessorInterface
{

    public function process(Context $c): GeneratorEntity
    {
        $className = $this->getClassName($c);
        $namespace = $this->getNamespace($c);
        $path = $this->getPath($namespace,$className);
        return new GeneratorEntity($this->generator($c,$path, $namespace, $className));
    }

    abstract protected function generator(Context $c,string $path,string $namespace,string $className): string;

    protected function getPath(string $namespace, string $className): string
    {
        return \Hyperf\Support\with(new Project())->path($namespace.'\\'.$className);
    }

    abstract protected function getNamespace(Context $c): string;

    abstract protected function getClassName(Context $c): string;
}