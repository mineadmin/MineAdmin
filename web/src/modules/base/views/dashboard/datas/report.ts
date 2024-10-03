/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import Mock from 'mockjs'

export default function getReportData() {
  const getOverviewData = () => {
    const generateLineData = (name: string) => {
      return {
        name,
        count: Mock.Random.natural(20, 2000),
        value: Array.from({ length: 8 }).fill(0).map(() => Mock.Random.natural(800, 4000)),
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
            .map(() => Mock.Random.natural(1000, 3000)),
        },
      }
    }
    return {
      count: Mock.Random.natural(1000, 3000),
      growth: Mock.Random.float(20, 100, 2, 2),
      chartData: getLineData(),
    }
  }

  return {
    getOverviewData,
    getItemData,
  }
}
