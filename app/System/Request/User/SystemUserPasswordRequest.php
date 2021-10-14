<?php

declare(strict_types=1);

namespace App\System\Request\User;

use App\System\Service\SystemUserService;
use Hyperf\Validation\Request\FormRequest;
use Mine\Helper\LoginUser;

class SystemUserPasswordRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'newPassword' => 'required|confirmed',
            'newPassword_confirmation' => 'required',
            'oldPassword' => ['required', function ($attribute, $value, $fail) {
                $service = $this->container->get(SystemUserService::class);
                $model = $service->mapper->getModel()::find((int) (new LoginUser)->getId(), ['password']);
                if (! $service->mapper->checkPass($value, $model->password)) {
                    $fail(__('system_user.valid_password'));
                }
            }],
        ];
    }
}
