<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Commands\Ast\ModelRewriteConnectionVisitor;
use Hyperf\Database\Commands\Ast\ModelUpdateVisitor;
use Hyperf\Database\Commands\ModelCommand;
use Hyperf\Database\Commands\ModelData;
use Hyperf\Database\Commands\ModelOption;
use Hyperf\Database\PgSQL\Schema\PostgresBuilder;
use Hyperf\Database\Schema\Builder;
use Hyperf\Database\Schema\MySqlBuilder;
use Hyperf\Stringable\Str;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Support\make;

/**
 * @noinspection PhpUnused
 */
#[Command]
class PluginGenModelCommand extends ModelCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct($container);
        $this->setName('plugin:gen:model');
        $this->setDescription('为插件生成模型文件');
        $this->setHelp('plugin:gen:model 表名(不带前缀) 插件名称(取mine.json中的name的值)');
        $this->addOption('plugin', '', InputOption::VALUE_REQUIRED, '插件名称');
    }

    protected string $pluginModelNamespace = '';

    protected string $pluginModelPath = '';

    /** @noinspection DuplicatedCode */
    protected function createModel(string $table, ModelOption $option): void
    {
        if (! $this->input->getOption('plugin')) {
            $this->output?->error('插件名称不能为空');
            exit();
        }

        /** @var Builder|MySqlBuilder|PostgresBuilder $builder */
        $builder = $this->getSchemaBuilder($option->getPool());
        $table = Str::replaceFirst($option->getPrefix(), '', $table);
        $pureTable = Str::after($table, '.');
        $databaseName = Str::contains($table, '.') ? Str::before($table, '.') : null;
        $columns = $this->formatColumns($builder->getColumnTypeListing($pureTable, $databaseName));

        if (empty($columns)) {
            $this->output?->error(
                sprintf('Query columns empty, maybe is table `%s` does not exist.You can check it in database.', $table)
            );
        }

        $class = $option->getTableMapping()[$table] ?? Str::studly(Str::singular($pureTable));
        $class = $this->getPluginNamespace($this->input->getOption('plugin')) . $class;
        $path = $this->getPathWithExtension($class);
        $class = $this->getPluginModelClass($class);

        if (! file_exists($path)) {
            $this->mkdir($path);
            file_put_contents($path, $this->buildClass($table, $class, $option));
        }

        $columns = $this->getColumns($class, $columns, $option->isForceCasts());
        $traverser = new NodeTraverser();
        $traverser->addVisitor(make(ModelUpdateVisitor::class, [
            'class' => $class,
            'columns' => $columns,
            'option' => $option,
        ]));
        $traverser->addVisitor(make(ModelRewriteConnectionVisitor::class, [$class, $option->getPool()]));
        $data = make(ModelData::class, ['class' => $class, 'columns' => $columns]);

        foreach ($option->getVisitors() as $visitorClass) {
            $traverser->addVisitor(make($visitorClass, [$option, $data]));
        }

        $traverser->addVisitor(new CloningVisitor());
        $originStmts = $this->astParser->parse(file_get_contents($path));
        $originTokens = $this->lexer->getTokens();
        $newStmts = $traverser->traverse($originStmts);
        $code = $this->printer->printFormatPreserving($newStmts, $originStmts, $originTokens);
        file_put_contents($path, $code);
        $this->output->writeln(sprintf('<info>Model %s was created.</info>', $class));

        if ($option->isWithIde()) {
            $this->generateIDE($code, $option, $data);
        }
    }

    /**
     * 根据插件名称生成规范路径
     * @param string $pluginName
     * @return string
     */
    private function getPathByPluginName(string $pluginName): string
    {
        $jsonPath = BASE_PATH . '/plugin/' . $pluginName . '/mine.json';

        if (! is_file($jsonPath)) {
            $this->output?->error('插件缺少配置文件');
            exit();
        }

        $jsonArray = json_decode(file_get_contents($jsonPath), true);
        $psrArray = $jsonArray['composer']['psr-4'];

        foreach ($psrArray as $value => $key) {
            if ($key === 'src') {
                $this->pluginModelNamespace = $value . 'Model';
            }
        }

        $path = 'plugin/' . $pluginName . '/src/Model';
        $this->pluginModelPath = $path;

        return $path;
    }

    /**
     * 优化类名的生成
     * @param $class
     * @return string
     */
    private function getPluginModelClass($class): string
    {
        $classArr = explode('\\', $class);
        $classStr = '';

        foreach ($classArr as $k => $value) {
            if (strtolower($value) !== 'src') {
                $classStr .= ($k > 0 ? '\\' : '') . Str::studly($value);
            }
        }

        return $classStr;
    }

    /**
     * 获取模型文件路径
     * @param string $path
     * @param string $extension
     * @return string
     * @noinspection PhpSameParameterValueInspection*/
    private function getPathWithExtension(string $path, string $extension = '.php'): string
    {
        if (Str::endsWith($path, '\\')) {
            $extension = '';
        }

        return BASE_PATH . '/' . str_replace('\\', '/', substr($path, 0)) . $extension;
    }

    private function getPluginNamespace(string $pluginName): string
    {
        $path = $this->getPathByPluginName($pluginName);

        if ($this->pluginModelNamespace === '') {
            throw new RuntimeException("Invalid plugin config");
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext !== '') {
            $path = substr($path, 0, -(strlen($ext) + 1));
        } else {
            $path = trim($path, '/') . '/';
        }

        return str_replace('/', '\\', $path);
    }
}
