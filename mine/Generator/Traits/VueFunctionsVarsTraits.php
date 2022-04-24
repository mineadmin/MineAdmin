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

use Mine\Helper\Str;

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
            return str_replace(
                ['{PK}', '{BUSINESS_EN_NAME}'],
                [$this->model->getKeyName(), $this->getBusinessEnName()],
                $this->getOtherTemplate('numberOperation')
            );
        }
        return '';
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
}
