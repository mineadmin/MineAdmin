/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default function getAnalysisData() {
  const getVisitorsData = () => {
    const year = new Date().getFullYear()
    const getLineData = (name: number) => {
      return Array.from({ length: 12 }).fill(0).map((_item, index) => ({
        x: `${index + 1}月`,
        y: Math.floor(Math.random() * 10 + 2),
        name: String(name),
      }))
    }
    return {
      count: 5670,
      growth: 206.32,
      chartData: [...getLineData(year), ...getLineData(year - 1)],
    }
  }

  const getPublishedData = () => {
    const year = new Date().getFullYear()
    const getLineData = (name: number) => {
      return Array.from({ length: 12 }).fill(0).map((_item, index) => ({
        x: `${index + 1}日`,
        y: Math.floor(Math.random() * 20 + 5),
        name: String(name),
      }))
    }
    return {
      count: 5670,
      growth: 206.32,
      chartData: [...getLineData(year)],
    }
  }

  const getContentTimerData = () => {
    return {
      count: 5670,
      growth: 206.32,
      chartData: [
        // itemStyle for demo
        { name: '文本类', value: 25, itemStyle: { color: '#8D4EDA' } },
        { name: '图文类', value: 35, itemStyle: { color: '#165DFF' } },
        { name: '视频类', value: 40, itemStyle: { color: '#00B2FF' } },
      ],
    }
  }

  function barChartOptionsFactory() {
    const data = ref<any>([])
    const chartOption = ref<any>({
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
      series: {
        name: 'total',
        data,
        type: 'bar',
        barWidth: 7,
        itemStyle: {
          borderRadius: 2,
        },
      },
    })
    return {
      data,
      chartOption,
    }
  }

  function lineChartOptionsFactory() {
    const data = ref<number[][]>([[], []])
    const chartOption = ref<any>({
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
    })

    return {
      data,
      chartOption,
    }
  }

  function pieChartOptionsFactory() {
    const data = ref<any>([])
    const chartOption = ref<any>({
      grid: {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
      },
      legend: {
        show: true,
        top: 'center',
        right: '0',
        orient: 'vertical',
        icon: 'circle',
        itemWidth: 6,
        itemHeight: 6,
        textStyle: {
          color: '#4E5969',
        },
      },
      tooltip: {
        show: true,
      },
      series: [
        {
          name: '总计',
          type: 'pie',
          radius: ['50%', '70%'],
          label: {
            show: false,
          },
          data,
        },
      ],
    })
    return {
      data,
      chartOption,
    }
  }

  function contentPublishRadio() {
    const generateLineData = (name: string) => {
      const result = {
        name,
        x: [] as string[],
        y: [] as number[],
      }
      Array.from({ length: 12 }).fill(0).forEach((_item, index) => {
        result.x.push(`${index * 2}:00`)
        result.y.push(Math.floor(Math.random() * 1000 + 500))
      })
      return result
    }
    return [
      generateLineData('纯文本'),
      generateLineData('图文类'),
      generateLineData('视频类'),
    ]
  }

  function contentPeriod() {
    const getLineData = (name: string) => {
      return {
        name,
        value: Array.from({ length: 12 }).fill(0).map(() => Math.floor(Math.random() * 30 + 5)),
      }
    }
    return {
      xAxis: Array.from({ length: 12 }).fill(0).map((_item, index) => `${index * 2}:00`),
      data: [
        getLineData('纯文本'),
        getLineData('图文类'),
        getLineData('视频类'),
      ],
    }
  }

  return {
    getVisitorsData,
    getPublishedData,
    getContentTimerData,
    barChartOptionsFactory,
    lineChartOptionsFactory,
    pieChartOptionsFactory,
    contentPublishRadio,
    contentPeriod,
  }
}
