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
use App\System\Model\SystemUser;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

test('initial super admin test', function () {
    $command = 'initial:super-admin';
    $params = [
        'command' => $command,
    ];
    $input = new ArrayInput($params);
    $output = new NullOutput();
    $container = ApplicationContext::getContainer();
    $application = $container->get(ApplicationInterface::class);
    $application->setAutoExit(false);
    $result = $application->run($input, $output);
    expect($result)->toBe(0);

    $params = [
        'command' => $command,
        '--yes' => true,
    ];
    $input = new ArrayInput($params);
    $output = new NullOutput();
    $container = ApplicationContext::getContainer();
    $application = $container->get(ApplicationInterface::class);
    $application->setAutoExit(false);
    $result = $application->find($command)->run($input, $output);
    expect($result)->toBe(0);
    $user = SystemUser::query()->whereKey(env('SUPER_ADMIN'))->first();
    $user->refresh();
    expect(password_verify('12345678', $user->password))->toBeTrue();
});
