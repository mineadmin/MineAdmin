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

namespace Mine\Helper;

class Id
{
    const TWEPOCH = 1620750646000; // 时间起始标记点，作为基准，一般取系统的最近时间（一旦确定不能变动）

    const WORKER_ID_BITS     = 2; // 机器标识位数
    const DATACENTER_ID_BITS = 2; // 数据中心标识位数
    const SEQUENCE_BITS      = 5; // 毫秒内自增位

    private $workerId; // 工作机器ID
    private $datacenterId; // 数据中心ID
    private $sequence; // 毫秒内序列

    private $maxWorkerId     = -1 ^ (-1 << self::WORKER_ID_BITS); // 机器ID最大值
    private $maxDatacenterId = -1 ^ (-1 << self::DATACENTER_ID_BITS); // 数据中心ID最大值

    private $workerIdShift      = self::SEQUENCE_BITS; // 机器ID偏左移位数
    private $datacenterIdShift  = self::SEQUENCE_BITS + self::WORKER_ID_BITS; // 数据中心ID左移位数
    private $timestampLeftShift = self::SEQUENCE_BITS + self::WORKER_ID_BITS + self::DATACENTER_ID_BITS; // 时间毫秒左移位数
    private $sequenceMask       = -1 ^ (-1 << self::SEQUENCE_BITS); // 生成序列的掩码

    private $lastTimestamp = -1; // 上次生产id时间戳

    public function __construct($workerId = 1, $datacenterId = 1, $sequence = 0)
    {
        if ($workerId > $this->maxWorkerId || $workerId < 0) {
            throw new \Exception("worker Id can't be greater than {$this->maxWorkerId} or less than 0");
        }

        if ($datacenterId > $this->maxDatacenterId || $datacenterId < 0) {
            throw new \Exception("datacenter Id can't be greater than {$this->maxDatacenterId} or less than 0");
        }

        $this->workerId     = $workerId;
        $this->datacenterId = $datacenterId;
        $this->sequence     = $sequence;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getId(?int $workerId = null)
    {
        $timestamp = $this->timeGen();

        if (! is_null($workerId)) {
            $this->workerId = $workerId;
        }

        if ($timestamp < $this->lastTimestamp) {
            $diffTimestamp = $this->lastTimestamp - $timestamp;
            throw new \Exception("Clock moved backwards.  Refusing to generate id for {$diffTimestamp} milliseconds");
        }

        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & $this->sequenceMask;

            if (0 == $this->sequence) {
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        return (($timestamp - self::TWEPOCH) << $this->timestampLeftShift) |
            ($this->datacenterId << $this->datacenterIdShift) |
            ($this->workerId << $this->workerIdShift) |
            $this->sequence;
    }

    protected function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }

    protected function timeGen()
    {
        return floor(microtime(true) * 1000);
    }

    // 左移 <<
    protected function leftShift($a, $b)
    {
        return bcmul($a, bcpow(2, $b));
    }
}