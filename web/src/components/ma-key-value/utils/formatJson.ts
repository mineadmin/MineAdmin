/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default function formatJson(json: Record<string, any>): string {
  try {
    return JSON.stringify(json, null, 2)
  }
  catch (error) {
    // 如果解析失败，返回原始字符串并附带错误信息
    console.error('Invalid JSON string:', error)
    return `/* Invalid JSON: ${json} */`
  }
}
