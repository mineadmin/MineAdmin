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
import getReportData from '~/base/views/dashboard/datas/report.ts'
import { useEcharts } from '@/hooks/useEcharts.ts'

const isDark = useColorMode()
const xAxis = ref<string[]>([])
const contentProductionData = ref<number[]>([])
const contentClickData = ref<number[]>([])
const contentExposureData = ref<number[]>([])
const activeUsersData = ref<number[]>([])
const echartsOverview = ref<HTMLDivElement>()

function tooltipItemsHtmlString(items: any[]) {
  return items
    .map(
      el => `<div class="content-panel">
        <p>
          <span style="background-color: ${
  el.color
}" class="tooltip-item-icon"></span><span>${el.seriesName}</span>
        </p>
        <span class="tooltip-value">${el.value.toLocaleString()}</span>
      </div>`,
    )
    .reverse()
    .join('')
}

const renderData = computed(() => [
  {
    title: '内容生产量',
    value: 1902,
    prefix: {
      icon: 'heroicons:pencil-square',
      background: isDark.value === 'dark' ? '#593E2F' : '#FFE4BA',
      iconColor: isDark.value === 'dark' ? '#F29A43' : '#F77234',
    },
  },
  {
    title: '内容点击量',
    value: 2445,
    prefix: {
      icon: 'heroicons:hand-thumb-up',
      background: isDark.value === 'dark' ? '#3D5A62' : '#E8FFFB',
      iconColor: isDark.value === 'dark' ? '#6ED1CE' : '#33D1C9',
    },
  },
  {
    title: '内容曝光量',
    value: 3034,
    prefix: {
      icon: 'heroicons:heart-16-solid',
      background: isDark.value === 'dark' ? '#354276' : '#E8F3FF',
      iconColor: isDark.value === 'dark' ? '#4A7FF7' : '#165DFF',
    },
  },
  {
    title: '活跃用户数',
    value: 1275,
    prefix: {
      icon: 'heroicons:user-16-solid',
      background: isDark.value === 'dark' ? '#3F385E' : '#F5E8FF',
      iconColor: isDark.value === 'dark' ? '#8558D3' : '#722ED1',
    },
  },
])

function generateSeries(name: string,
  lineColor: string,
  itemBorderColor: string,
  data: number[]) {
  return {
    name,
    data,
    stack: 'Total',
    type: 'line',
    smooth: true,
    symbol: 'circle',
    symbolSize: 10,
    itemStyle: {
      color: lineColor,
    },
    emphasis: {
      focus: 'series',
      itemStyle: {
        color: lineColor,
        borderWidth: 2,
        borderColor: itemBorderColor,
      },
    },
    lineStyle: {
      width: 2,
      color: lineColor,
    },
    showSymbol: false,
    areaStyle: {
      opacity: 0.1,
      color: lineColor,
    },
  }
}

const { getOverviewData } = getReportData()
const data = getOverviewData()
xAxis.value = data.xAxis
data.data.forEach((el) => {
  if (el.name === '内容生产量') {
    contentProductionData.value = el.value
  }
  else if (el.name === '内容点击量') {
    contentClickData.value = el.value
  }
  else if (el.name === '内容曝光量') {
    contentExposureData.value = el.value
  }
  activeUsersData.value = el.value
})

const chartOption = {
  grid: {
    left: '2.6%',
    right: '4',
    top: '40',
    bottom: '40',
  },
  xAxis: {
    type: 'category',
    offset: 2,
    data: xAxis.value,
    boundaryGap: false,
    axisLabel: {
      color: '#4E5969',
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
    axisLine: {
      show: false,
    },
    axisTick: {
      show: false,
    },
    splitLine: {
      show: false,
    },
    axisPointer: {
      show: true,
      lineStyle: {
        color: '#23ADFF',
        width: 2,
      },
    },
  },
  yAxis: {
    type: 'value',
    axisLine: {
      show: false,
    },
    axisLabel: {
      formatter(value: number, idx: number) {
        if (idx === 0) {
          return String(value)
        }
        return `${value / 1000}k`
      },
    },
    splitLine: {
      lineStyle: {
        color: isDark.value === 'dark' ? '#2E2E30' : '#F2F3F5',
      },
    },
  },
  tooltip: {
    trigger: 'axis',
    formatter(params) {
      const [firstElement] = params as any[]
      return `<div>
            <p class="tooltip-title">${firstElement.axisValueLabel}</p>
            ${tooltipItemsHtmlString(params as any[])}
          </div>`
    },
    className: 'echarts-tooltip-diy',
  },
  graphic: {
    elements: [
      {
        type: 'text',
        left: '2.6%',
        bottom: '18',
        style: {
          text: '12.10',
          textAlign: 'center',
          fill: '#4E5969',
          fontSize: 12,
        },
      },
      {
        type: 'text',
        right: '0',
        bottom: '18',
        style: {
          text: '12.17',
          textAlign: 'center',
          fill: '#4E5969',
          fontSize: 12,
        },
      },
    ],
  },
  series: [
    generateSeries(
      '内容生产量',
      '#722ED1',
      '#F5E8FF',
      contentProductionData.value,
    ),
    generateSeries(
      '内容点击量',
      '#F77234',
      '#FFE4BA',
      contentClickData.value,
    ),
    generateSeries(
      '内容曝光量',
      '#33D1C9',
      '#E8FFFB',
      contentExposureData.value,
    ),
    generateSeries(
      '活跃用户数',
      '#3469FF',
      '#E8F3FF',
      activeUsersData.value,
    ),
  ],
}

useEcharts(echartsOverview).setOption(chartOption)
</script>

<template>
  <div class="mine-card w-auto p-3 xl:w-8/12">
    <div class="text-base">
      报表总览
    </div>
    <div class="grid grid-cols-2 mt-6 gap-y-3 md:grid-cols-4">
      <template v-for="(item, idx) in renderData" :key="idx">
        <div class="content flex gap-3">
          <div
            class="h-[50px] w-[50px] flex-center rounded-md p-1"
            :style="{ background: item.prefix.background }"
          >
            <ma-svg-icon
              :name="item.prefix.icon"
              :style="{ color: item.prefix.iconColor }"
              :size="50"
            />
          </div>
          <el-statistic :value="item.value">
            <template #title>
              <div class="text-base">
                {{ item.title }}
              </div>
            </template>
          </el-statistic>
        </div>
      </template>
    </div>
    <div ref="echartsOverview" class="mt-5 h-400px" />
  </div>
</template>

<style scoped lang="scss">

</style>
