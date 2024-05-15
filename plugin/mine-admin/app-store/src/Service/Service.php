<?php

namespace Plugin\MineAdmin\AppStore\Service;

use Hyperf\Contract\ApplicationInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use function Hyperf\Support\make;

class Service
{
    public function downloadAndInstall(array $params)
    {
        $command = [
            'command' => 'mine-extension:download',
            'parameters' => [
                'identifier' => $params['identifier'],
                'version' => $params['version']
            ]
        ];
        $input = make(ArrayInput::class, $command);
        $output = make(NullOutput::class);
        $application = container()->get(ApplicationInterface::class);
        $application->setAutoExit(false);
        $result = $application->find($command['command'])->run($input, $output);

    }
}