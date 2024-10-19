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
const echartsContentClassify = ref()

const chartOption = {
  grid: {
    left: 0,
    right: 0,
    top: 0,
    bottom: 20,
  },
  legend: {
    show: true,
    top: 'center',
    right: '0',
    orient: 'vertical',
    icon: 'circle',
    itemWidth: 10,
    itemHeight: 10,
    itemGap: 20,
    textStyle: {
      color: isDark.value === 'dark' ? '#ffffff' : '#4E5969',
    },
  },
  radar: {
    center: ['40%', '50%'],
    radius: 80,
    indicator: [
      { name: '国际', max: 6500 },
      { name: '财经', max: 22000 },
      { name: '科技', max: 30000 },
      { name: '其他', max: 38000 },
      { name: '体育', max: 52000 },
      { name: '娱乐', max: 25000 },
    ],
    axisName: {
      color: isDark.value === 'dark' ? '#ffffff' : '#1D2129',
    },
    axisLine: {
      lineStyle: {
        color: isDark.value === 'dark' ? '#484849' : '#E5E6EB',
      },
    },
    splitLine: {
      lineStyle: {
        color: isDark.value === 'dark' ? '#484849' : '#E5E6EB',
      },
    },
    splitArea: {
      areaStyle: {
        color: [],
      },
    },
  },
  series: [
    {
      type: 'radar',
      areaStyle: {
        opacity: 0.2,
      },
      data: [
        {
          value: [4850, 19000, 19000, 29500, 35200, 20000],
          name: '纯文本',
          symbol: 'none',
          itemStyle: {
            color: isDark.value === 'dark' ? '#6CAAF5' : '#249EFF',
          },
        },
        {
          value: [2250, 17000, 21000, 23500, 42950, 22000],
          name: '图文类',
          symbol: 'none',
          itemStyle: {
            color: isDark.value === 'dark' ? '#A079DC' : '#313CA9',
          },
        },
        {
          value: [5850, 11000, 26000, 27500, 46950, 18000],
          name: '视频类',
          symbol: 'none',
          itemStyle: {
            color: isDark.value === 'dark' ? '#3D72F6' : '#21CCFF',
          },
        },
      ],
    },
  ],
}

useEcharts(echartsContentClassify).setOption(chartOption)
</script>

<template>
  <div class="mine-card h-300px !ml-3 !lg:ml-0">
    <div class="text-base">
      内容题材分布
    </div>
    <div ref="echartsContentClassify" class="mt-5 h-[250px]" />
  </div>
</template>

<style scoped lang="scss">

</style>
