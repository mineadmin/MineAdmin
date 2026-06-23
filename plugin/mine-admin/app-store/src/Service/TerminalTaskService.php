<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\AppStore\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Http\CurrentUser;
use Hyperf\Coroutine\Coroutine;
use Plugin\MineAdmin\AppStore\Enum\TerminalAction;
use Plugin\MineAdmin\AppStore\Support\PluginIdentifier;

class TerminalTaskService
{
    public function __construct(
        private readonly TerminalRuntimeStore $store,
        private readonly TerminalCommandService $commandService,
        private readonly PluginIdentifier $identifier,
        private readonly CurrentUser $currentUser
    ) {}

    public function create(array $payload): array
    {
        $action = TerminalAction::tryFrom((string) ($payload['action'] ?? ''));
        if (! $action instanceof TerminalAction) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, '终端动作不支持');
        }

        $this->assertActionPermission($action);

        $identifier = null;
        if ($action->requiresIdentifier()) {
            $identifier = $this->identifier->normalize($payload['identifier'] ?? null);
        } elseif (! empty($payload['identifier'])) {
            $identifier = $this->identifier->normalize($payload['identifier']);
        }

        $version = $this->identifier->normalizeVersion(
            $payload['version'] ?? null,
            in_array($action, [TerminalAction::Download, TerminalAction::Install, TerminalAction::Uninstall], true)
        );

        $task = $this->store->create([
            'action' => $action->value,
            'title' => $action->label(),
            'identifier' => $identifier,
            'version' => $version,
            'created_by' => $this->currentUser->id(),
        ]);

        Coroutine::create(fn () => $this->commandService->execute($task['task_no']));

        return [
            'task_no' => $task['task_no'],
            'status' => $task['status'],
            'poll_interval' => 1000,
        ];
    }

    public function status(string $taskNo): array
    {
        return $this->store->getTask($taskNo);
    }

    public function logs(string $taskNo, int $afterSeq = 0, int $limit = 200): array
    {
        return $this->store->logs($taskNo, $afterSeq, $limit);
    }

    public function cancel(string $taskNo): array
    {
        $this->assertPermission('plugin:store:terminal:cancel');
        return $this->store->requestCancel($taskNo);
    }

    private function assertActionPermission(TerminalAction $action): void
    {
        $this->assertPermission($action->permissionCode());
    }

    private function assertPermission(string $permission): void
    {
        $user = $this->currentUser->user();
        if ($user->isSuperAdmin()) {
            return;
        }

        if (! $user->hasPermission($permission)) {
            throw new BusinessException(ResultCode::FORBIDDEN);
        }
    }
}
