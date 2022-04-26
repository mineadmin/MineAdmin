<?php
/** @noinspection ThisExpressionReferencesGlobalObjectJS */
/** @noinspection JSValidateTypes */
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
use Hyperf\Database\Model\Collection;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Generator\Traits\VueSaveGeneratorTraits;
use Mine\Generator\Traits\VueFunctionsVarsTraits;
use Mine\Helper\Str;

/**
 * Vue index文件生成
 * Class VueSaveGenerator
 * @package Mine\Generator
 */
class VueSaveGenerator extends MineGenerator implements CodeGenerator
{
    use VueSaveGeneratorTraits, VueFunctionsVarsTraits;

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
     * @var Collection
     */
    protected Collection $columns;

    /**
     * 设置生成信息
     * @param SettingGenerateTables $model
     * @return VueSaveGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): VueSaveGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get([
                'column_name', 'column_comment', 'is_required', 'options',
                'is_pk', 'is_insert', 'is_edit', 'view_type', 'dict_type',
        ]);
        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::lower($this->model->module_name);
        $path = BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}/save.vue";
        $this->filesystem->makeDirectory(
            BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}",
            0755, true, true
        );
        $this->filesystem->put($path, $this->replace()->getCodeContent());
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
        return $this->getStubDir().'/Vue/save.stub';
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
    protected function placeholderReplace(): VueSaveGenerator
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
            '{IMPORT_COMPONENT}',
            '{COMPONENT_TYPE}',
            '{BUSINESS_EN_NAME}',
            '{TREE_REQUEST}',
            '{BUSINESS_NAME}',
            '{FORM_LIST}',
            '{FORM_DATA}',
            '{REQUIRED_LIST}',
            '{DICT_LIST}',
            '{DICT_DATA}',
            '{UPLOAD_IMAGE}',
            '{UPLOAD_FILE}',
            '{MODULE_NAME}',
            '{PK}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->importComponent(),
            $this->getComponentType(),
            $this->getBusinessEnName(),
            $this->getTreeRequest(),
            $this->getBusinessName(),
            $this->getFormList(),
            $this->getFormData(),
            $this->getRequiredList(),
            $this->getDictList(),
            $this->getDictData(),
            $this->getUploadImage(),
            $this->getUploadFile(),
            $this->getModuleName(),
            $this->getPk(),
        ];
    }

    protected function importComponent(): string
    {
        $importCode = '';
        foreach ($this->columns as $column) {
            if ($column->view_type === 'editor' && ! $this->model->importEditorCode) {
                $importCode .= str_replace(
                    ['{NAME}', '{PATH}'],
                    ['editor', '@/components/scEditor'],
                    $this->getOtherTemplate('import')
                );
                $this->model->importEditorCode = true;
            }
            if ($column->view_type === 'area' && ! $this->model->improtAreaCode) {
                $importCode .= str_replace(
                    ['{NAME}', '{PATH}'],
                    ['cityLinkage', '@/components/maCityLinkage'],
                    $this->getOtherTemplate('import')
                );
                $importCode .= str_replace(
                    ['{NAME}', '{PATH}'],
                    ['threeLevelLinkage', '@/components/maCityLinkage/threeLevelLinkage'],
                    $this->getOtherTemplate('import')
                );
                $this->model->improtAreaCode = true;
            }
        }
        return $importCode;
    }

    protected function getComponentType(): string
    {
        return $this->model->component_type === '0' ? 'el-dialog' : 'el-drawer';
    }

    protected function getTreeRequest(): string
    {
        if ($this->model->type === 'tree') {
            return sprintf(
                "%s.tree().then( res => {\n      treeList.value = res.data\n    })",
                $this->getBusinessEnName()
            );
        } else {
            return '';
        }
    }

    /**
     * 获取其余搜索列表
     * @return string
     */
    protected function getFormList(): string
    {
        $jsCode = '';

        // 对树表生成级联选择器
        if (!empty($this->model->options['tree_parent_id'])) {
            $parent_id = $this->model->options['tree_parent_id'];
            $jsCode .= str_replace('{COLUMN_NAME}', $parent_id, $this->getFormItemTemplate('treeCascader'));
        }

        foreach ($this->columns as $column) {
            if (isset($this->model->options['tree_parent_id']) && $this->model->options['tree_parent_id'] === $column->column_name) {
                continue;
            }
            if ($column->is_insert === '1' || $column->is_edit === '1') {
                $jsCode .= $this->getFormListCode($column);
            }
        }
        return $jsCode;
    }

    /**
     * 获取第一个搜索
     * @return string
     * @noinspection CommaExpressionJS
     */
    protected function getFormData(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_pk === '1' || $column->is_insert === '1' || $column->is_edit === '1') {
                $type = match ($column->view_type) {
                    'checkbox', 'userSelect', 'area' => '[]',
                    'userinfo' => !empty($column->options['userinfo'])
                        ? sprintf("'%s'", user()->getUserInfo()[$column->options['userinfo']])
                        : "''",
                    default => "''"
                };
                $jsCode .= sprintf("%s: %s,\n    ", $column->column_name, $type);
            }
        }
        return $jsCode;
    }

    /**
     * 获取其余搜索列表
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getRequiredList(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_required === '1') {
                $jsCode .= sprintf(
                    "%s: [{required: true, message: '%s必填', trigger: 'blur' }],\n    ",
                    $column->column_name,
                    $column->column_comment
                );
            }
        }
        return $jsCode;
    }

    /**
     * 获取业务英文名
     * @return string
     */
    protected function getBusinessEnName(): string
    {
        return Str::camel(str_replace(env('DB_PREFIX'), '', $this->model->table_name));
    }

    /**
     * 获取业务名
     * @return string
     */
    protected function getBusinessName(): string
    {
        return str_replace('管理', '', $this->model->menu_name);
    }

    /**
     * @return string
     */
    protected function getModuleName(): string
    {
        return Str::lower($this->model->module_name);
    }

    /**
     * 返回主键
     * @return string
     */
    protected function getPk(): string
    {
        foreach ($this->columns as $column) {
            if ($column->is_pk == '1') {
                return $column->column_name;
            }
        }
        return '';
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
     * 获取图片上传处理代码
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getUploadImage(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->view_type == 'image') {
                $jsCode .= str_replace(
                    ['{FUN_NAME}', '{COLUMN_NAME}'],
                    [Str::studly($column->column_name), $column->column_name],
                    $this->getOtherTemplate('uploadImage')
                );
            }
        }
        return $jsCode;
    }

    /**
     * 获取文件上传处理代码
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getUploadFile(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->view_type == 'file') {
                $jsCode .= str_replace(
                    ['{FUN_NAME}', '{COLUMN_NAME}'],
                    [Str::studly($column->column_name), $column->column_name],
                    $this->getOtherTemplate('uploadFile')
                );
            }
        }
        return $jsCode;
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