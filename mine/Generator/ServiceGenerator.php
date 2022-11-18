<?php /** @noinspection PhpIllegalStringOffsetInspection */
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
 * 服务类生成
 * Class ServiceGenerator
 * @package Mine\Generator
 */
class ServiceGenerator extends MineGenerator implements CodeGenerator
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
     * @return ServiceGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): ServiceGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);
        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $this->model->module_name[0] = Str::title($this->model->module_name[0]);
        $module = $this->model->module_name;
        if ($this->model->generate_type === 1) {
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Service/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Service/";
        }
        $this->filesystem->exists($path) || $this->filesystem->makeDirectory($path, 0755, true, true);
        $this->filesystem->put($path . "{$this->getClassName()}.php", $this->replace()->getCodeContent());
    }

    /**
     * 预览代码
     */
    public function preview(): string
    {
        return $this->replace()->getCodeContent();
    }

    /**
     * 获取生成的类型
     * @return string
     */
    public function getType(): string
    {
        return ucfirst($this->model->type);
    }

    /**
     * 获取模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir().$this->getType().'/service.stub';
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
    protected function placeholderReplace(): ServiceGenerator
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
            '{USE}',
            '{COMMENT}',
            '{CLASS_NAME}',
            '{MAPPER}',
            '{FIELD_ID}',
            '{FIELD_PID}'
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->initNamespace(),
            $this->getUse(),
            $this->getComment(),
            $this->getClassName(),
            $this->getMapperName(),
            $this->getFieldIdName(),
            $this->getFieldPidName(),
        ];
    }

    /**
     * 初始化服务类命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . "\\Service";
    }

    /**
     * 获取控制器注释
     * @return string
     */
    protected function getComment(): string
    {
        return $this->model->menu_name. '服务类';
    }

    /**
     * 获取使用的类命名空间
     * @return string
     */
    protected function getUse(): string
    {
        return <<<UseNamespace
use {$this->getNamespace()}\\Mapper\\{$this->getBusinessName()}Mapper;
UseNamespace;
    }

    /**
     * 获取控制器类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName().'Service';
    }

    /**
     * 获取Mapper类名称
     * @return string
     */
    protected function getMapperName(): string
    {
        return $this->getBusinessName().'Mapper';
    }

    /**
     * 获取树表ID
     * @return string
     */
    protected function getFieldIdName(): string
    {
        if ($this->getType() == 'Tree') {
            if ( $this->model->options['tree_id'] ?? false ) {
                return $this->model->options['tree_id'];
            } else {
                return 'id';
            }
        }
        return '';
    }

    /**
     * 获取树表父ID
     * @return string
     */
    protected function getFieldPidName(): string
    {
        if ($this->getType() == 'Tree') {
            if ( $this->model->options['tree_pid'] ?? false ) {
                return $this->model->options['tree_pid'];
            } else {
                return 'parent_id';
            }
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