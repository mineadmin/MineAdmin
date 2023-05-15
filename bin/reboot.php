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

/**
 * 强制重启服务脚本，并清理缓存代理类
 */

$pid = shell_exec(sprintf('cat %s/../runtime/hyperf.pid', __DIR__));
$rebootCmd = sprintf('rm -rf %s/../runtime/container/* && php %s/hyperf.php start > /dev/null 2>/dev/null &', __DIR__, __DIR__);

if (shell_exec(sprintf('ps -ef | grep -v grep | grep %s', $pid))) {
    shell_exec("kill -9 {$pid}");
    shell_exec($rebootCmd);
} else {
    shell_exec($rebootCmd);
}
