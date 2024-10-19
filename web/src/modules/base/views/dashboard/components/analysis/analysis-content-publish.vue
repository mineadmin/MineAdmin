<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { CallbackDataParams } from 'echarts/types/dist/shared'
import { useColorMode } from '@vueuse/core'
import getAnalysisData from '~/base/views/dashboard/datas/analysis.ts'
import { useEcharts } from '@/hooks/useEcharts.ts'

const dater = ref<string>('today')
const xAxis = ref<string[]>([])
const textChartsData = ref<number[]>([])
const imgChartsData = ref<number[]>([])
const videoChartsData = ref<number[]>([])
const echarts = ref<HTMLDivElement>()

const isDark = useColorMode()

export interface ToolTipFormatterParams extends CallbackDataParams {
  axisDim: string
  axisIndex: number
  axisType: string
  axisId: string
  axisValue: string
  axisValueLabel: string
}

const { contentPublishRadio } = getAnalysisData()

const chartData = contentPublishRadio()
xAxis.value = chartData[0].x
chartData.forEach((el: ContentPublishRecord) => {
  if (el.name === '纯文本') {
    textChartsData.value = el.y
  }
  else if (el.name === '图文类') {
    imgChartsData.value = el.y
  }
  videoChartsData.value = el.y
})

function tooltipItemsRender(items: ToolTipFormatterParams[]) {
  return items.map(el =>
    `<div class="content-panel">
      <p>
        <span style="background-color: ${el.color}" class="tooltip-item-icon"></span>
      <span>
        ${el.seriesName}
      </span>
    </p>
    <span class="tooltip-value">
      ${Number(el.value).toLocaleString()}
    </span>
  </div>`,
  ).join('')
}

const options = computed(() => {
  return {
    grid: {
      left: '4%',
      right: 0,
      top: '20',
      bottom: '60',
    },
    legend: {
      bottom: 0,
      icon: 'circle',
      textStyle: {
        color: '#4E5969',
      },
    },
    xAxis: {
      type: 'category',
      data: xAxis.value,
      axisLine: {
        lineStyle: {
          color: isDark.value === 'dark' ? '#3f3f3f' : '#A9AEB8',
        },
      },
      axisTick: {
        show: true,
        alignWithLabel: true,
        lineStyle: {
          color: '#86909C',
        },
      },
      axisLabel: {
        color: '#86909C',
      },
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        color: '#86909C',
        formatter(value: number, idx: number) {
          if (idx === 0) {
            return `${value}`
          }
          return `${value / 1000}k`
        },
      },
      splitLine: {
        lineStyle: {
          color: isDark.value === 'dark' ? '#3F3F3F' : '#E5E6EB',
        },
      },
    },
    tooltip: {
      show: true,
      trigger: 'axis',
      formatter(params) {
        const [firstElement] = params as ToolTipFormatterParams[]
        return `
          <div>
            <p class="tooltip-title">
              ${firstElement.axisValueLabel}
            </p>
            ${tooltipItemsRender(params as ToolTipFormatterParams[])}
          </div>
        `
      },
      className: 'echarts-tooltip-diy',
    },
    series: [
      {
        name: '纯文本',
        data: textChartsData.value,
        stack: 'one',
        type: 'bar',
        barWidth: 16,
        color: isDark.value === 'dark' ? '#4A7FF7' : '#246EFF',
      },
      {
        name: '图文类',
        data: imgChartsData.value,
        stack: 'one',
        type: 'bar',
        color: isDark.value === 'dark' ? '#085FEF' : '#00B2FF',
      },
      {
        name: '视频类',
        data: videoChartsData.value,
        stack: 'one',
        type: 'bar',
        color: isDark.value === 'dark' ? '#01349F' : '#81E2FF',
        itemStyle: {
          borderRadius: 2,
        },
      },
    ],
  }
})

useEcharts(echarts).setOption(options.value)
</script>

<template>
  <div class="mine-card w-auto xl:w-8/12">
    <div class="flex items-center justify-between text-base">
      <div>内容发布比例</div>
      <div class="w-180px">
        <m-tabs
          v-model="dater"
          class="h-7 w-full text-sm"
          :options="[
            { label: '一周内', value: 'week' },
            { label: '昨日', value: 'yesterday' },
            { label: '今日', value: 'today' },
          ]"
        />
      </div>
    </div>
    <div ref="echarts" class="mt-5 h-300px" />
  </div>
</template>

<style scoped lang="scss">

</style>
