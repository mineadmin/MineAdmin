<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Mine\Crontab;

use Hyperf\Crontab\Crontab;

class MineCrontab extends Crontab
{
    /**
     * 失败策略
     * @var string
     */
    protected string $failPolicy = '3';

    /**
     * 调用参数
     * @var string
     */
    protected string $parameter;

    /**
     * 任务ID
     * @var integer
     */
    protected int $crontab_id;

    /**
     * @return string
     */
    public function getFailPolicy(): string
    {
        return $this->failPolicy;
    }

    /**
     * @param string $failPolicy
     */
    public function setFailPolicy(string $failPolicy): void
    {
        $this->failPolicy = $failPolicy;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @param string $parameter
     */
    public function setParameter(string $parameter): void
    {
        $this->parameter = $parameter;
    }

    /**
     * @return int
     */
    public function getCrontabId(): int
    {
        return $this->crontab_id;
    }

    /**
     * @param int $crontab_id
     */
    public function setCrontabId(int $crontab_id): void
    {
        $this->crontab_id = $crontab_id;
    }
}