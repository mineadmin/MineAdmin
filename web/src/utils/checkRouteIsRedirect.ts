/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MineRoute } from '#/global'

export default function checkRouteIsRedirect(route: MineRoute.routeRecord, type: 'redirect' | 'component' = 'redirect'): boolean {
  if (type === 'redirect' && route.redirect && route?.meta?.type === 'M') {
    return true
  }

  return !!(route.component && route.path)
}
