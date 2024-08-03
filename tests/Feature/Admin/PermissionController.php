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

namespace HyperfTests\Feature\Admin;

use App\Http\Common\ResultCode;
use Hyperf\Collection\Arr;

class PermissionController extends Controller
{
    public function testMenus(): void
    {
        $token = $this->token;
        $noTokenResult = $this->get('/admin/permission/menu/list');
        $this->assertSame(Arr::get($noTokenResult, 'code'), ResultCode::UNAUTHORIZED->value);

        $result = $this->get('/admin/permission/menu/list', [], ['Authorization' => 'Bearer ' . $token]);
        $this->assertSame(Arr::get($result, 'code'), ResultCode::SUCCESS->value);
    }
}
