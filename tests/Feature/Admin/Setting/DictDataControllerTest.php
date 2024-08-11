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

namespace HyperfTests\Feature\Admin\Setting;

use App\Model\Setting\DictData;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
class DictDataControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/dictData/list', 'dictData:list');
    }

    public function testCreate(): void
    {
        $this->caseCreate('/admin/dictData', 'dictData:create', [
            'type_id' => 1,
            'label' =>  Str::random(50),
            'value' => Str::random(100),
            'code' => Str::random(100),
            'sort' => 0,
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ], DictData::class);

    }

    public function testSave(): void
    {
        $entity = DictData::create([
            'type_id' => 1,
            'label' =>  Str::random(50),
            'value' => Str::random(100),
            'code' => Str::random(100),
            'sort' => 0,
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $this->caseSave('/admin/dictData/', $entity, 'dictData:save', [
            'type_id' => 1,
            'label' =>  Str::random(50),
            'value' => Str::random(100),
            'code' => Str::random(100),
            'sort' => 1,
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
    }

    public function testDelete(): void
    {
        $entity = DictData::create([
            'type_id' => 1,
            'label' =>  Str::random(50),
            'value' => Str::random(100),
            'code' => Str::random(100),
            'sort' => 1,
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $this->caseDelete('/admin/dictData/', $entity, 'dictData:delete');
    }
}
