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

namespace HyperfTests\Feature\Admin\Permission;

use App\Model\Permission\Dept;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
class DeptControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/dept/list', 'dept:list');
    }

    public function testCreate(): void
    {
        $this->caseCreate('/admin/dept', 'dept:create', [
            'parent_id' => rand(1, 10),
            'name' => Str::random(),
            'leader' => Str::random(10),
            'phone' => Str::random(11),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(),
        ], Dept::class);
    }

    public function testSave(): void
    {
        $entity = Dept::create([
            'parent_id' => rand(1, 10),
            'name' => Str::random(),
            'leader' => Str::random(10),
            'phone' => Str::random(11),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(),
        ]);
        $this->caseSave('/admin/dept/', $entity, 'dept:save', [
            'parent_id' => rand(1, 10),
            'name' => Str::random(),
            'leader' => Str::random(10),
            'phone' => Str::random(11),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(),
        ]);
    }

    public function testDelete(): void
    {
        $entity = Dept::create([
            'parent_id' => rand(1, 10),
            'name' => Str::random(),
            'leader' => Str::random(10),
            'phone' => Str::random(11),
            'status' => rand(0, 1),
            'sort' => rand(1, 100),
            'remark' => Str::random(),
        ]);
        $this->caseDelete('/admin/dept/', $entity, 'dept:delete');
    }
}
