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

namespace App\Command;

use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use Hyperf\Command\Annotation\AsCommand;
use Hyperf\Command\Concerns\InteractsWithIO;
use Hyperf\DbConnection\Db;

class UpdateCommand
{
    use InteractsWithIO;

    #[AsCommand(
        signature: 'mine:update-20241031'
    )]
    public function handle()
    {
        $this->output->title('Update 2024_10_31_193302_create_user_belongs_role');
        Db::table(\Hyperf\Config\config('permission.database.table'))->insert([
            'v0' => 'admin',
            'v1' => 'superAdmin',
            'ptype' => 'g',
        ]);
        $result = Db::table(\Hyperf\Config\config('permission.database.table'))->select([
            'v1', 'v0', 'ptype',
        ])->get();
        $result->map(static function (\stdClass $item) {
            if ($item->ptype === 'g') {
                $username = $item->v0;
                $roleCode = $item->v1;
                $userId = User::where('username', $username)->value('id');
                $roleId = Role::where('code', $roleCode)->value('id');
                $userId && $roleId && Db::table('user_belongs_role')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } elseif ($item->ptype === 'p') {
                $menuId = Menu::where('name', $item->v1)->value('id');
                $roleId = Role::where('code', $item->v0)->value('id');
                $menuId && $roleId && Db::table('user_belongs_role')->insert([
                    'menu_id' => $menuId,
                    'role_id' => $roleId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        });
    }
}
