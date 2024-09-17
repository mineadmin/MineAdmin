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

export default function getAnalysisData() {
  const getVisitorsData = () => {
    const year = new Date().getFullYear()
    const getLineData = (name: number) => {
      return Array.from({ length: 12 }).fill(0).map((_item, index) => ({
        x: `${index + 1}月`,
        y: Mock.Random.natural(0, 100),
        name: String(name),
      }))
    }
    return successResponseWrap({
      count: 5670,
      growth: 206.32,
      chartData: [...getLineData(year), ...getLineData(year - 1)],
    })
  }

  const getPublishedData = () => {
    const year = new Date().getFullYear()
    const getLineData = (name: number) => {
      return Array.from({ length: 12 }).fill(0).map((_item, index) => ({
        x: `${index + 1}日`,
        y: Mock.Random.natural(20, 100),
        name: String(name),
      }))
    }
    return successResponseWrap({
      count: 5670,
      growth: 206.32,
      chartData: [...getLineData(year)],
    })
  }

  const getContentTimerData = () => {
    return successResponseWrap({
      count: 5670,
      growth: 206.32,
      chartData: [
        // itemStyle for demo
        { name: '文本类', value: 25, itemStyle: { color: '#8D4EDA' } },
        { name: '图文类', value: 35, itemStyle: { color: '#165DFF' } },
        { name: '视频类', value: 40, itemStyle: { color: '#00B2FF' } },
      ],
    })
  }

  return {
    getVisitorsData,
    getPublishedData,
    getContentTimerData,
  }
}
