/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Router } from 'vue-router'
import type { MineRoute } from '#/global'

export default async function menuGotoHandle(router: Router, route: MineRoute.routeRecord) {
  const setting = useSettingStore().getSettings('mainAside')
  switch (route?.meta?.type) {
    case 'L':
      window.open(route.meta?.link, '_blank')
      break
    case 'I':
      await router.push({ path: route.path })
      break
    default :
      if (route?.redirect ?? undefined) {
        await router.push({ path: route.redirect })
      }
      else if (route.path && (route.component || route.components)) {
        await router.push({ path: route.path })
      }
      else if (setting.enableOpenFirstRoute && route?.children?.length) {
        if (route?.children[0]) {
          await menuGotoHandle(router, route?.children[0])
          break
        }
      }
      break
  }
}
