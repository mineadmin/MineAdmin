<?php

namespace HyperfTests\Feature\Command;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ApplicationInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;

class ApplicationInstallCommandTest extends TestCase
{
    public function testRun(): void
    {
        $app = ApplicationContext::getContainer()->get(ApplicationInterface::class);
        $app->run(new ArrayInput(['migrate']));
        $app->run(new ArrayInput(['db:seed']));
    }
}