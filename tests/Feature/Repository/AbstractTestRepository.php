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

namespace HyperfTests\Feature\Repository;

use App\Repository\IRepository;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Hyperf\DbConnection\Model\Model;
use PHPUnit\Framework\TestCase;

/**
 * @template T extends IRepository
 */
abstract class AbstractTestRepository extends TestCase
{
    /**
     * @var class-string<T>
     */
    protected string $repositoryClass;

    public function getRepository(): IRepository
    {
        return ApplicationContext::getContainer()->get($this->repositoryClass);
    }

    public function testCreate(): void
    {
        $repository = $this->getRepository();
        $repository->getModel()->newQuery()->whereRaw('1=1')->forceDelete();
        /**
         * @var Model[] $entityList
         */
        $entityList = [];
        $fields = [];
        for ($i = 0; $i <= 10; ++$i) {
            $entityList[] = $current = $repository->create($this->getAttributes());
            $fields[$current->getKeyName()][] = $current->getKey();
        }
        self::assertSame($this->getRepository()->count(), 11);
        self::assertSame($this->getRepository()->list()->count(), 11);

        foreach ($entityList as $entity) {
            $key = $this->getRepository()->findByField($entity->getKey(), $entity->getKeyName());
            self::assertSame($key, $entity->getKey());
            self::assertTrue($this->getRepository()->existsById($entity->getKey()));
            self::assertNotNull($this->getRepository()->findById($entity->getKey()));
            self::assertTrue($this->getRepository()->forceDeleteById($entity->getKey()));
        }
    }

    public function testSearch(): void
    {
        $repository = $this->getRepository();
        $query = $this->getRepository()->handleSearch($repository->getModel()->newQuery(), []);
        $repository->getModel()->newQuery()->whereRaw('1=1')->forceDelete();
        $entityList = Collection::make();
        for ($i = 0; $i <= 10; ++$i) {
            $current = $repository->create($this->getAttributes());
            $entityList->push($current);
            foreach ($this->getSearchAttributes($current, $entityList) as $key => $attribute) {
                $query = $this->getRepository()->handleSearch($repository->getModel()->newQuery(), [$key => $attribute]);
                if ($query->count() === 0) {
                    self::fail(\sprintf('Search failed for %s => %s', $key, serialize($attribute)));
                }
                self::assertTrue($query->count() >= 1);
            }
        }
    }

    abstract protected function getAttributes(): array;

    abstract protected function getSearchAttributes(Model $model, Collection $entityList): array;
}
