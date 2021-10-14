<?php

declare(strict_types=1);
namespace Mine\Generator;

use Psr\Container\ContainerInterface;

abstract class MineGenerator
{
    /**
     * @var string
     */
    protected $stubDir;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * MineGenerator constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setStubDir(BASE_PATH . '/mine/Generator/Stubs/');
        $this->container = $container;
    }

    public function getStubDir(): string
    {
        return $this->stubDir;
    }

    public function setStubDir(string $stubDir)
    {
        $this->stubDir = $stubDir;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }


}