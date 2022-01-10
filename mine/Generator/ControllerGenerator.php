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
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;

/**
 * 控制器生成
 * Class ControllerGenerator
 * @package Mine\Generator
 */
class ControllerGenerator extends MineGenerator implements CodeGenerator
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
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return ControllerGenerator
     */
    public function setGenInfo(SettingGenerateTables $model): ControllerGenerator
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
     */
    public function generator(): void
    {
        $module = Str::title($this->model->module_name);
        if ($this->model->generate_type == '0') {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Controller/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Controller/";
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
     * 获取生成控制器的类型
     * @return string
     */
    public function getType(): string
    {
        return ucfirst($this->model->type);
    }

    /**
     * 获取控制器模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().$this->getType().'/controller.stub';
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
    protected function placeholderReplace(): ControllerGenerator
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
            '{USE}',
            '{CLASS_NAME}',
            '{SERVICE}',
            '{CONTROLLER_ROUTE}',
            '{INDEX_PERMISSION}',
            '{RECYCLE_PERMISSION}',
            '{SAVE_PERMISSION}',
            '{READ_PERMISSION}',
            '{UPDATE_PERMISSION}',
            '{DELETE_PERMISSION}',
            '{REAL_DELETE_PERMISSION}',
            '{RECOVERY_PERMISSION}',
            '{CREATE_REQUEST}',
            '{UPDATE_REQUEST}'
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
            $this->getUse(),
            $this->getClassName(),
            $this->getServiceName(),
            $this->getControllerRoute(),
            $this->getMethodRoute('index'),
            $this->getMethodRoute('recycle'),
            $this->getMethodRoute('save'),
            $this->getMethodRoute('read'),
            $this->getMethodRoute('update'),
            $this->getMethodRoute('delete'),
            $this->getMethodRoute('realDelete'),
            $this->getMethodRoute('recovery'),
            $this->getCreateRequestName(),
            $this->getUpdateRequestName(),
        ];
    }

    /**
     * 初始化控制器命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        $namespace = $this->getNamespace() . "\\Controller";
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
        return $this->model->menu_name. '控制器';
    }

    /**
     * 获取使用的类命名空间
     * @return string
     */
    protected function getUse(): string
    {
        $namespace = "\\";
        if (!empty($this->model->package_name)) {
            $namespace = "\\" . Str::title($this->model->package_name). "\\";
        }
        return <<<UseNamespace
use {$this->getNamespace()}\\Service\\{$this->getBusinessName()}Service;
use {$this->getNamespace()}\\Request$namespace{$this->getBusinessName()}CreateRequest;
use {$this->getNamespace()}\\Request$namespace{$this->getBusinessName()}UpdateRequest;
UseNamespace;
    }

    /**
     * 获取控制器类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName().'Controller';
    }

    /**
     * 获取服务类名称
     * @return string
     */
    protected function getServiceName(): string
    {
        return $this->getBusinessName().'Service';
    }

    /**
     * 获取控制器路由
     * @return string
     */
    protected function getControllerRoute(): string
    {
        return sprintf(
            '%s/%s',
            Str::lower($this->model->module_name),
            $this->getShortBusinessName()
        );
    }

    /**
     * 获取方法路由
     * @param string $route
     * @return string
     */
    protected function getMethodRoute(string $route): string
    {
        return sprintf(
            '%s:%s:%s',
            Str::lower($this->model->module_name),
            $this->getShortBusinessName(),
            $route
        );
    }

    /**
     * 获取保存数据的验证器
     * @return string
     */
    protected function getCreateRequestName(): string
    {
        return $this->getBusinessName(). 'CreateRequest';
    }

    /**
     * 获取更新数据的验证器
     * @return string
     */
    protected function getUpdateRequestName(): string
    {
        return $this->getBusinessName(). 'UpdateRequest';
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
     * 获取短业务名称
     * @return string
     */
    public function getShortBusinessName(): string
    {
        return Str::camel(str_replace(
            Str::lower($this->model->module_name),
            '',
            str_replace(env('DB_PREFIX'), '', $this->model->table_name)
        ));
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