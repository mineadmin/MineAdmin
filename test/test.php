<?php
$fp = popen('top -b -n 1 | grep -E "^(%Cpu|Cpu|CPU|Mem)"',"r");
$content = '';
while(!feof($fp)) {
    $content .= fread($fp, 1024);
}
fclose($fp);

$sys_info = explode("\n",$content);

$cpu_info = explode(",",$sys_info[1]); //CPU占有量 数组
$mem_info = explode(",",$sys_info[0]); //内存占有量 数组

//CPU占有量
if (preg_match('/[\%Cpu|Cpu|CPU]\:\s+(\d+)%/', $sys_info[1], $matche)) {
    print_r($matche);
}

