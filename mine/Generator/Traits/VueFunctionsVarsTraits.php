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

namespace Mine\Generator\Traits;

trait VueFunctionsVarsTraits
{

    /**
     * 获取字典数据
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getDictList(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if (!empty($column->dict_type)) {
                $jsCode .= sprintf(
                    "systemDict.getDict('%s').then(res => {\n      dictData.%s = res.data\n    })\n    ",
                    $column->dict_type, $column->dict_type
                );
            }
        }
        return $jsCode;
    }

    /**
     * 获取字典变量
     * @return string
     * @noinspection BadExpressionStatementJS
     */
    protected function getDictData(): string
    {
        $jsCode = '';
        foreach ($this->columns as $column) {
            if (!empty($column->dict_type)) {
                $jsCode .= sprintf("%s: [],\n    ", $column->dict_type);
            }
        }
        return $jsCode;
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
     * @return string
     */
    protected function getExportExcel(): string
    {
        if (in_array('export' , explode(',', $this->model->generate_menus))) {
            return str_replace('{BUSINESS_EN_NAME}', $this->getBusinessEnName(), $this->getOtherTemplate('exportExcel'));
        }
        return '';
    }

    /**
     * @param string $tpl
     * @return string
     */
    protected function getFormItemTemplate(string $tpl): string
    {
        return $this->filesystem->sharedGet($this->getStubDir() . "/Vue/formItem/{$tpl}.stub");
    }

    /**
     * @param string $tpl
     * @return string
     */
    protected function getOtherTemplate(string $tpl): string
    {
        return $this->filesystem->sharedGet($this->getStubDir() . "/Vue/Other/{$tpl}.stub");
    }
}
