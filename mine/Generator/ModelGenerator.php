<?php

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
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

use App\Setting\Model\SettingGenerateTables;
use App\Setting\Service\SettingGenerateColumnsService;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * 模型生成
 * Class ModelGenerator
 * @package Mine\Generator
 */
class ModelGenerator extends MineGenerator implements CodeGenerator
{
    /**
     * @var SettingGenerateTables
     */
    protected SettingGenerateTables $model;

    /**
     * @var string
     */
    protected string $codeContent;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return ModelGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): ModelGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);
        return $this;
    }

    /**
     * 生成代码
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function generator(): void
    {
        $this->model->module_name[0] = Str::title($this->model->module_name[0]);
        $module = $this->model->module_name;
        if ($this->model->generate_type === 1) {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Model/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Model/";
        }
        $this->filesystem->exists($path) || $this->filesystem->makeDirectory($path, 0755, true, true);

        $command = [
            'command'  => 'mine:model-gen',
            '--module' => $this->model->module_name,
            '--table'  => $this->model->table_name
        ];

        if (! Str::contains($this->model->table_name, Str::lower($this->model->module_name))) {
            throw new NormalStatusException(t('setting.gen_model_error'), 500);
        }

        if (mb_strlen($this->model->table_name) === mb_strlen($this->model->module_name)) {
            throw new NormalStatusException(t('setting.gen_model_error'), 500);
        }

        $input = new ArrayInput($command);
        $output = new NullOutput();

        /** @var \Symfony\Component\Console\Application $application */
        $application = $this->container->get(\Hyperf\Contract\ApplicationInterface::class);
        $application->setAutoExit(false);

        $modelName  = Str::studly(str_replace(env('DB_PREFIX'), '', $this->model->table_name));

        if ($application->run($input, $output) === 0) {

            // 对模型文件处理
            if ($modelName[strlen($modelName) - 1] == 's' && $modelName[strlen($modelName) - 2] != 's') {
                $oldName = Str::substr($modelName, 0, (strlen($modelName) - 1));
                $oldPath = BASE_PATH . "/app/{$module}/Model/{$oldName}.php";
                $sourcePath = BASE_PATH . "/app/{$module}/Model/{$modelName}.php";
                $this->filesystem->put(
                    $sourcePath,
                    str_replace($oldName, $modelName, $this->filesystem->sharedGet($oldPath))
                );
                @unlink($oldPath);
            } else {
                $sourcePath = BASE_PATH . "/app/{$module}/Model/{$modelName}.php";
            }

            if (!empty($this->model->options['relations'])) {
                $this->filesystem->put(
                    $sourcePath,
                    preg_replace('/}$/', $this->getRelations() . "}", $this->filesystem->sharedGet($sourcePath))
                );
            }

            // 压缩包下载
            if ($this->model->generate_type === 1) {
                $toPath = BASE_PATH . "/runtime/generate/php/app/{$module}/Model/{$modelName}.php";

                $isFile = is_file($sourcePath);

                if ($isFile) {
                    $this->filesystem->copy($sourcePath, $toPath);
                } else {
                    $this->filesystem->move($sourcePath, $toPath);
                }
            }
        } else {
            throw new NormalStatusException(t('setting.gen_model_error'), 500);
        }
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->placeholderReplace()->getCodeContent();
    }

    /**
     * 获取控制器模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().'model.stub';
    }

    /**
     * 读取模板内容
     * @return string
     */
    protected function readTemplate(): string
    {
        return $this->filesystem->sharedGet($this->getTemplatePath());
    }

    /**
     * 占位符替换
     */
    protected function placeholderReplace(): ModelGenerator
    {
        $this->setCodeContent(str_replace(
            $this->getPlaceHolderContent(),
            $this->getReplaceContent(),
            $this->readTemplate(),
        ));

        return $this;
    }

    /**
     * 获取要替换的占位符
     */
    protected function getPlaceHolderContent(): array
    {
        return [
            '{NAMESPACE}',
            '{CLASS_NAME}',
            '{TABLE_NAME}',
            '{FILL_ABLE}',
            '{RELATIONS}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getClassName(),
            $this->getTableName(),
            $this->getFillAble(),
            $this->getRelations(),
        ];
    }

    /**
     * 初始化模型命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . "\\Model";
    }

    /**
     * 获取类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName();
    }

    /**
     * 获取表名称
     * @return string
     */
    protected function getTableName(): string
    {
        return $this->model->table_name;
    }

    /**
     * 获取file able
     */
    protected function getFillAble(): string
    {
        $data = make(SettingGenerateColumnsService::class)->getList(
            ['select' => 'column_name', 'table_id' => $this->model->id]
        );
        $columns = [];
        foreach ($data as $column) {
            $columns[] = "'".$column['column_name']."'";
        }

        return implode(', ', $columns);
    }

    /**
     * @return string
     */
    protected function getRelations(): string
    {
        $prefix = env('DB_PREFIX');
        if (!empty($this->model->options['relations'])) {
            $path = $this->getStubDir() . 'ModelRelation/';
            $phpCode = '';
            foreach ($this->model->options['relations'] as $relation) {
                $content = $this->filesystem->sharedGet($path . $relation['type'] . '.stub');
                $content = str_replace(
                    [ '{RELATION_NAME}', '{MODEL_NAME}', '{TABLE_NAME}', '{FOREIGN_KEY}', '{LOCAL_KEY}' ],
                    [ $relation['name'], $relation['model'], str_replace($prefix, '', $relation['table']), $relation['foreignKey'], $relation['localKey'] ],
                    $content
                );
                $phpCode .= $content;
            }
            return $phpCode;
        }
        return '';
    }

    /**
     * 获取业务名称
     * @return string
     */
    public function getBusinessName(): string
    {
        return Str::studly(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
    }


    /**
     * 设置代码内容
     * @param string $content
     */
    public function setCodeContent(string $content)
    {
        $this->codeContent = $content;
    }

    /**
     * 获取代码内容
     * @return string
     */
    public function getCodeContent(): string
    {
        return $this->codeContent;
    }
}