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

namespace App\System\Command;

use App\System\Model\SystemUser;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\DbConnection\Db;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class InitialSuperAdminCommand extends HyperfCommand
{
    protected ?string $name = 'initial:super-admin';

    protected string $username = 'superAdmin';

    #[Transactional]
    public function __invoke()
    {
        $yes = $this->input->getOption('yes');
        if (! $yes) {
            $this->output->warning('Are you sure to initial super admin?');
            $this->output->warning('If you want to initial super admin, please use --yes');
            $this->output->warning(
                'Note that this command will reset permissions,' .
                 'roles, and user tables. Please check whether this operation will affect the'
            );
            return;
        }
        Db::table('system_user')->truncate();
        $password = $this->input->getOption('password');
        if (empty($password)) {
            $password = '12345678';
        }
        $username = $this->username;
        SystemUser::create([
            'id' => env('SUPER_ADMIN', 1),
            'username' => $username,
            'password' => $password,
            'user_type' => '100',
            'nickname' => '创始人',
            'email' => 'admin@adminmine.com',
            'phone' => '16858888988',
            'signed' => '广阔天地，大有所为',
            'dashboard' => 'statistics',
            'created_by' => 0,
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        Db::table('system_role')->truncate();
        // Create Administrator Role
        Db::table('system_role')->insert([
            'id' => env('ADMIN_ROLE', 1),
            'name' => '超级管理员（创始人）',
            'code' => 'superAdmin',
            'data_scope' => 0,
            'sort' => 0,
            'created_by' => env('SUPER_ADMIN', 0),
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'remark' => '系统内置角色，不可删除',
        ]);
        if (env('DB_DRIVER') === 'pgsql') {
            Db::select("SELECT setval('system_user_id_seq', 1)");
            Db::select("SELECT setval('system_role_id_seq', 1)");
        }
    }

    protected function configure()
    {
        $this->setDescription('Initial super admin');
        $this->addOption('password', 'pwd', InputOption::VALUE_OPTIONAL, 'password,Default: 12345678');
        $this->addOption('yes', 'y', InputOption::VALUE_NONE, 'yes');
    }
}
