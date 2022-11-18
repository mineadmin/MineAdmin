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
     * @var array
     */
    protected array $columns;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return RequestGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): RequestGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->setNamespace($this->model->namespace);

        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)
            ->where(function($query) {
                $query->where('is_required', self::YES);
            })
            ->orderByDesc('sort')
            ->get([ 'column_name', 'column_comment', 'is_insert', 'is_edit' ])->toArray();

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
            $path = BASE_PATH . "/runtime/generate/php/app/{$module}/Request/";
        } else {
            $path = BASE_PATH . "/app/{$module}/Request/";
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
     * 获取模板地址
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->getStubDir() . '/Request/main.stub';
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
            '{RULES}',
            '{ATTRIBUTES}',
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
            $this->getAttributes(),
        ];
    }

    /**
     * 初始化命名空间
     * @return string
     */
    protected function initNamespace(): string
    {
        return $this->getNamespace() . "\\Request";
    }

    /**
     * 获取注释
     * @return string
     */
    protected function getComment(): string
    {
        return $this->model->menu_name . '验证数据类';
    }

    /**
     * 获取类名称
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->getBusinessName() . 'Request';
    }

    /**
     * 获取验证数据规则
     * @return string
     */
    protected function getRules(): string
    {
        $phpContent = '';
        $createCode = '';
        $updateCode = '';
        $path = $this->getStubDir() . '/Request/rule.stub';

        foreach ($this->columns as $column) {
            if ($column['is_insert'] == self::YES) {
                $createCode .= $this->getRuleCode($column);
            }
            if ($column['is_edit'] == self::YES) {
                $updateCode .= $this->getRuleCode($column);
            }
        }

        $phpContent .= str_replace(
            ['{METHOD_COMMENT}', '{METHOD_NAME}', '{LIST}'],
            ['新增数据验证规则', 'saveRules', $createCode],
            $this->filesystem->sharedGet($path)
        );
        $phpContent .= str_replace(
            ['{METHOD_COMMENT}', '{METHOD_NAME}', '{LIST}'],
            ['更新数据验证规则', 'updateRules', $updateCode],
            $this->filesystem->sharedGet($path)
        );

        return $phpContent;
    }

    /**
     * @param array $column
     * @return string
     */
    protected function getRuleCode(array &$column): string
    {
        $space = '            ';
        return sprintf(
            "%s//%s 验证\n%s'%s' => 'required',\n",
            $space,  $column['column_comment'],
            $space, $column['column_name']
        );
    }

    /**
     * @return string
     */
    protected function getAttributes(): string
    {
        $phpCode = '';
        $path = $this->getStubDir() . '/Request/attribute.stub';
        foreach ($this->columns as $column) {
            $phpCode .= $this->getAttributeCode($column);
        }
        return str_replace('{LIST}', $phpCode, $this->filesystem->sharedGet($path));
    }

    /**
     * @param array $column
     * @return string
     */
    protected function getAttributeCode(array &$column): string
    {
        $space = '            ';
        return sprintf(
            "%s'%s' => '%s',\n", $space, $column['column_name'], $column['column_comment']
        );
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