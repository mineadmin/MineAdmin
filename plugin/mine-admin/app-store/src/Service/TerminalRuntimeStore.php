<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Plugin\MineAdmin\AppStore\Enum\TerminalStatus;
use Plugin\MineAdmin\AppStore\Support\SensitiveOutputMasker;

class TerminalRuntimeStore
{
    private const MAX_LOG_BYTES = 5242880;

    private const TASK_TTL_SECONDS = 86400;

    public function __construct(
        private readonly SensitiveOutputMasker $masker
    ) {}

    public function create(array $payload): array
    {
        $this->ensureDirectories();
        $this->cleanupExpired();

        $taskNo = $this->generateTaskNo();
        $now = $this->now();
        $task = array_merge([
            'task_no' => $taskNo,
            'status' => TerminalStatus::Pending->value,
            'current_step' => 'pending',
            'progress' => 0,
            'exit_code' => null,
            'error_message' => null,
            'created_at' => $now,
            'started_at' => null,
            'finished_at' => null,
            'expires_at' => date('Y-m-d H:i:s', time() + self::TASK_TTL_SECONDS),
            'process_pid' => null,
            'cancel_requested' => false,
            'log_size_exceeded' => false,
        ], $payload);

        $this->saveTask($task);
        $this->appendLog($taskNo, 'info', 'system', sprintf('创建终端任务：%s', $payload['title'] ?? $payload['action'] ?? $taskNo));

        return $task;
    }

    public function getTask(string $taskNo): array
    {
        $this->assertTaskNo($taskNo);
        $path = $this->taskPath($taskNo);
        if (! is_file($path)) {
            throw new BusinessException(ResultCode::NOT_FOUND, '终端任务不存在或已过期');
        }

        $content = file_get_contents($path);
        $task = is_string($content) ? json_decode($content, true) : null;
        if (! is_array($task)) {
            throw new BusinessException(ResultCode::FAIL, '终端任务状态读取失败');
        }

        if ($this->isExpired($task)) {
            $task['status'] = TerminalStatus::Expired->value;
            $task['finished_at'] ??= $this->now();
            $this->saveTask($task);
            $this->releaseTaskLock($taskNo);
        } elseif (($task['status'] ?? null) === TerminalStatus::Running->value && $this->isLost($task)) {
            $task['status'] = TerminalStatus::Lost->value;
            $task['finished_at'] = $this->now();
            $task['error_message'] = '执行进程已丢失，可能是后端服务重启或进程异常退出';
            $this->saveTask($task);
            $this->appendLog($taskNo, 'error', 'system', $task['error_message']);
            $this->releaseTaskLock($taskNo);
        }

        unset($task['process_pid'], $task['cancel_requested'], $task['lock_name'], $task['log_size_exceeded']);
        return $task;
    }

    public function getInternalTask(string $taskNo): array
    {
        $this->assertTaskNo($taskNo);
        $content = @file_get_contents($this->taskPath($taskNo));
        $task = is_string($content) ? json_decode($content, true) : null;

        return is_array($task) ? $task : [];
    }

    public function updateTask(string $taskNo, array $payload): array
    {
        $task = $this->getInternalTask($taskNo);
        if ($task === []) {
            throw new BusinessException(ResultCode::NOT_FOUND, '终端任务不存在或已过期');
        }

        $task = array_merge($task, $payload);
        $this->saveTask($task);
        $this->touchLock($task);

        return $task;
    }

    public function touchTask(string $taskNo): void
    {
        $task = $this->getInternalTask($taskNo);
        if ($task === []) {
            return;
        }

        @touch($this->taskPath($taskNo));
        $this->touchLock($task);
    }

    public function markRunning(string $taskNo, string $step = 'running', int $progress = 1): void
    {
        $this->updateTask($taskNo, [
            'status' => TerminalStatus::Running->value,
            'current_step' => $step,
            'progress' => $progress,
            'started_at' => $this->now(),
        ]);
    }

    public function markSuccess(string $taskNo, string $message = '执行成功'): void
    {
        $this->appendLog($taskNo, 'info', 'system', $message);
        $this->updateTask($taskNo, [
            'status' => TerminalStatus::Success->value,
            'current_step' => 'finished',
            'progress' => 100,
            'exit_code' => 0,
            'finished_at' => $this->now(),
            'process_pid' => null,
        ]);
        $this->releaseTaskLock($taskNo);
    }

    public function markFailed(string $taskNo, string $message, ?int $exitCode = null): void
    {
        $this->appendLog($taskNo, 'error', 'system', $message);
        $this->updateTask($taskNo, [
            'status' => TerminalStatus::Failed->value,
            'current_step' => 'failed',
            'exit_code' => $exitCode,
            'error_message' => $message,
            'finished_at' => $this->now(),
            'process_pid' => null,
        ]);
        $this->releaseTaskLock($taskNo);
    }

    public function requestCancel(string $taskNo): array
    {
        $task = $this->getInternalTask($taskNo);
        if ($task === []) {
            throw new BusinessException(ResultCode::NOT_FOUND, '终端任务不存在或已过期');
        }

        $status = TerminalStatus::tryFrom((string) ($task['status'] ?? ''));
        if ($status?->isTerminal()) {
            return $this->getTask($taskNo);
        }

        $this->appendLog($taskNo, 'warning', 'system', '用户请求取消执行');
        $this->updateTask($taskNo, [
            'cancel_requested' => true,
        ]);

        return $this->getTask($taskNo);
    }

    public function markCancelled(string $taskNo): void
    {
        $this->appendLog($taskNo, 'warning', 'system', '任务已取消');
        $this->updateTask($taskNo, [
            'status' => TerminalStatus::Cancelled->value,
            'current_step' => 'cancelled',
            'finished_at' => $this->now(),
            'process_pid' => null,
        ]);
        $this->releaseTaskLock($taskNo);
    }

    public function shouldCancel(string $taskNo): bool
    {
        $task = $this->getInternalTask($taskNo);
        return (bool) ($task['cancel_requested'] ?? false);
    }

    public function setProcessPid(string $taskNo, ?int $pid): void
    {
        $this->updateTask($taskNo, [
            'process_pid' => $pid,
        ]);
    }

    public function appendLog(string $taskNo, string $level, string $stream, string $message): void
    {
        $this->assertTaskNo($taskNo);
        $this->ensureDirectories();

        $logPath = $this->logPath($taskNo);
        if (is_file($logPath) && filesize($logPath) > self::MAX_LOG_BYTES) {
            $task = $this->getInternalTask($taskNo);
            if (! (bool) ($task['log_size_exceeded'] ?? false)) {
                $this->updateTask($taskNo, ['log_size_exceeded' => true]);
                $message = '终端输出已超过最大限制，后续普通输出将不再追加';
                $level = 'warning';
                $stream = 'system';
            } else {
                return;
            }
        }

        $seq = $this->nextSeq($logPath);
        $line = [
            'seq' => $seq,
            'level' => $level,
            'stream' => $stream,
            'message' => $this->masker->mask($message),
            'time' => $this->now(),
        ];

        file_put_contents(
            $logPath,
            json_encode($line, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
        $this->touchTask($taskNo);
    }

    public function logs(string $taskNo, int $afterSeq = 0, int $limit = 200): array
    {
        $this->assertTaskNo($taskNo);
        $limit = max(1, min($limit, 500));
        $logPath = $this->logPath($taskNo);
        if (! is_file($logPath)) {
            return [
                'task_no' => $taskNo,
                'next_seq' => $afterSeq,
                'has_more' => false,
                'lines' => [],
            ];
        }

        $lines = [];
        $nextSeq = $afterSeq;
        $hasMore = false;
        $file = new \SplFileObject($logPath, 'r');
        while (! $file->eof()) {
            $raw = trim((string) $file->fgets());
            if ($raw === '') {
                continue;
            }
            $item = json_decode($raw, true);
            if (! is_array($item)) {
                continue;
            }
            $seq = (int) ($item['seq'] ?? 0);
            if ($seq <= $afterSeq) {
                continue;
            }
            if (count($lines) >= $limit) {
                $hasMore = true;
                break;
            }
            $lines[] = $item;
            $nextSeq = max($nextSeq, $seq);
        }

        return [
            'task_no' => $taskNo,
            'next_seq' => $nextSeq,
            'has_more' => $hasMore,
            'lines' => $lines,
        ];
    }

    public function acquireActionLock(string $taskNo, string $lockName): void
    {
        $this->ensureDirectories();
        $lockPath = $this->lockPath($lockName);
        if (is_file($lockPath) && time() - ((int) filemtime($lockPath)) > 21600) {
            @unlink($lockPath);
        }

        $handle = @fopen($lockPath, 'x');
        if (! $handle) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '已有插件任务正在执行，请稍后再试');
        }

        fwrite($handle, $taskNo);
        fclose($handle);
        $this->updateTask($taskNo, ['lock_name' => $lockName]);
    }

    public function releaseTaskLock(string $taskNo): void
    {
        $task = $this->getInternalTask($taskNo);
        $lockName = $task['lock_name'] ?? null;
        if (! is_string($lockName) || $lockName === '') {
            return;
        }

        $lockPath = $this->lockPath($lockName);
        if (is_file($lockPath)) {
            $content = trim((string) @file_get_contents($lockPath));
            if ($content === $taskNo) {
                @unlink($lockPath);
            }
        }
    }

    private function saveTask(array $task): void
    {
        $this->ensureDirectories();
        $taskNo = (string) ($task['task_no'] ?? '');
        $this->assertTaskNo($taskNo);
        file_put_contents(
            $this->taskPath($taskNo),
            json_encode($task, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }

    private function nextSeq(string $logPath): int
    {
        if (! is_file($logPath)) {
            return 1;
        }

        $lastSeq = 0;
        $file = new \SplFileObject($logPath, 'r');
        while (! $file->eof()) {
            $raw = trim((string) $file->fgets());
            if ($raw === '') {
                continue;
            }
            $item = json_decode($raw, true);
            if (is_array($item)) {
                $lastSeq = max($lastSeq, (int) ($item['seq'] ?? 0));
            }
        }

        return $lastSeq + 1;
    }

    private function isExpired(array $task): bool
    {
        $expiresAt = strtotime((string) ($task['expires_at'] ?? ''));
        return $expiresAt > 0 && $expiresAt < time();
    }

    private function isLost(array $task): bool
    {
        $startedAt = strtotime((string) ($task['started_at'] ?? ''));
        $updatedAt = filemtime($this->taskPath((string) $task['task_no'])) ?: $startedAt;

        return $startedAt > 0 && time() - $updatedAt > 21600;
    }

    private function cleanupExpired(): void
    {
        $taskDir = $this->taskDir();
        if (! is_dir($taskDir)) {
            return;
        }

        foreach (glob($taskDir . DIRECTORY_SEPARATOR . '*.json') ?: [] as $taskFile) {
            $content = file_get_contents($taskFile);
            $task = is_string($content) ? json_decode($content, true) : null;
            if (! is_array($task) || $this->isExpired($task)) {
                $taskNo = basename($taskFile, '.json');
                @unlink($taskFile);
                @unlink($this->logPath($taskNo));
            }
        }
    }

    private function assertTaskNo(string $taskNo): void
    {
        if (! preg_match('/\Apst_[0-9]{14}_[a-f0-9]{16}\z/', $taskNo)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '终端任务编号格式不正确');
        }
    }

    private function generateTaskNo(): string
    {
        return 'pst_' . date('YmdHis') . '_' . bin2hex(random_bytes(8));
    }

    private function baseDir(): string
    {
        return BASE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'plugin-store-terminal';
    }

    private function taskDir(): string
    {
        return $this->baseDir() . DIRECTORY_SEPARATOR . 'tasks';
    }

    private function logDir(): string
    {
        return $this->baseDir() . DIRECTORY_SEPARATOR . 'logs';
    }

    private function lockDir(): string
    {
        return $this->baseDir() . DIRECTORY_SEPARATOR . 'locks';
    }

    private function taskPath(string $taskNo): string
    {
        return $this->taskDir() . DIRECTORY_SEPARATOR . $taskNo . '.json';
    }

    private function logPath(string $taskNo): string
    {
        return $this->logDir() . DIRECTORY_SEPARATOR . $taskNo . '.jsonl';
    }

    private function lockPath(string $lockName): string
    {
        $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $lockName) ?: 'global';
        return $this->lockDir() . DIRECTORY_SEPARATOR . $safeName . '.lock';
    }

    private function touchLock(array $task): void
    {
        $lockName = $task['lock_name'] ?? null;
        if (! is_string($lockName) || $lockName === '') {
            return;
        }

        $lockPath = $this->lockPath($lockName);
        if (is_file($lockPath)) {
            @touch($lockPath);
        }
    }

    private function ensureDirectories(): void
    {
        foreach ([$this->taskDir(), $this->logDir(), $this->lockDir()] as $dir) {
            if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
                throw new BusinessException(ResultCode::FAIL, '运行时目录创建失败');
            }
        }
    }

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
