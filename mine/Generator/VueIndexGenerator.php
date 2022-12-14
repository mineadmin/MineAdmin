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
use Mine\Helper\Str;
use Hyperf\Database\Model\Collection;

/**
 * Vue index文件生成
 * Class VueIndexGenerator
 * @package Mine\Generator
 */
class VueIndexGenerator extends MineGenerator implements CodeGenerator
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
                'column_name', 'column_comment', 'allow_roles', 'options', 'is_required', 'is_insert',
                'is_edit', 'is_query', 'is_sort', 'is_pk', 'is_list', 'view_type', 'dict_type',
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
            '{CRUD}',
            '{COLUMNS}',
            '{BUSINESS_EN_NAME}',
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
            $this->getCrud(),
            $this->getColumns(),
            $this->getBusinessEnName(),
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
     * 获取CRUD配置代码
     * @return string
     */
    protected function getCrud(): string
    {
        // 配置项
        $options = [];
        $options['rowSelection'] = [ 'showCheckedAll' => true ];
        $options['searchLabelWidth'] = "'75px'";
        $options['pk'] = "'".$this->getPk()."'";
        $options['operationColumn'] = false;
        $options['operationWidth'] = 160;
        $options['viewLayoutSetting'] = [
            'layout' => "'auto'",
            'cols' => 1,
            'viewType' => $this->model->component_type == 1 ? "'modal'" : "'drawer'",
            'width' => 600,
        ];
        $options['api'] = $this->getBusinessEnName() . '.getList';
        if (Str::contains($this->model->generate_menus, 'recycle')) {
            $options['recycleApi'] = $this->getBusinessEnName() . '.getRecycleList';
        }
        if (Str::contains($this->model->generate_menus, 'save')) {
            $options['add'] = [
                'show' => true, 'api' => $this->getBusinessEnName() . '.save',
                'auth' => "['".$this->getCode().":save']"
            ];
        }
        if (Str::contains($this->model->generate_menus, 'update')) {
            $options['operationColumn'] = true;
            $options['edit'] = [
                'show' => true, 'api' => $this->getBusinessEnName() . '.update',
                'auth' => "['".$this->getCode().":update']"
            ];
        }
        if (Str::contains($this->model->generate_menus, 'delete')) {
            $options['operationColumn'] = true;
            $options['delete'] = [
                'show' => true,
                'api' => $this->getBusinessEnName() . '.deletes',
                'auth' => "['".$this->getCode().":delete']"
            ];
            if (Str::contains($this->model->generate_menus, 'recycle')) {
                $options['delete']['realApi'] = $this->getBusinessEnName() . '.realDeletes';
                $options['delete']['realAuth'] = "['".$this->getCode().":realDeletes']";
                $options['recovery'] = [
                    'show' => true,
                    'api' => $this->getBusinessEnName() . '.recoverys',
                    'auth' => "['".$this->getCode().":recovery']"
                ];
            }
        }
        $requestRoute = Str::lower($this->model->module_name) . '/' . $this->getShortBusinessName();
        // 导入
        if (Str::contains($this->model->generate_menus, 'import')) {
            $options['import'] = [
                'show' => true,
                'url' => "'".$requestRoute . '/import'."'",
                'templateUrl' => "'".$requestRoute . '/downloadTemplate'."'",
                'auth' => "['".$this->getCode().":import']"
            ];
        }
        // 导出
        if (Str::contains($this->model->generate_menus, 'export')) {
            $options['export'] = [
                'show' => true,
                'url' => "'".$requestRoute . '/export'."'",
                'auth' => "['".$this->getCode().":export']"
            ];
        }
        return 'const crud = reactive(' . $this->jsonFormat($options, true) . ')';
    }

    /**
     * 获取列配置代码
     * @return string
     */
    protected function getColumns(): string
    {
        // 字段配置项
        $options = [];
        foreach ($this->columns as $column) {
            $tmp = [
                'title' => $column->column_comment,
                'dataIndex' => $column->column_name,
                'formType' => $this->getViewType($column->view_type),
            ];
            // 基础
            if ($column->is_query == self::YES) {
                $tmp['search'] = true;
            }
            if ($column->is_insert == self::NO) {
                $tmp['addDisplay'] = false;
            }
            if ($column->is_edit == self::NO) {
                $tmp['editDisplay'] = false;
            }
            if ($column->is_list == self::NO) {
                $tmp['hide'] = true;
            }
            if ($column->is_required == self::YES) {
                $tmp['rules'] = [
                    'required' => true,
                    'message' => '请输入' . $column->column_comment
                ];
            }
            if ($column->is_sort == self::YES) {
                $tmp['sortable'] = [
                    'sortDirections' => [ 'ascend', 'descend' ],
                    'sorter' => true
                ];
            }
            // 扩展项
            if (!empty($column->options)) {
                $collection = $column->options['collection'];
                // 合并
                $tmp = array_merge($tmp, $column->options);
                // 自定义数据
                if (in_array($column->view_type, ['checkbox', 'radio', 'select', 'transfer']) && !empty($collection)) {
                    $tmp['dict'] = [ 'data' => $collection, 'translation' => true ];
                }
                // 对日期时间处理
                if ($column->view_type == 'date' && $column->options['mode'] == 'date') {
                    unset($tmp['mode']);
                    if (isset($column->options['range']) && $column->options['range']) {
                        $tmp['formType'] = 'range';
                        unset($tmp['range']);
                    }
                }
                unset($tmp['collection']);
            }
            // 字典
            if (!empty($column->dict_type)) {
                $tmp['dict'] = [
                    'name' => $column->dict_type,
                    'props' => [ 'label' => 'title', 'value' => 'key' ],
                    'translation' => true
                ];
            }
            // 密码处理
            if ($column->view_type == 'password') {
                $tmp['type'] = 'password';
            }
            // 允许查看字段的角色（前端还待支持）
            // todo...
            $options[] = $tmp;
        }
        return 'const columns = reactive(' . $this->jsonFormat($options) . ')';
    }

    /**
     * @return string
     */
    protected function getShowRecycle(): string
    {
        return ( strpos($this->model->generate_menus, 'recycle') > 0 ) ? 'true' : 'false';
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
            if ($column->is_pk == self::YES) {
                return $column->column_name;
            }
        }
        return '';
    }

    /**
     * 计数器组件方法
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getInputNumber(): string
    {
        if (in_array('numberOperation' , explode(',', $this->model->generate_menus))) {
            return str_replace('{BUSINESS_EN_NAME}', $this->getBusinessEnName(), $this->getOtherTemplate('numberOperation'));
        }
        return '';
    }

    /**
     * 计数器组件方法
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getSwitchStatus(): string
    {
        if (in_array('changeStatus' , explode(',', $this->model->generate_menus))) {
            return str_replace('{BUSINESS_EN_NAME}', $this->getBusinessEnName(), $this->getOtherTemplate('switchStatus'));
        }
        return '';
    }

    /**
     * @param string $tpl
     * @return string
     */
    protected function getOtherTemplate(string $tpl): string
    {
        return $this->filesystem->sharedGet($this->getStubDir() . "/Vue/{$tpl}.stub");
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
     * 视图组件
     * @param string $viewType
     * @return string
     */
    protected function getViewType(string $viewType): string
    {
        $viewTypes = [
            'text' => 'input',
            'password' => 'input',
            'textarea' => 'textarea',
            'inputNumber' => 'input-number',
            'inputTag' => 'input-tag',
            'mention' => 'mention',
            'switch' => 'switch',
            'slider' => 'slider',
            'select' => 'select',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'treeSelect' => 'tree-select',
            'date' => 'date',
            'time' => 'time',
            'rate' => 'rate',
            'cascader' => 'cascader',
            'transfer' => 'transfer',
            'selectUser' => 'select-user',
            'userInfo' => 'user-info',
            'cityLinkage' => 'city-linkage',
            'icon' => 'icon',
            'formGroup' => 'form-group',
            'upload' => 'upload',
            'selectResource' => 'select-resource',
            'editor' => 'editor',
            'codeEditor' => 'code-editor',
        ];

        return $viewTypes[$viewType] ?? 'input';
    }

    /**
     * array 到 json 数据格式化
     * @param array $data
     * @param bool $removeValueQuotes
     * @return string
     */
    protected function jsonFormat(array $data, bool $removeValueQuotes = false): string
    {
        $data = str_replace('    ', '  ', json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        $data = str_replace(['"true"', '"false"', "\\"], [ true, false, ''], $data);
        $data = preg_replace('/(\s+)\"(.+)\":/', "\\1\\2:", $data);
        if ($removeValueQuotes) {
            $data = preg_replace('/(:\s)\"(.+)\"/', "\\1\\2", $data);
        }
        return $data;
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