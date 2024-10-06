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

use Mine\GeneratorCrud\Context;
use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;

abstract class AbstractAstProcessor extends AbstractProcessor
{
    protected ?Lexer $lexer = null;

    protected ?Parser $astParser = null;

    protected ?PrettyPrinterAbstract $printer = null;

    protected function generator(Context $c, string $path, string $namespace, string $className): string
    {
        if (is_file($path)) {
            $traverser = new NodeTraverser();
            $originStmts = $this->astParser->parse(file_get_contents($path));
            $originTokens = $this->lexer->getTokens();
            $this->handleAst($c, $traverser, $originStmts, $originTokens, $path, $namespace, $className);
            $newStmts = $traverser->traverse($originStmts);
            return $this->printer->printFormatPreserving($newStmts, $originStmts, $originTokens);
        }
        return $this->handleCreate($c, $path, $namespace, $className);
    }

    abstract protected function handleAst(Context $c, NodeTraverser $traverser, array $originStmts, array $originTokens, string $path, string $namespace, string $className): void;

    abstract protected function handleCreate(Context $c, string $path, string $namespace, string $className): string;

    private function initialAst(): void
    {
        $this->lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->astParser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $this->lexer);
        $this->printer = new Standard();
    }
}
