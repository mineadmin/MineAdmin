<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Support;

use Symfony\Component\Process\Process;

final class SafeProcessRunner
{
    public function __construct(
        private readonly SensitiveOutputMasker $masker
    ) {}

    public function run(
        array $command,
        string $workingDirectory,
        callable $onOutput,
        callable $shouldCancel,
        int $timeout = 600,
        array $environment = [],
        ?callable $onHeartbeat = null
    ): int {
        $process = new Process(
            $command,
            $workingDirectory,
            $this->safeEnvironment($environment),
            null,
            $timeout
        );
        $process->setIdleTimeout(120);
        $process->start();

        $pid = $process->getPid();
        if (is_int($pid)) {
            $onOutput('system', sprintf('进程已启动，PID: %d', $pid), $pid);
        }

        while ($process->isRunning()) {
            $process->checkTimeout();
            if ($shouldCancel()) {
                $process->stop(3, 15);
                $onOutput('system', '进程已收到取消信号');
                return 130;
            }

            $this->flushOutput($process, $onOutput);
            $onHeartbeat && $onHeartbeat();
            usleep(200000);
        }

        $this->flushOutput($process, $onOutput);

        return $process->getExitCode() ?? 0;
    }

    public function findBinary(string $name): string
    {
        $candidate = trim($name);
        if ($candidate === '' || str_contains($candidate, "\0")) {
            throw new \RuntimeException('命令路径不正确');
        }

        if (preg_match('/[;&|`$<>]/', $candidate)) {
            throw new \RuntimeException('命令路径包含不安全字符');
        }

        return $candidate;
    }

    private function splitLines(string $data): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $data);
        if (! is_array($lines)) {
            return [$data];
        }

        return array_values(array_filter($lines, static fn (string $line): bool => $line !== ''));
    }

    private function flushOutput(Process $process, callable $onOutput): void
    {
        foreach ([
            'stdout' => $process->getIncrementalOutput(),
            'stderr' => $process->getIncrementalErrorOutput(),
        ] as $stream => $data) {
            if ($data === '') {
                continue;
            }
            foreach ($this->splitLines($data) as $line) {
                $onOutput($stream, $this->masker->mask($line));
            }
        }
    }

    private function safeEnvironment(array $environment): array
    {
        $allowed = [
            'PATH',
            'Path',
            'HOME',
            'USERPROFILE',
            'COMPOSER_HOME',
            'COMPOSER_CACHE_DIR',
            'PNPM_HOME',
            'NPM_CONFIG_CACHE',
            'CI',
        ];

        $safe = [];
        foreach ($allowed as $key) {
            $value = getenv($key);
            if ($value !== false) {
                $safe[$key] = $value;
            }
        }

        foreach ($environment as $key => $value) {
            if (in_array($key, $allowed, true) && is_scalar($value)) {
                $safe[$key] = (string) $value;
            }
        }

        return $safe;
    }
}
