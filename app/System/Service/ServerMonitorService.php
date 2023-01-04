<?php

declare(strict_types=1);
namespace App\System\Service;

class ServerMonitorService
{
    /**
     * 获取cpu信息
     * @return array
     */
    public function getCpuInfo(): array
    {
        try {
            if (PHP_OS == 'Linux') {
                $cpu = $this->getCpuUsage();
                preg_match('/(\d+)/', shell_exec('cat /proc/cpuinfo | grep "cache size"'), $cache);
            } else {
                preg_match('/(\d+\.\d+)%\suser/', shell_exec('top -l 1 | head -n 10 | grep CPU'), $cpu);
                $cpu = $cpu[1] ?? '未知';
                preg_match('/(\d+)/', shell_exec('system_profiler SPHardwareDataType | grep L2'), $cache);
                $cache = $cache[1] ?? '未知';
            }
            return [
                'name' => $this->getCpuName(),
                'cores' => '物理核心数：' . $this->getCpuPhysicsCores() . '个，逻辑核心数：' . $this->getCpuLogicCores() . '个',
                'cache' => $cache[1] ? $cache[1] / 1024 : 0,
                'usage' => $cpu,
                'free' => 100 - $cpu
            ];
        } catch (\Throwable $e) {
            $res = '无法获取';
            return [
                'name' => $res, 'cores' => $res, 'cache' => $res, 'usage' => $res, 'free' => $res,
            ];
        }
    }

    /**
     * 获取CPU名称
     * @return string
     */
    public function getCpuName(): string
    {
        if (PHP_OS == 'Linux') {
            preg_match('/^\s+\d\s+(.+)/', shell_exec('cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c'), $matches);
            return $matches[1] ?? "未知";
        } else {
            return shell_exec('sysctl -n machdep.cpu.brand_string');
        }
    }

    /**
     * 获取cpu物理核心数
     */
    public function getCpuPhysicsCores(): string
    {
        if (PHP_OS == 'Linux') {
            return str_replace("\n", '', shell_exec('cat /proc/cpuinfo |grep "physical id"|sort |uniq|wc -l'));
        } else {
            return shell_exec('sysctl hw.physicalcpu');
        }
    }

    /**
     * 获取cpu逻辑核心数
     */
    public function getCpuLogicCores(): string
    {
        if (PHP_OS == 'Linux') {
            return str_replace("\n", '', shell_exec('cat /proc/cpuinfo |grep "processor"|wc -l'));
        } else {
            return shell_exec('sysctl hw.logicalcpu');
        }
    }

    /**
     * 获取CPU使用率
     * @return string
     */
    public function getCpuUsage(): string
    {
        $start = $this->calculationCpu();
        sleep(1);
        $end   = $this->calculationCpu();

        $totalStart = $start['total'];
        $totalEnd = $end['total'];

        $timeStart = $start['time'];
        $timeEnd = $end['time'];

        return sprintf('%.2f', ($timeEnd - $timeStart) / ($totalEnd - $totalStart) * 100);
    }

    /**
     * 计算CPU
     * @return array
     */
    protected function calculationCpu(): array
    {
        $mode = '/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/';
        $string = shell_exec('more /proc/stat | grep cpu');
        preg_match_all($mode, $string,$matches);

        $total = $matches[2][0] + $matches[3][0] + $matches[4][0] + $matches[5][0] + $matches[6][0] + $matches[7][0] + $matches[8][0] + $matches[9][0];
        $time  = $matches[2][0] + $matches[3][0] + $matches[4][0] + $matches[6][0] + $matches[7][0] + $matches[8][0] + $matches[9][0];

        return ['total' => $total, 'time' => $time];
    }

    /**
     * 获取内存信息
     * @return array
     */
    public function getMemInfo(): array
    {
        if (PHP_OS == 'Linux') {
            $string = shell_exec('cat /proc/meminfo | grep MemTotal');
            preg_match('/(\d+)/', $string, $total);
            $result['total'] = sprintf('%.2f', $total[1] / 1024 / 1024);

            $string = shell_exec('cat /proc/meminfo | grep MemAvailable');
            preg_match('/(\d+)/', $string, $available);

            $result['free'] = sprintf('%.2f', $available[1] / 1024 / 1024);

            $result['usage'] = sprintf('%.2f', ($total[1] - $available[1]) / 1024 / 1024);

            $result['php'] = round(memory_get_usage() / 1024 / 1024, 2);

            $result['rate'] = sprintf(
                '%.2f', (sprintf('%.2f', $result['usage']) / sprintf('%.2f', $result['total'])) * 100
            );
        } else {
            preg_match('/(\d+)/', shell_exec('system_profiler SPHardwareDataType | grep Memory'), $total);
            $result['total'] = $total[1];
            preg_match('/(\d+)[G|M]\sused/', shell_exec('system_profiler SPHardwareDataType | grep Memory'), $usage);
            $result['usage'] = $usage[1];
            $result['free']  = $result['total'] - $result['usage'];
            $result['php'] = round(memory_get_usage() / 1024 / 1024, 2);
            $result['rate'] = sprintf(
                '%.2f', (sprintf('%.2f', $result['usage']) / sprintf('%.2f', $result['total'])) * 100
            );
        }

        return $result;
    }

    /**
     * 获取PHP及环境信息
     * @return array
     */
    public function getPhpAndEnvInfo(): array
    {
        preg_match('/(\d\.\d\.\d)/', shell_exec('php --ri swoole | grep Version'), $matches);
        $result['swoole_version'] = $matches[1];

        $result['php_version'] = PHP_VERSION;

        $result['os'] = PHP_OS;

        $result['project_path'] = BASE_PATH;

        $result['start_time'] = date('Y-m-d H:i:s', START_TIME);

        $result['run_time'] = \Mine\Helper\Str::Sec2Time(time() - START_TIME);

        $result['mineadmin_version'] = \Mine\Mine::getVersion();

        $result['hyperf_version'] = HF_VERSION;

        return $result;
    }

    /**
     * 获取磁盘信息
     * @return array
     */
    public function getDiskInfo(): array
    {
        $hds = explode(' ', preg_replace(
            '/\s{2,}/',
            ' ',
            shell_exec('df -h | grep -E "^(/)"')
        ));
        return [
            'total' => $hds[1],
            'usage' => $hds[2],
            'free'  => $hds[3],
            'rate'  => $hds[4]
        ];
    }

}
