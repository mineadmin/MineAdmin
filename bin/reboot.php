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

declare(strict_types=1);


/*
 * 强制重启服务脚本，并清理缓存代理类
 */
$env = isset($argv[1]) ? $argv[1] : 'dev';

$httpPort = isset($argv[2]) ? $argv[2] : 9501;
$messagePort = isset($argv[3]) ? $argv[3] : 9502;

killHyperfPid();
killHttpPort($httpPort);
killWebsocketPort($messagePort);

startService($env);

function startService($env)
{
    echo "启动{$env}服务\n";
    if ($env == 'dev'){
        $rebootCmd = sprintf('php %s/hyperf.php server:watch  > /dev/tty', __DIR__);
        shell_exec($rebootCmd);
    }else{
        $rebootCmd = sprintf('php %s/hyperf.php start > /dev/null', __DIR__);
        shell_exec($rebootCmd);
    }
}

function killHyperfPid()
{
    echo "执行killHyperfPid中\n";
    $pid = shell_exec(sprintf('cat %s/../runtime/hyperf.pid', __DIR__));
    $rebootCmd = sprintf('rm -rf %s/../runtime/container/*', __DIR__);
    //    $rebootCmd = sprintf('rm -rf %s/../runtime/container/* && php %s/hyperf.php start > /dev/null 2>/dev/null &', __DIR__, __DIR__);

    if (shell_exec(sprintf('ps -ef | grep -v grep | grep %s', $pid))) {
        shell_exec("kill -9 {$pid}");
    }
    echo "执行killHyperfPid完成\n";

    echo "执行清理缓存代理中\n";
    shell_exec($rebootCmd);
    echo "执行清理缓存代理成功\n";
}

function killWebsocketPort($port = 9502)
{
    echo "执行killWebsocketPort中\n";

    $command = 'lsof -t -i:' . $port;
    $output = shell_exec($command);

    if ($output) {
        $pidArray = explode("\n", trim($output));

        $pidList = array_filter($pidArray, 'strlen');

        foreach ($pidList as $pid) {
            if (is_numeric($pid)) {
                shell_exec("kill -9 {$pid}");
                echo __FUNCTION__ . ":{$port}端口进程 {$pid} 已杀死\n";
            }
        }
    }

    echo "执行killWebsocketPort完成\n";
}

function killHttpPort($port = 9501)
{
    echo "执行killHttpPort中\n";

    $command = 'lsof -t -i:' . $port;
    $output = shell_exec($command);

    if ($output) {
        $pidArray = explode("\n", trim($output));

        $pidList = array_filter($pidArray, 'strlen');

        foreach ($pidList as $pid) {
            if (is_numeric($pid)) {
                shell_exec("kill -9 {$pid}");
                echo __FUNCTION__ . ":{$port}端口进程 {$pid} 已杀死\n";
            }
        }
    }

    echo "执行killHttpPort完成\n";
}
