<?php
/** @noinspection PhpExpressionResultUnusedInspection */
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

use App\Setting\Model\SettingGenerateColumns;
use App\Setting\Model\SettingGenerateTables;
use App\Setting\Service\SettingGenerateColumnsService;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;

/**
 * 验证器生成
 * Class RequestGenerator
 * @package Mine\Generator
 */
class RequestGenerator extends MineGenerator implements CodeGenerator
{
    /**
     * @var SettingGenerateTables
     */
    protected $model;

    /**
     * @var string
     */
    protected $codeContent;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $type;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @param string $type
     * @return RequestGenerator
     */
    public function setGenInfo(SettingGenerateTables $model, string $type): RequestGenerator
    {
        $this->model = $model;
        $this->type  = $type;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);
        return $this;
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::title($this->model->module_name);
        if ($this->model->generate_type == '0') {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Request/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Request/";
        }
        if (!empty($this->model->package_name)) {
            $path .= Str::title($this->model->package_name) . '/';
        }
        $this->filesystem->makeDirectory($path, 0755, true, false);
        $this->filesystem->put($path . "{$this->getClassName()}.php", $this->placeholderReplace()->getCodeContent());
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->placeholderReplace()->getCodeContent();
    }

    /**
     * 获取模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().'/request.stub';
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
    protected function placeholderReplace(): RequestGenerator
    {
        $this->setCodeContent(str_replace(
            $this->getPlaceHolderContent(),
            $this->getReplaceContent(),
            $this->readTemplate()
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
            '{COMMENT}',
            '{CLASS_NAME}',
            '{RULES}'
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getComment(),
            $this->getClassName(),
            $this->getRules(),
        ];
    }

    /**
     * 初始化控制器命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        $namespace = $this->getNamespace() . "\\Request";
        if (!empty($this->model->package_name)) {
            return $namespace . "\\" . Str::title($this->model->package_name);
        }
        return $namespace;
    }

    /**
     * 获取控制器注释
     * @return string
     */
    protected function getComment(): string
    {
        return $this->model->menu_name. '验证数据类 ('.$this->type.')';
    }

    /**
     * 获取类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName().$this->type.'Request';
    }

    /**
     * 获取验证数据规则
     * @return string
     */
    protected function getRules(): string
    {
        /* @var SettingGenerateColumns $model */
        $model = make(SettingGenerateColumnsService::class)->mapper->getModel();
        $query = $model->newQuery()->where('table_id', $this->model->id);

        if ($this->type == 'Create') {
            $query = $query->where('is_insert', 1)->where('is_required', 1);
        } else {
            $query = $query->where('is_edit', 1)->where('is_required', 1);
        }

        $columns = $query->get(['column_name', 'column_comment', 'query_type'])->toArray();

        $phpContent = '';
        foreach ($columns as $column) {
            $phpContent .= $this->getRuleCode($column);
        }

        return $phpContent;
    }

    protected function getRuleCode(array $column): string
    {
        return <<<php

            // {$column['column_comment']} 验证
            '{$column['column_name']}' => 'required',

php;
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