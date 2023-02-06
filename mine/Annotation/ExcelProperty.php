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

declare(strict_types = 1);
namespace Mine\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * excel导入导出元数据。
 * @Annotation
 * @Target("PROPERTY")
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ExcelProperty extends AbstractAnnotation
{
    /**
     * 列表头名称
     * @var string
     */
    public string $value;

    /**
     * 列顺序
     * @var int
     */
    public int $index;

    /**
     * 宽度
     * @var int
     */
    public int $width;

    /**
     * 对齐方式，默认居左
     * @var string
     */
    public string $align;

    /**
     * 列表头字体颜色
     * @var string
     */
    public string $headColor;

    /**
     * 列表头背景颜色
     * @var string
     */
    public string $headBgColor;

    /**
     * 列表体字体颜色
     * @var string
     */
    public string $color;

    /**
     * 列表体背景颜色
     * @var string
     */
    public string $bgColor;

    /**
     * 字典数据列表
     */
    public ?array $dictData = null;

    /**
     * 字典名称
     * @var string
     */
    public string $dictName;
    /**
     * 数据路径 用法: object.value
     * @var string
     */
    public string $path;
}