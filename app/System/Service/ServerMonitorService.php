<?php

declare(strict_types=1);

namespace App\System\Service;

class ServerMonitorService
{
    public function isCygwin() {
        return str_contains(PHP_OS, 'CYGWIN');
    }

    /**
     * 获取cpu信息
     * @return array
     */
    public function getCpuInfo(): array
    {
        try {
            if (PHP_OS == 'Linux') {
                $cpu = $this->getCpuUsage();
                preg_match('/(\d+)/', shell_exec('cat /proc/cpuinfo | grep "cache size"') ?? '', $cache);
                if (count($cache) == 0) {
                    // aarch64 有可能是arm架构
                    $cache = trim(shell_exec("lscpu | grep L3 | awk '{print \$NF}'") ?? '');
                    if ($cache == '') {
                        $cache = trim(shell_exec("lscpu | grep L2 | awk '{print \$NF}'") ?? '');
                    }
                    if ($cache != '') {
                        $cache = [0, intval(str_replace(['K', 'B'], '', strtoupper($cache)))];
                    }
                }
            } else if ($this->isCygwin()) {
                $cpu = shell_exec('wmic cpu get LoadPercentage | findstr /V "LoadPercentage"');
                $cpu = intval(trim($cpu ?? '0'));
                $cache = shell_exec('wmic cpu get L3CacheSize | findstr /V "L3CacheSize"');
                $cache = trim($cache ?? '');
                if ($cache == '') {
                     $cache = shell_exec('wmic cpu get L2CacheSize | findstr /V "L2CacheSize"');
                     $cache = trim($cache ?? '');
                }
                if ($cache != '') {
                    $cache = [0, intval($cache) * 1024];
                }
            } else {
                $cpuUsage = shell_exec('top -l 1 | head -n 10 | grep CPU');
                preg_match('/(\d+\.\d+)%\suser/', $cpuUsage, $cpu);
                $cpu = $cpu[1] ?? '未知';
                preg_match('/(\d+)/', shell_exec('sysctl -n hw.l2cachesize'), $cache);
                $cache = $cache[1] ? $cache : [0, 0];
            }
            return [
                'name' => $this->getCpuName(),
                'cores' => '物理核心数：' . $this->getCpuPhysicsCores() . '个，逻辑核心数：' . $this->getCpuLogicCores() . '个',
                'cache' => $cache[1] ? $cache[1] / 1024 : 0,
                'usage' => $cpu,
                'free' => round(100 - $cpu, 2)
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
            preg_match('/^\s+\d\s+(.+)/', shell_exec('cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c') ?? '', $matches);
            if (count($matches) == 0) {
                // aarch64 有可能是arm架构
                $name = trim(shell_exec("lscpu| grep Architecture | awk '{print $2}'") ?? '');
                if ($name != '') {
                    $mfMhz = trim(shell_exec("lscpu| grep 'MHz' | awk '{print \$NF}' | head -n1") ?? '');
                    $mfGhz = trim(shell_exec("lscpu| grep 'GHz' | awk '{print \$NF}' | head -n1") ?? '');
                    if ($mfMhz == '' && $mfGhz == '') {
                        return $name;
                    } else if ($mfGhz != '') {
                        return $name .' @ ' . $mfGhz .'GHz';
                    } else if ($mfMhz != '') {
                        return $name .' @ ' . round(intval($mfMhz) / 1000, 2) .'GHz';
                    }
                } else {
                    return '未知';
                }
            }
            return $matches[1] ?? "未知";
        } else if ($this->isCygwin()) {
            $name = shell_exec('wmic cpu get Name | findstr /V "Name"');
            return trim($name);
        }else {
            return shell_exec('sysctl -n machdep.cpu.brand_string');
        }
    }

    /**
     * 获取cpu物理核心数
     */
    public function getCpuPhysicsCores(): string
    {
        if (PHP_OS == 'Linux') {
            $num = str_replace("\n", '', shell_exec('cat /proc/cpuinfo |grep "physical id"|sort |uniq|wc -l'));
            return intval($num) == 0 ? '1' : $num;
        }  else if ($this->isCygwin()) {
            $num = shell_exec('wmic cpu get NumberOfCores | findstr /V "NumberOfCores"'); 
            return trim($num ?? '1');
        } else {
            return trim(shell_exec('sysctl -n hw.physicalcpu'));
        }
    }

    /**
     * 获取cpu逻辑核心数
     */
    public function getCpuLogicCores(): string
    {
        if (PHP_OS == 'Linux') {
            return str_replace("\n", '', shell_exec('cat /proc/cpuinfo |grep "processor"|wc -l'));
        } else if ($this->isCygwin()) {
            $num = shell_exec('wmic cpu get NumberOfLogicalProcessors | findstr /V "NumberOfLogicalProcessors"');
            return trim($num ?? '1');
        } else {
            return trim(shell_exec('sysctl -n hw.logicalcpu'));
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
        $end = $this->calculationCpu();

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
        $string = shell_exec('cat /proc/stat | grep cpu');
        preg_match_all($mode, $string, $matches);

        $total = $matches[2][0] + $matches[3][0] + $matches[4][0] + $matches[5][0] + $matches[6][0] + $matches[7][0] + $matches[8][0] + $matches[9][0];
        $time = $matches[2][0] + $matches[3][0] + $matches[4][0] + $matches[6][0] + $matches[7][0] + $matches[8][0] + $matches[9][0];

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
        } else if ($this->isCygwin()) {
            $cap = shell_exec('wmic Path Win32_PhysicalMemory Get Capacity | findstr /V "Capacity"');
            $cap = trim($cap ?? '');
            $total = 0;
            $caps = explode("\n", $cap);
            foreach($caps as $c) {
                $total += intval($c);
            }
            $result['total'] = round($total / 1024 / 1024 / 1024, 2);
            // 可用物理内存
            $free = shell_exec('wmic OS get FreePhysicalMemory | findstr /V "FreePhysicalMemory"');
            $result['free'] = round($free / 1024 / 1024, 2);
            $result['usage'] = round($result['total'] - $result['free'], 2);
            $result['php'] = round(memory_get_usage() / 1024 / 1024, 2);
            $result['rate'] = sprintf(
                '%.2f', (sprintf('%.2f', $result['usage']) / sprintf('%.2f', $result['total'])) * 100
            );
        } else {
            preg_match('/(\d+)/', shell_exec('system_profiler SPHardwareDataType | grep Memory'), $total);
            $result['total'] = $total[1];
            $usage = shell_exec("ps -caxm -orss | awk '{ mem += $1} END {print mem}'"); // 单位KB
            $result['usage'] = round($usage / 1024 / 1024, 2);
            $result['free'] = round($result['total'] - $result['usage'], 2);
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
        preg_match('/(\d\.\d\.\d)/', swoole_version(), $matches);
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
        if ($this->isCygwin()) {
            $ds = explode('/', __DIR__);
            // 只看项目所在磁盘
            $driver = $ds[2].':/';
            $info = shell_exec("fsutil volume diskfree $driver");
            $info = trim($info ?? '');
            if ($info != '') {
                $lines = explode("\n", $info);
                $i = 0;
                $free = 0;
                $total = 0;
                foreach($lines as $line) {
                    $c = explode(':', $line);
                    $tmp = explode('(', $c[1]);
                    if ($i == 0) {
                        $free = intval(str_replace(',', '', $tmp[0]));
                    } else if ($i == 1) {
                        $total = intval(str_replace(',', '', $tmp[0]));
                    } else {
                        continue;
                    }
                    $i++;
                } 

                $usage = $total - $free;
                return [
                    'total' => round($total / 1024 / 1024 / 1024, 2),
                    'usage' => round($usage / 1024 / 1024 / 1024, 2),
                    'free' => round($free / 1024 / 1024 / 1024, 2),
                    'rate' => round($usage / $total * 100, 2),
                ];
            }
            return [
                'total' => 0,
                'usage' => 0,
                'free' => 0,
                'rate' => 0,
            ];
        } else {
            $hds = explode(' ', preg_replace(
                '/\s{2,}/',
                ' ',
                shell_exec('df -h | grep -E "^(/)"')
            ));
            return [
                'total' => $hds[1],
                'usage' => $hds[2],
                'free' => $hds[3],
                'rate' => $hds[4]
            ];
        }
    }

}
