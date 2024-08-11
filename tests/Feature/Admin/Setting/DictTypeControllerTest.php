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
use App\Model\Setting\DictType;
use Hyperf\Stringable\Str;
use HyperfTests\Feature\Admin\CrudControllerCase;

/**
 * @internal
 * @coversNothing
 */
class DictTypeControllerTest extends CrudControllerCase
{
    public function testPageList(): void
    {
        $this->casePageList('/admin/dictType/list', 'dictType:list');
    }

    public function testCreate(): void
    {
        $this->caseCreate('/admin/dictType', 'dictType:create', [
            'name' => Str::random(50),
            'code' => Str::random(100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ], DictType::class);
    }

    public function testSave(): void
    {
        $entity = DictType::create([
            'name' => Str::random(50),
            'code' => Str::random(100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $this->caseSave('/admin/dictType/', $entity, 'dictType:save', [
            'name' => Str::random(50),
            'code' => Str::random(100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
    }

    public function testDelete(): void
    {
        $entity = DictType::create([
            'name' => Str::random(50),
            'code' => Str::random(100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $dictData = DictData::create([
            'type_id' => $entity->id,
            'label' => Str::random(50),
            'value' => Str::random(100),
            'code' => Str::random(100),
            'sort' => rand(1, 100),
            'status' => rand(0, 1),
            'remark' => Str::random(),
        ]);
        $this->caseDelete('/admin/dictType/', $entity, 'dictType:delete');
        $dictData->refresh();
        $this->assertTrue($dictData->trashed());
        $dictData->forceDelete();
    }
}
