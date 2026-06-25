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

namespace App\Http\Admin\Request\Permission;

use App\Http\Common\Request\Traits\HttpMethodTrait;
use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Schema\PositionSchema;
use Hyperf\Validation\Request\FormRequest;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: PositionSchema::class,
    only: [
        'name', 'dept_id',
    ]
)]
class PositionRequest extends FormRequest
{
    use HttpMethodTrait;
    use NoAuthorizeTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:50',
            'dept_id' => 'required|integer|exists:department,id',
        ];
        if ($this->isCreate()) {
            $rules['name'] = 'required|string|max:50|unique:position,name';
        }
        if ($this->isUpdate()) {
            $rules['name'] = 'required|string|max:50|unique:position,name,' . $this->route('id');
        }
        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => '岗位名称',
            'dept_id' => '部门ID',
        ];
    }
}
