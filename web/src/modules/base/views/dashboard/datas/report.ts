/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */

export default function getReportData() {
  const getOverviewData = () => {
    const generateLineData = (name: string) => {
      return {
        name,
        count: Math.floor(Math.random() * 20 + 5),
        value: Array.from({ length: 8 }).fill(0).map(() => Math.floor(Math.random() * 800 + 500)),
      }
    }
    const xAxis = Array.from({ length: 8 }).fill(0).map((_item, index) => {
      return `12.1${index}`
    })
    return {
      xAxis,
      data: [
        generateLineData('内容生产量'),
        generateLineData('内容点击量'),
        generateLineData('内容曝光量'),
        generateLineData('活跃用户数'),
      ],
    }
  }

  const getItemData = (quota: string) => {
    const getLineData = () => {
      return {
        xAxis: Array.from({ length: 12 }).fill(0).map((_item, index) => `${index + 1}日`),
        data: {
          name: quota,
          value: Array.from({ length: 12 })
            .fill(0)
            .map(() => Math.floor(Math.random() * 1000 + 500)),
        },
      }
    }
    return {
      count: Math.floor(Math.random() * 1000 + 100),
      growth: Math.floor(Math.random() * 20 + 5),
      chartData: getLineData(),
    }
  }

  return {
    getOverviewData,
    getItemData,
  }
}
