<?php

declare(strict_types=1);
namespace Mine\Crontab;

class MineCrontabScheduler
{
    /**
     * @var MineCrontabManage
     */
    protected $crontabManager;

    /**
     * @var \SplQueue
     */
    protected $schedules;

    public function __construct(MineCrontabManage $crontabManager)
    {
        $this->schedules = new \SplQueue();
        $this->crontabManager = $crontabManager;
    }

    public function schedule(): \SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }
        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        return $this->crontabManager->getCrontabList();
    }
}