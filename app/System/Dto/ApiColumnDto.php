<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\System\Dto;

use Mine\Annotation\ExcelData;
use Mine\Annotation\ExcelProperty;
use Mine\Interfaces\MineModelExcel;

/**
 * 接口字段DTO.
 */
#[ExcelData]
class ApiColumnDto implements MineModelExcel
{
    #[ExcelProperty(value: '所属接口ID', index: 0)]
    public string $api_id;

    #[ExcelProperty(value: '接口名称', index: 1)]
    public string $name;

    #[ExcelProperty(value: '类型 0 请求 1 响应', index: 2)]
    public string $type;

    #[ExcelProperty(value: '数据类型', index: 3)]
    public string $data_type;

    #[ExcelProperty(value: '是否必填 0 非必填 1 必填', index: 4)]
    public string $is_required;

    #[ExcelProperty(value: '默认值', index: 5)]
    public string $default_value;

    #[ExcelProperty(value: '字段说明', index: 6)]
    public string $description;
}
