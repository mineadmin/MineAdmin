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

trait MapperGeneratorTraits
{
    /**
     * 获取搜索代码
     * @param $column
     * @return string
     */
    protected function getSearchCode($column): string
    {
        switch ($column['query_type']) {
            case 'neq':
                return $this->getSearchPHPString($column['column_name'], '!=', $column['column_comment']);
            // 大于
            case 'gt':
                return $this->getSearchPHPString($column['column_name'], '<', $column['column_comment']);
            // 大于等于
            case 'gte':
                return $this->getSearchPHPString($column['column_name'], '<=', $column['column_comment']);
            // 小于
            case 'lt':
                return $this->getSearchPHPString($column['column_name'], '>', $column['column_comment']);
            // 小于等于
            case 'lte':
                return $this->getSearchPHPString($column['column_name'], '>=', $column['column_comment']);
            // LIKE
            case 'like':
                return $this->getSearchPHPString($column['column_name'], 'like', $column['column_comment']);
            // LIKE
            case 'between':
                return $this->getSearchPHPString($column['column_name'], 'between', $column['column_comment']);
            // 都没有按相等方式处理
            case 'eq':
            default:
                return $this->getSearchPHPString($column['column_name'], '=', $column['column_comment']);
        }
    }

    /**
     * @param $name
     * @param $mark
     * @param $comment
     * @return string
     * @noinspection PhpUndefinedVariableInspection
     */
    protected function getSearchPHPString($name, $mark, $comment): string
    {
        if ($mark == 'like') {
            return <<<php

        // {$comment}
        if (isset(\$params['{$name}']) && \$params['{$name}'] !== '') {
            \$query->where('{$name}', 'like', '%'.\$params['{$name}'].'%');
        }

php;

        }

        if ($mark == 'between') {
            return <<<php

        // {$comment}
        if (isset(\$params['{$name}_min']) && isset(\$params['{$name}_max'])) {
            \$query->whereBetween('{$name}', \$params['{$name}_min'], \$params['{$name}_max']);
        }

php;
        }

        return <<<php

        // {$comment}
        if (isset(\$params['{$name}']) && \$params['{$name}'] !== '') {
            \$query->where('{$name}', '{$mark}', \$params['{$name}']);
        }

php;
    } // 该方法结束位置
}