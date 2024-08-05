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

use App\Model\Permission\Post;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
class PostControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/post/list', 'post:list');
    }

    public function testCreate(): void
    {
        /*
         * @property string $name 岗位名称
         * @property string $code 岗位代码
         * @property int $sort 排序
         * @property int $status 状态 (1正常 2停用)
         */
        $this->caseCreate('/admin/post', 'post:create', [
            'name' => Str::random(),
            'code' => Str::random(),
            'sort' => rand(1, 99),
            'status' => rand(0, 1),
        ], Post::class);
    }

    public function testSave(): void
    {
        $entity = Post::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'sort' => rand(1, 99),
            'status' => rand(0, 1),
        ]);
        $this->caseSave('/admin/post/', $entity, 'post:save', [
            'name' => Str::random(),
            'code' => Str::random(),
            'sort' => rand(1, 99),
            'status' => rand(0, 1),
        ]);
    }

    public function testDelete(): void
    {
        $entity = Post::create([
            'name' => Str::random(),
            'code' => Str::random(),
            'sort' => rand(1, 99),
            'status' => rand(0, 1),
        ]);
        $this->caseDelete('/admin/post/', $entity, 'post:delete');
    }
}
