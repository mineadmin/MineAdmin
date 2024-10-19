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

const { contentPeriod } = getAnalysisData()

const chartData = contentPeriod()
xAxis.value = chartData.xAxis
chartData.data.forEach((el) => {
  if (el.name === '纯文本') {
    textChartsData.value = el.value
  }
  else if (el.name === '图文类') {
    imgChartsData.value = el.value
  }
  videoChartsData.value = el.value
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
      left: '40',
      right: 0,
      top: '20',
      bottom: '100',
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
      boundaryGap: false,
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
        interval(idx: number) {
          if (idx === 0) {
            return false
          }
          return idx !== xAxis.value.length - 1
        },
      },
      axisLabel: {
        color: '#86909C',
        formatter(value: number, idx: number) {
          if (idx === 0) {
            return ''
          }
          if (idx === xAxis.value.length - 1) {
            return ''
          }
          return `${value}`
        },
      },
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        color: '#86909C',
        formatter: '{value}%',
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
        return `<div>
            <p class="tooltip-title">${firstElement.axisValueLabel}</p>
            ${tooltipItemsRender(params as ToolTipFormatterParams[])}
          </div>`
      },
      className: 'echarts-tooltip-diy',
    },
    series: [
      {
        name: '纯文本',
        data: textChartsData.value,
        type: 'line',
        smooth: true,
        showSymbol: false,
        color: isDark.value === 'dark' ? '#3D72F6' : '#246EFF',
        symbol: 'circle',
        symbolSize: 10,
        emphasis: {
          focus: 'series',
          itemStyle: {
            borderWidth: 2,
            borderColor: '#E0E3FF',
          },
        },
      },
      {
        name: '图文类',
        data: imgChartsData.value,
        type: 'line',
        smooth: true,
        showSymbol: false,
        color: isDark.value === 'dark' ? '#A079DC' : '#00B2FF',
        symbol: 'circle',
        symbolSize: 10,
        emphasis: {
          focus: 'series',
          itemStyle: {
            borderWidth: 2,
            borderColor: '#E2F2FF',
          },
        },
      },
      {
        name: '视频类',
        data: videoChartsData.value,
        type: 'line',
        smooth: true,
        showSymbol: false,
        color: isDark.value === 'dark' ? '#6CAAF5' : '#81E2FF',
        symbol: 'circle',
        symbolSize: 10,
        emphasis: {
          focus: 'series',
          itemStyle: {
            borderWidth: 2,
            borderColor: '#D9F6FF',
          },
        },
      },
    ],
    dataZoom: [
      {
        bottom: 40,
        type: 'slider',
        left: 40,
        right: 14,
        height: 14,
        borderColor: 'transparent',
        handleIcon:
          'image://http://p3-armor.byteimg.com/tos-cn-i-49unhts6dw/1ee5a8c6142b2bcf47d2a9f084096447.svg~tplv-49unhts6dw-image.image',
        handleSize: '20',
        handleStyle: {
          shadowColor: 'rgba(0, 0, 0, 0.2)',
          shadowBlur: 4,
        },
        brushSelect: false,
        backgroundColor: isDark.value === 'dark' ? '#313132' : '#F2F3F5',
      },
      {
        type: 'inside',
        start: 0,
        end: 100,
        zoomOnMouseWheel: false,
      },
    ],
  }
})

useEcharts(echarts).setOption(options.value)
</script>

<template>
  <div class="mine-card">
    <div class="flex items-center justify-between text-base">
      <div>内容时段分析</div>
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
