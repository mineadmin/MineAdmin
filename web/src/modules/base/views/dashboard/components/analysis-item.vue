<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script lang="ts" setup>
import getAnalysisData from '../datas/analysis.ts'
import { themeMode, useEcharts } from '@/hooks/useEcharts.ts'

const props = withDefaults(defineProps<{
  name: string
}>(), {
  name: 'visitors',
})

const renderData = ref<Record<string, any>>({
  count: 0,
  growth: 0,
  chartData: [],
})

const chartOption = ref({})

function lineChartOptionsFactory() {
  const data = ref<number[][]>([[], []])
  const chartOption = {
    grid: {
      left: 0,
      right: 0,
      top: 10,
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
    },
    series: [
      {
        name: '2001',
        data: data.value[0],
        type: 'line',
        showSymbol: false,
        smooth: true,
        lineStyle: {
          color: '#165DFF',
          width: 3,
        },
      },
      {
        name: '2002',
        data: data.value[1],
        type: 'line',
        showSymbol: false,
        smooth: true,
        lineStyle: {
          color: '#6AA1FF',
          width: 3,
          type: 'dashed',
        },
      },
    ],
  }

  return {
    data,
    chartOption,
  }
}

const echarts = ref()
const theme = themeMode()
watch(theme, () => {
  const { setOption } = useEcharts(echarts, { theme })
  switch (props.name) {
    case 'visitors':
      const { getVisitorsData } = getAnalysisData()
      console.log(getVisitorsData())
      // setOption()
      break
  }
}, { immediate: true })
</script>

<template>
  <div class="mine-card">
    <div class="content-wrap">
      <div class="content">
        <el-statistic :value="renderData.count">
          <template #title>
            访问总人次
          </template>
        </el-statistic>
        <div class="desc">
          <div>较昨日</div>
          <div>
            {{ renderData.growth }}
            <ma-svg-icon name="ic:baseline-arrow-upward" />
          </div>
        </div>
      </div>
      <div class="chart">
        <div ref="echarts" class="h-[90px]" />
      </div>
    </div>
  </div>
</template>
