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

use App\Setting\Model\SettingGenerateColumns;
use App\Setting\Model\SettingGenerateTables;
use Hyperf\Utils\Filesystem\Filesystem;
use Mine\Exception\NormalStatusException;
use Mine\Generator\Traits\VueFunctionsVarsTraits;
use Mine\Helper\Str;
use Hyperf\Database\Model\Collection;

/**
 * Vue index文件生成
 * Class VueIndexGenerator
 * @package Mine\Generator
 */
class VueIndexGenerator extends MineGenerator implements CodeGenerator
{
    use VueFunctionsVarsTraits;

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
     * @return VueIndexGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setGenInfo(SettingGenerateTables $model): VueIndexGenerator
    {
        $this->model = $model;
        $this->filesystem = make(Filesystem::class);
        if (empty($model->module_name) || empty($model->menu_name)) {
            throw new NormalStatusException(t('setting.gen_code_edit'));
        }
        $this->columns = SettingGenerateColumns::query()
            ->where('table_id', $model->id)->orderByDesc('sort')
            ->get([
                'column_name', 'column_comment', 'allow_roles', 'options',
                'is_query', 'is_pk', 'is_list', 'view_type', 'dict_type',
            ]);

        return $this->placeholderReplace();
    }

    /**
     * 生成代码
     */
    public function generator(): void
    {
        $module = Str::lower($this->model->module_name);
        $path = BASE_PATH . "/runtime/generate/vue/src/views/{$module}/{$this->getShortBusinessName()}/index.vue";
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
        return $this->getStubDir().'/Vue/index.stub';
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
    protected function placeholderReplace(): VueIndexGenerator
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
            '{CODE}',
            '{IMPORT}',
            '{EXPORT}',
            '{SHOW_RECYCLE}',
            '{HIDE_PAGE}',
            '{FIRST_SEARCH}',
            '{SEARCH_LIST}',
            '{COLUMN_LIST}',
            '{BUSINESS_EN_NAME}',
            '{QUERY_PARAMS}',
            '{DICT_LIST}',
            '{DICT_DATA}',
            '{EXPORT_EXCEL}',
            '{INPUT_NUMBER}',
            '{SWITCH_STATUS}',
            '{MODULE_NAME}',
            '{PK}',
        ];
    }

    /**
     * 获取要替换占位符的内容
     * @return string[]
     */
    protected function getReplaceContent(): array
    {
        return [
            $this->getCode(),
            $this->getImport(),
            $this->getExport(),
            $this->getShowRecycle(),
            $this->getHidePage(),
            $this->getFirstSearch(),
            $this->getSearchList(),
            $this->getColumnList(),
            $this->getBusinessEnName(),
            $this->getQueryParams(),
            $this->getDictList(),
            $this->getDictData(),
            $this->getExportExcel(),
            $this->getInputNumber(),
            $this->getSwitchStatus(),
            $this->getModuleName(),
            $this->getPk(),
        ];
    }

    /**
     * 获取标识代码
     * @return string
     */
    protected function getCode(): string
    {
        return Str::lower($this->model->module_name) . ':' . $this->getShortBusinessName();
    }

    /**
     * @return string
     */
    protected function getImport(): string
    {
        if ( strpos($this->model->generate_menus, 'import') > 0 ) {
            return str_replace('{CODE}', $this->getCode(), $this->getFormItemTemplate('import'));
        }
        return '';
    }

    /**
     * @return string
     */
    protected function getExport(): string
    {
        if ( strpos($this->model->generate_menus, 'export') > 0 ) {
            return str_replace(
                ['{CODE}', '{BUSINESS_EN_NAME}'], [ $this->getCode(), $this->getBusinessEnName()],
                $this->getFormItemTemplate('export')
            );
        }
        return '';
    }

    /**
     * 获取是否隐藏分页
     * @return string
     */
    protected function getHidePage(): string
    {
        return $this->model->type === 'single' ? 'false' : 'true';
    }

    /**
     * @return string
     */
    protected function getShowRecycle(): string
    {
        return ( strpos($this->model->generate_menus, 'recycle') > 0 ) ? 'true' : 'false';
    }

    /**
     * 获取第一个搜索
     * @return string
     */
    protected function getFirstSearch(): string
    {
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
                return $this->getHtmlType($column);
            }
        }
        return '';
    }

    /**
     * 获取其余搜索列表
     * @return string
     */
    protected function getSearchList(): string
    {
        $jsCode = '';
        $first = false;
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
                if (! $first) {
                    $first = true;
                    continue;
                }
                $jsCode .= str_replace(
                    ['{LABEL_COMMENT}', '{COLUMN_NAME}', '{FORM_ITEM}'],
                    [$column->column_comment, $column->column_name, $this->getHtmlType($column)],
                    $this->getOtherTemplate('searchFormItem')
                );
            }
        }
        return $jsCode;
    }

    /**
     * 获取html类型
     * @param $column
     * @return string
     */
    protected function getHtmlType($column): string
    {
        $tagTypes = ['radio', 'select', 'checkbox'];
        if (!empty($column->dict_type)) {
            return str_replace(
                ['{LABEL_NAME}', '{COLUMN_NAME}', '{OPTION_LIST}'],
                [$column->column_comment, $column->column_name, 'dictData.' . $column->dict_type],
                $this->getOtherTemplate('searchSelect')
            );
        } else if (in_array($column->view_type, $tagTypes)) {
            $data = [];
            foreach ($column->options[$column->view_type] as $item) {
                $data[] = ['label' => $item['name'], 'value' => $item['value']];
            }
            return str_replace(
                ['{LABEL_NAME}', '{COLUMN_NAME}', '{OPTION_LIST}'],
                [$column->column_comment, $column->column_name, json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)],
                $this->getOtherTemplate('searchSelect')
            );
        } else {
            if ($column->view_type === 'date') {
                return str_replace(
                    ['{COLUMN_NAME}', '{LABEL_NAME}', '{DATE_TYPE}', '{WEEK_FORMAT}', '{RANGE_TIPS}'],
                    [
                        $column->column_name,
                        $column->column_comment,
                        $column->options['date'],
                        $column->options['date'] === 'week' ? 'format="第 ww 周"' : '',
                        strpos($column->options['date'], 'range') > 0 ? 'start-placeholder="起始时间" end-placeholder="结束时间"' : ''
                    ],
                    $this->getOtherTemplate('searchDate')
                );
            }

            if ($column->view_type === 'time') {
                return str_replace(
                    ['{COLUMN_NAME}', '{LABEL_NAME}'],
                    [$column->column_name, $column->column_comment],
                    $this->getOtherTemplate('searchTime')
                );
            }
        }

        return str_replace(
            ['{COLUMN_NAME}', '{LABEL_NAME}'],
            [$column->column_name, $column->column_comment],
            $this->getOtherTemplate('searchDefault')
        );
    }

    /**
     * 获取表格显示列
     * @return string
     * @noinspection CheckTagEmptyBody
     */
    protected function getColumnList(): string
    {
        $jsCode = '';
        $viewTypes = ['inputNumber', 'switch'];
        $tagTypes = ['radio', 'select'];
        $notNeedDictType = ['checkbox'];
        foreach ($this->columns as $column) if ($column->is_list === '1') {
            $roleCode = empty($column->allow_roles) ? '' : "v-if=\"\$ROLE('{$column->allow_roles}')\"";
            if (in_array($column->view_type, $viewTypes)) {
                $jsCode .= str_replace(
                    ['{LABEL_COMMENT}', '{COLUMN_NAME}', '{ROLE_CODE}'],
                    [$column->column_comment, $column->column_name, $roleCode],
                    $this->getOtherTemplate('columnBy' . Str::title($column->view_type))
                );
            } else if (!empty($column->dict_type) && ! in_array($column->view_type, $notNeedDictType)) {
                $jsCode .= str_replace(
                    ['{LABEL_COMMENT}', '{COLUMN_NAME}', '{ROLE_CODE}', '{DICT_TYPE}'],
                    [$column->column_comment, $column->column_name, $roleCode, $column->dict_type],
                    $this->getOtherTemplate('columnByDictType')
                );
            } else if (in_array($column->view_type, $tagTypes)) {
                $data = [];
                foreach ($column->options[$column->view_type] as $item) {
                    $data[] = ['label' => $item['name'], 'value' => $item['value']];
                }
                $jsCode .= str_replace(
                    ['{LABEL_COMMENT}', '{COLUMN_NAME}', '{ROLE_CODE}', '{CUSTOM_DATA}'],
                    [$column->column_comment, $column->column_name, $roleCode, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)],
                    $this->getOtherTemplate('columnByArray')
                );
            } else {
                $jsCode .= str_replace(
                    ['{LABEL_COMMENT}', '{COLUMN_NAME}', '{ROLE_CODE}'],
                    [$column->column_comment, $column->column_name, $roleCode],
                    $this->getOtherTemplate('column')
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
     * 获取需要搜索的字段列表
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getQueryParams(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if ($column->is_query === '1') {
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