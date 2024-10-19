<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { useColorMode } from '@vueuse/core'
import { useEcharts } from '@/hooks/useEcharts.ts'

const isDark = useColorMode()
const echartsUserAction = ref()

const chartOption = {
  grid: {
    left: 44,
    right: 20,
    top: 0,
    bottom: 20,
  },
  xAxis: {
    type: 'value',
    axisLabel: {
      show: true,
      formatter(value: number, idx: number) {
        if (idx === 0) {
          return String(value)
        }
        return `${Number(value) / 1000}k`
      },
    },
    splitLine: {
      lineStyle: {
        color: isDark.value === 'dark' ? '#484849' : '#E5E8EF',
      },
    },
  },
  yAxis: {
    type: 'category',
    data: ['点赞量', '评论量', '分享量'],
    axisLabel: {
      show: true,
      color: '#4E5969',
    },
    axisTick: {
      show: true,
      length: 2,
      lineStyle: {
        color: '#A9AEB8',
      },
      alignWithLabel: true,
    },
    axisLine: {
      lineStyle: {
        color: isDark.value === 'dark' ? '#484849' : '#A9AEB8',
      },
    },
  },
  tooltip: {
    show: true,
    trigger: 'axis',
  },
  series: [
    {
      data: [1033, 1244, 1520],
      type: 'bar',
      barWidth: 7,
      itemStyle: {
        color: '#4086FF',
        borderRadius: 4,
      },
    },
  ],
}

useEcharts(echartsUserAction).setOption(chartOption)
</script>

<template>
  <div class="mine-card h-200px !ml-3 !lg:ml-0">
    <div class="text-base">
      今日转评赞统计
    </div>
    <div ref="echartsUserAction" class="mt-5 h-[150px]" />
  </div>
</template>

<style scoped lang="scss">

</style>
