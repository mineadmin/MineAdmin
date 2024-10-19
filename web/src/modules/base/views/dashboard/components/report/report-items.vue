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
import getReportData from '../../datas/report.ts'
import { useEcharts } from '@/hooks/useEcharts.ts'

const { title = '', quota = '', chartType = '' } = defineProps<{
  title?: string
  quota?: string
  chartType?: string
}>()

const isDark = useColorMode()
const count = ref(0)
const growth = ref(100)
const isUp = computed(() => growth.value > 50)
const chartData = ref<any>([])
const echartReportItem = ref()

const { getItemData } = getReportData()

const data = getItemData(quota)
const { chartData: resChartData } = data
count.value = data.count
growth.value = data.growth
resChartData.data.value.forEach((el, idx) => {
  if (chartType === 'bar') {
    chartData.value.push({
      value: el,
      itemStyle: {
        color: idx % 2 ? '#468DFF' : '#86DF6C',
      },
    })
  }
  else {
    chartData.value.push(el)
  }
})

const chartOption = ref({
  grid: {
    left: 0,
    right: 0,
    top: 0,
    bottom: 0,
  },
  xAxis: {
    type: 'category',
    show: false,
  },
  yAxis: {
    show: false,
  },
  tooltip: {
    show: true,
    trigger: 'axis',
    formatter: '{c}',
  },
  series: [
    {
      data: chartData.value,
      ...(chartType === 'bar'
        ? {
            type: 'bar',
            barWidth: 7,
            barGap: '0',
          }
        : {
            type: 'line',
            showSymbol: false,
            smooth: true,
            lineStyle: {
              color: '#4080FF',
            },
          }),
    },
  ],
})

useEcharts(echartReportItem).setOption(chartOption.value)
</script>

<template>
  <div class="mine-card h-150px" v-bind="$attrs">
    <div class="text-base">
      {{ title }}
    </div>
    <div class="content-wrap">
      <div class="content">
        <el-statistic :value="count" />
        <div class="desc mt-2">
          <div
            class="flex items-center" :class="{
              'text-red-5': isUp,
              'text-green-6': !isUp,
            }"
          >
            {{ growth }}%
            <ma-svg-icon v-if="isUp" name="ic:baseline-arrow-upward" />
            <ma-svg-icon v-else name="ic:baseline-arrow-downward" />
          </div>
        </div>
      </div>
      <div class="chart">
        <div ref="echartReportItem" class="mt-5 h-100px" />
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.content-wrap {
  @apply
  flex gap-x-3 items-center
  ;

  .chart {
    @apply w-[calc(100%-80px)];
  }
}
</style>
