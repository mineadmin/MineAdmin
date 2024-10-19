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
import { graphic } from 'echarts'
import { useEcharts } from '@/hooks/useEcharts.ts'

const echartsLogin = ref()

function graphicFactory(side) {
  return {
    type: 'text',
    bottom: '8',
    ...side,
    style: {
      text: '',
      textAlign: 'center',
      fill: '#4E5969',
      fontSize: 12,
    },
  }
}

const xAxis = ref([
  '2022-07-06',
  '2022-07-07',
  '2022-07-08',
  '2022-07-09',
  '2022-07-10',
  '2022-07-11',
  '2022-07-12',
  '2022-07-13',
  '2022-07-14',
  '2022-07-15',
])
const chartsData = ref([32, 56, 61, 89, 12, 33, 56, 92, 180, 25])
const graphicElements = ref([
  graphicFactory({ left: '2.6%' }),
  graphicFactory({ right: 0 }),
])

const loginChartOptions = ref({
  grid: {
    left: '2.6%',
    right: '0',
    top: '10',
    bottom: '30',
  },
  xAxis: {
    type: 'category',
    offset: 2,
    data: xAxis.value,
    boundaryGap: false,
    axisLabel: {
      color: '#4E5969',
      formatter(value, idx) {
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
      show: true,
      interval: (idx) => {
        if (idx === 0) {
          return false
        }
        if (idx === xAxis.value.length - 1) {
          return false
        }
        return true
      },
      lineStyle: {
        color: '#E5E8EF',
      },
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
      formatter(value, idx) {
        if (idx === 0) {
          return value
        }
        return `${value}`
      },
    },
    splitLine: {
      show: true,
      lineStyle: {
        type: 'dashed',
        color: '#E5E8EF',
      },
    },
  },
  tooltip: {
    trigger: 'axis',
    formatter(params) {
      return `<div class="echarts-tooltip-diy">
        <p class="tooltip-title">${params[0].axisValueLabel}</p>
        <div class="content-panel"><span>登录次数：</span><span class="tooltip-value">${Number(
    params[0].value,
  ).toLocaleString()}</span></div>
      </div>`
    },
    className: 'echarts-tooltip-diy',
  },
  graphic: {
    elements: graphicElements.value,
  },
  series: [
    {
      data: chartsData.value,
      type: 'line',
      smooth: true,
      symbolSize: 12,
      emphasis: {
        focus: 'series',
        itemStyle: {
          borderWidth: 2,
        },
      },
      lineStyle: {
        width: 3,
        color: new graphic.LinearGradient(0, 0, 1, 0, [
          {
            offset: 0,
            color: 'rgba(30, 231, 255, 1)',
          },
          {
            offset: 0.5,
            color: 'rgba(36, 154, 255, 1)',
          },
          {
            offset: 1,
            color: 'rgba(111, 66, 251, 1)',
          },
        ]),
      },
      showSymbol: false,
      areaStyle: {
        opacity: 0.8,
        color: new graphic.LinearGradient(0, 0, 0, 1, [
          {
            offset: 0,
            color: 'rgba(17, 126, 255, 0.16)',
          },
          {
            offset: 1,
            color: 'rgba(17, 128, 255, 0)',
          },
        ]),
      },
    },
  ],
})

useEcharts(echartsLogin).setOption(loginChartOptions.value)
</script>

<template>
  <div class="mine-card">
    <div class="text-base">
      最近登录次数
    </div>
    <div ref="echartsLogin" class="mt-5 h-380px" />
  </div>
</template>
