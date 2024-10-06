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

namespace Mine\Access\Attribute;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class Permission extends AbstractAnnotation
{
    public const OPERATION_AND = 'and';

    public const OPERATION_OR = 'or';

    public function __construct(
        protected array|string $code,
        protected string $operation = self::OPERATION_AND,
    ) {}

    public function getCode(): array
    {
        return \is_array($this->code) ? $this->code : [$this->code];
    }

    public function getOperation(): string
    {
        return $this->operation;
    }
}
