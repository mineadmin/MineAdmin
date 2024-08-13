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

namespace App\Http\Admin\Request\Setting;

use App\Kernel\Swagger\Attributes\FormRequest as SwaggerFormRequest;
use App\Schema\ConfigSchema;
use Hyperf\Validation\Request\FormRequest;

#[SwaggerFormRequest(
    schema: ConfigSchema::class,
    description: '配置表单',
    only: ['group_id', 'key', 'value', 'name', 'input_type', 'config_select_data', 'sort', 'remark']
)]
class ConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|integer|exists:config_group,id',
            'key' => 'required|string',
            'value' => 'required|string',
            'name' => 'required|string',
            'input_type' => 'required|string',
            'config_select_data' => 'array',
            'sort' => 'integer',
            'remark' => 'string',
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => trans('config.group_id'),
            'key' => trans('config.key'),
            'value' => trans('config.value'),
            'name' => trans('config.name'),
            'input_type' => trans('config.input_type'),
            'config_select_data' => trans('config.config_select_data'),
            'sort' => trans('config.sort'),
            'remark' => trans('config.remark'),
        ];
    }
}
