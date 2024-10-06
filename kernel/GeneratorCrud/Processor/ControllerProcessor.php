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

use App\Http\Common\Controller\AbstractController;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
use Mine\GeneratorCrud\Context;
use PhpParser\NodeTraverser;

final class ControllerProcessor extends AbstractAstProcessor
{
    protected function handleAst(Context $c, NodeTraverser $traverser, array $originStmts, array $originTokens, string $path, string $namespace, string $className): void {}

    protected function handleCreate(Context $c, string $path, string $namespace, string $className): string
    {
        $commands = Arr::get($c->getConfig(), 'commands', []);
        $stub = file_get_contents(__DIR__ . '/stubs/controller.stub');
        $stub = str_replace('%namespace%', $namespace, $stub);
        $stub = str_replace('%controllerName%', $className, $stub);
        $stub = str_replace('%controllerMethods%', $this->handleMethods($c, $commands), $stub);
        $stub = str_replace('%controllerExtends%', $this->handleExtends($c, $commands), $stub);
        return str_replace('%use%', $this->handleUse($c, $commands), $stub);
    }

    protected function handleExtends(Context $c, array $commands): string
    {
        if (Arr::exists($commands, 'controller.extend')) {
            return Arr::get($commands, 'controller.extend');
        }

        return AbstractController::class;
    }

    protected function handleMethods(Context $c, array $commands): string
    {
        $content = '';
        $methods = Arr::get($commands, 'controller.methods', [
            '__construct', 'list', 'create', 'save', 'delete',
        ]);
        foreach ($methods as $method) {
            $content .= file_get_contents(__DIR__ . '/stubs/controller/' . $method . '.stub') . \PHP_EOL;
        }

        return $content;
    }

    protected function handleUse(Context $c, array $command): string
    {
        $use = '';
        foreach (Arr::get($command, 'controller.use', []) as $item) {
            $use .= 'use ' . $item . ';' . \PHP_EOL;
        }
        return $use;
    }

    protected function getNamespace(Context $c): string
    {
        if (Arr::has($c->getConfig(), 'controller.namespace')) {
            return (string) Arr::get($c->getConfig(), 'controller.namespace');
        }
        return 'App\Controller';
    }

    protected function getClassName(Context $c): string
    {
        if (Arr::has($c->getConfig(), 'controller.name')) {
            return Str::ucfirst((string) Arr::get($c->getConfig(), 'controller.name'));
        }
        return Str::ucfirst($c->getTable()->getTableName());
    }
}
