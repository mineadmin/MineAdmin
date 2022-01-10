<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Generator;

use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;

class TableGenerator extends MineGenerator
{
    /**
     * @var array
     */
    protected $tableInfo;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * 创建数据表
     * @param bool $init
     * @return bool
     */
    public function createTable(bool $init = true): bool
    {
        $init && $this->init();

        if (Schema::hasTable($this->getTableName())) {
            throw new NormalStatusException(
                "数据表 {$this->getTableName()} 已存在",
                500
            );
        }

        // 创建数据表
        // 创建数据表迁移文件, 暂未开发。
        // if ($this->tableInfo['migrate'] && $result) {
        //     $result = $this->createMigrateFile();
        // }

        return $this->execSchemaSql();
    }

    protected function init(): void
    {
        $this->setTableName(
            Str::lower($this->tableInfo['module']) . '_' .
            Str::lower(trim($this->tableInfo['name']))
        );
        $this->setModuleName(Str::lower($this->tableInfo['module']));
    }

    /**
     * 执行建表架构SQL
     */
    protected function execSchemaSql(): bool
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            foreach ($this->tableInfo['columns'] as $column) {
                if (! $this->tableInfo['snowflakeId'] && $column['name'] == $this->tableInfo['pk']) {
                    $table->id($column['name']);
                    continue;
                }
                $currentTable = $table->addColumn(
                    $this->getColumnType($column['type']),
                    $column['name'],
                    $this->getColumnOptions($column),
                );
                if ($column['isNull']) {
                    $currentTable->nullable();
                }
                if (!empty($column['index'])) {
                    switch ($column['index']){
                        case 'NORMAL':
                            $table->index($column['name']);
                            break;
                        case 'UNIQUE':
                            $table->unique($column['name']);
                            break;
                        case 'FULLTEXT':
                            break;
                        default:
                            break;
                    }
                }
            }
            // 添加系统字段
            $this->addSysColumns($table);
            if ($this->tableInfo['snowflakeId']) {
                $table->primary($this->tableInfo['pk']);
            }
            $table->engine = $this->tableInfo['engine'];
            $table->comment($this->tableInfo['comment']);
        });

        return true;
    }

    /**
     * 创建迁移文件
     */
    protected function createMigrateFile(): bool
    {
        /** @var Filesystem $fs */
        $fs = make(Filesystem::class);
        $content = $fs->sharedGet($this->getStubDir() . 'table.stub');
        return true;
    }

    protected function getTableColumns(): string
    {
        return '';
    }

    protected function addSysColumns(Blueprint $table)
    {
        if ($this->tableInfo['autoUser']) {
            $table->addColumn(
                'bigInteger', 'created_by', ['comment' => '创建者']
            )->nullable();
            $table->addColumn(
                'bigInteger', 'updated_by', ['comment' => '更新者']
            )->nullable();
        }
        if ($this->tableInfo['autoTime']) {
            $table->addColumn(
                'timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间']
            )->nullable();
            $table->addColumn(
                'timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间']
            )->nullable();
        }
        if ($this->tableInfo['softDelete']) {
            $table->addColumn(
                'timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间']
            )->nullable();
        }
    }

    protected function getColumnType(string $type): string
    {
        $type = Str::lower($type);
        if (strpos($type, 'int') > 0 || $type == 'int') {
            return $type . 'eger';
        }

        if ($type == 'char') {
            return 'char';
        }

        if (strpos($type, 'char') > 0) {
            return 'string';
        }

        return $type;
    }

    protected function getColumnOptions(array &$column): array
    {
        $type = Str::lower($column['type']);
        $option = [];
        if (strpos($type, 'int') > 0) {
            $option = [
                'unsigned' => $column['unsigned'],
                'length'   => $column['len'],
            ];
        }
        if ($type == 'decimal') {
            $option = [
                'unsigned' => $column['unsigned'],
                'total'    => $column['len'],
                'places'   => 2
            ];
        }

        if ($type == 'char' || strpos($type, 'char') > 0) {
            $option = [ 'length'   => $column['len'] ];
        }

        if (! empty($column['default'])) {
            $option['default'] = $column['default'];
        }
        $option['comment'] = $column['comment'];

        return $option;
    }

    public function setTableName(string $tableName): TableGenerator
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     * @return TableGenerator
     */
    public function setModuleName(string $moduleName): TableGenerator
    {
        $this->moduleName = $moduleName;
        return $this;
    }


    public function setTableInfo(array $tableInfo): TableGenerator
    {
        $this->tableInfo = $tableInfo;
        return $this;
    }

    public function getTableInfo(): array
    {
        return $this->tableInfo;
    }
}