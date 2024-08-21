/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import hasIncludesByArray from '../hasIncludesByArray'

export default function hasDept(value: string | string[], whetherCheckRouteMeta: boolean = false): boolean {
  if (!value) {
    return false
  }

  const depts = useUserStore().getDepts()

  if (!depts) {
    return false
  }

  let values: string[]
  if (whetherCheckRouteMeta) {
    const meta = (useRoute()?.meta?.dept ?? []) as string[]
    values = (Array.isArray(value) ? value.push(...meta) : [value, ...meta]) as string[]
  }
  else {
    values = Array.isArray(value) ? value : [value]
  }

  return hasIncludesByArray(depts, values)
}
