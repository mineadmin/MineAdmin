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
import getAnalysisData from '../../datas/analysis.ts'
import { useEcharts } from '@/hooks/useEcharts.ts'

const props = withDefaults(defineProps<{
  name: string
  title: string
  chartType: string
}>(), {
  name: 'visitors',
  title: '',
  chartType: 'line',
})

const renderData = ref<Record<string, any>>({
  count: 0,
  growth: 0,
  chartData: [],
})

const chartOption = ref({})

const { lineChartOptionsFactory, barChartOptionsFactory, pieChartOptionsFactory } = getAnalysisData()

const { chartOption: lineChartOption, data: lineData }
  = lineChartOptionsFactory()
const { chartOption: barChartOption, data: barData }
  = barChartOptionsFactory()
const { chartOption: pieChartOption, data: pieData }
  = pieChartOptionsFactory()

const ele = ref()
const { setOption } = useEcharts(ele)

switch (props.name) {
  case 'visitors': {
    const { getVisitorsData } = getAnalysisData()
    renderData.value = getVisitorsData()
    break
  }
  case 'published': {
    const { getPublishedData } = getAnalysisData()
    renderData.value = getPublishedData()
    break
  }
  case 'contentTimer': {
    const { getContentTimerData } = getAnalysisData()
    renderData.value = getContentTimerData()
    break
  }
}

if (props.chartType === 'bar') {
  renderData.value.chartData.forEach((el, idx) => {
    barData.value.push({
      value: el.y,
      itemStyle: {
        color: idx % 2 ? '#2CAB40' : '#86DF6C',
      },
    })
  })
  chartOption.value = barChartOption.value
}
else if (props.chartType === 'line') {
  renderData.value.chartData.forEach((el) => {
    if (el.name === '2021') {
      lineData.value[0].push(el.y)
    }
    else {
      lineData.value[1].push(el.y)
    }
  })
  chartOption.value = lineChartOption.value
}
else {
  renderData.value.chartData.forEach((el) => {
    pieData.value.push(el)
  })
  chartOption.value = pieChartOption.value
}
setOption(chartOption.value)
</script>

<template>
  <div class="mine-layout">
    <div class="content-wrap">
      <div class="content">
        <el-statistic :value="renderData.count">
          <template #title>
            <div class="text-base">
              {{ props.title }}
            </div>
          </template>
        </el-statistic>
        <div class="desc mt-2">
          <div class="text-sm">
            较昨日
          </div>
          <div class="flex items-center text-red-5">
            {{ renderData.growth }}
            <ma-svg-icon name="ic:baseline-arrow-upward" />
          </div>
        </div>
      </div>
      <div class="chart">
        <div ref="ele" class="h-[100px]" />
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.content-wrap {
  @apply
  flex rounded mt-5 p-3 bg-[rgb(var(--ui-primary)/5%)] dark-bg-[rgb(var(--ui-primary)/10%)]
  gap-x-3 items-center
  ;

  .chart {
    @apply w-[calc(100%-100px)];
  }
}
</style>
