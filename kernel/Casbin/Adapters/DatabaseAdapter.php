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

namespace Mine\Casbin\Adapters;

use Casbin\Model\Model;
use Casbin\Persist\Adapter;
use Casbin\Persist\AdapterHelper;
use Mine\Casbin\Rule\Rule;

final class DatabaseAdapter implements Adapter
{
    use AdapterHelper;

    /**
     * Rules eloquent model.
     *
     * @var Rule
     */
    protected $eloquent;

    /**
     * the DatabaseAdapter constructor.
     */
    public function __construct(Rule $eloquent)
    {
        $this->eloquent = $eloquent;
    }

    /**
     * savePolicyLine function.
     */
    public function savePolicyLine(string $ptype, array $rule): void
    {
        $col['ptype'] = $ptype;
        foreach ($rule as $key => $value) {
            $col['v' . (string) $key] = $value;
        }

        $this->eloquent->fill($col)->save();
    }

    /**
     * loads all policy rules from the storage.
     */
    public function loadPolicy(Model $model): void
    {
        $rows = $this->eloquent::query()->select('ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5')->get()->toArray();

        foreach ($rows as $row) {
            $line = implode(', ', array_filter($row, static function ($val) {
                return $val !== '' && $val !== null;
            }));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * saves all policy rules to the storage.
     */
    public function savePolicy(Model $model): void
    {
        foreach ($model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }

        foreach ($model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
    }

    /**
     * adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        $this->savePolicyLine($ptype, $rule);
    }

    /**
     * This is part of the Auto-Save feature.
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        $count = 0;

        $instance = $this->eloquent->newQuery()->where('ptype', $ptype);

        foreach ($rule as $key => $value) {
            $instance->where('v' . (string) $key, $value);
        }

        foreach ($instance->get() as $model) {
            if ($model->delete()) {
                ++$count;
            }
        }
    }

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
        $count = 0;

        $instance = $this->eloquent->newQuery()->where('ptype', $ptype);
        foreach (range(0, 5) as $value) {
            if ($fieldIndex <= $value && $value < $fieldIndex + \count($fieldValues)) {
                if ($fieldValues[$value - $fieldIndex] !== '') {
                    $instance->where('v' . $value, $fieldValues[$value - $fieldIndex]);
                }
            }
        }

        foreach ($instance->get() as $model) {
            if ($model->delete()) {
                ++$count;
            }
        }
    }
}
