/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { RouteRecordRaw } from 'vue-router'
import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'
import { useNProgress } from '@vueuse/integrations/useNProgress'
import '@/assets/styles/nprogress.scss'
import routes from './static-routes/rootRoute.ts'
import useUserStore from "@/store/modules/useUserStore.ts";
import useSettingStore from "@/store/modules/useSettingStore.ts";
import useRouteStore from "@/store/modules/useRouteStore.ts";

const { isLoading } = useNProgress()

const router = createRouter({
  history: import.meta.env.VITE_APP_ROUTE_MODE === 'history' ? createWebHistory() : createWebHashHistory(),
  routes: routes as RouteRecordRaw[],
})

router.beforeEach(async (to, from, next) => {
  const settingStore = useSettingStore()
  const userStore = useUserStore()
  const routeStore = useRouteStore()
  isLoading.value = true
  if (userStore.isLogin) {
    if (to.name === 'login') {
      next({
        path: settingStore.getSettings('welcomePage').path,
        replace: true,
      })
    }
    if (userStore.getUserInfo() === null) {
      await userStore.requestUserInfo()
      await routeStore.initRoutes(router, )
      next({ path: to.fullPath })
    }
    else {
      next()
    }
  }
  else {
    settingStore.getSettings('app').whiteRoute.includes(to.name as string)
      ? next()
      : next({ name: 'login', query: { redirect: to.fullPath } })
  }
})

router.afterEach((to) => {
  isLoading.value = false
  const keepAliveStore = useKeepAliveStore()

  if (to.meta.cache) {
    const componentName = to.matched.at(-1)?.components?.default.name
    if (componentName) {
      keepAliveStore.add(componentName)
    }
    else {
      console.warn(`MineAdmin-UI：[${to.meta.title}] 组件页面未设置组件名，将不会被缓存`)
    }
  }
})

export default router
