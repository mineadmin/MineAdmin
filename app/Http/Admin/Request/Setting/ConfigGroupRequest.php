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

use App\Schema\ConfigGroupSchema;
use Hyperf\Validation\Request\FormRequest;

#[\App\Kernel\Swagger\Attributes\FormRequest(
    schema: ConfigGroupSchema::class,
    description: '配置分组表单',
    only: ['name', 'code', 'remark']
)]
class ConfigGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'code' => 'required|string',
            'remark' => 'string',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('config_group.name'),
            'code' => trans('config_group.code'),
            'remark' => trans('config_group.remark'),
        ];
    }
}
