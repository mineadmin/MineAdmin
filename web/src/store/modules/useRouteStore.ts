import type { Plugin, SystemSettings } from '#/global'
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Router, RouteRecordRaw } from 'vue-router'
import dashboardRoute from '@/router/static-routes/dashboardRoute'
import welcomeRoute from '@/router/static-routes/welcomeRoute'
import usePluginStore from '@/store/modules/usePluginStore.ts'

const useRouteStore = defineStore(
  'useRouteStore',
  () => {
    const defaultSetting = ref<SystemSettings.all>(useDefaultSetting())
    // 原始路由
    const routesRaw = ref<RouteRecordRaw[]>([])
    const flatteningRoutesList = ref<RouteRecordRaw[]>([])
    async function initRoutes(router: Router, routes: any[]) {
      const MineRootLayoutRoute = getMineRootLayoutRoute()

      router.hasRoute('MineRootLayoutRoute') && router.removeRoute('MineRootLayoutRoute')
      router.addRoute(MineRootLayoutRoute)
      routesRaw.value = router.getRoutes()
      routes = menuToRoutes(routes)

      router.getRoutes().find((route, key) => {
        if (route.name === 'MineRootLayoutRoute') {
          // @ts-expect-error eslint-disable-next-line ts/ban-ts-comment
          routesRaw.value[key].children.push(...routes)
        }
      })
      flatteningRoutesList.value = flatteningRoutes(routes)
      flatteningRoutesList.value.map((routeItem: RouteRecordRaw) => router.addRoute('MineRootLayoutRoute', routeItem))
      const plugins = usePluginStore().getPluginConfig() as { [ key: string ]: Plugin.PluginConfig }
      Object.keys(plugins).map((name: string) => {
        const plugin = plugins[name] as Plugin.PluginConfig
        if (plugin.config?.enable === true && plugin?.views) {
          plugin.views.map((item: Plugin.Views) => {
            const route = toRecordRawRoute(item)
            MineRootLayoutRoute.children?.push(route)
            router.addRoute('MineRootLayoutRoute', route)
          })
        }
      })
      await usePluginStore().callHooks('registerRoute', router, routesRaw.value)
    }

    function getMineRootLayoutRoute(): RouteRecordRaw {
      const welcomePage: SystemSettings.welcomePage = defaultSetting.value.welcomePage
      return {
        name: 'MineRootLayoutRoute',
        path: '/',
        component: () => import('@/layouts'),
        redirect: welcomePage.path,
        children: [
          Object.assign(welcomeRoute, {
            name: welcomePage.name,
            path: welcomePage.path,
            meta: {
              title: welcomePage.title,
              i18n: 'menu.welcome',
              icon: welcomePage.icon,
              type: 'M',
              affix: true,
              breadcrumbEnable: true,
              copyright: true,
              cache: true,
            },
          }),
          toRecordRawRoute(dashboardRoute),
          toRecordRawRoute({
            path: '/:pathMatch(.*)*',
            name: 'MineSystemError',
            component: () => import(('@/layouts/[...all].tsx')),
            meta: {
              hidden: true,
              i18n: 'menu.pageError',
            },
          }),
        ],
      }
    }

    /**
     * 扁平化为一层路由
     */
    function flatteningRoutes(routes: any[] = [], breadcrumb: any[] = []) {
      const res: any = []
      routes.forEach((route) => {
        const tmp = { ...route }
        if (tmp.children) {
          const childrenBreadcrumb = [...breadcrumb]
          childrenBreadcrumb.push(route)
          const tmpRoute = { ...route }
          tmpRoute.meta = tmpRoute?.meta ?? {}
          tmpRoute.meta.breadcrumb = childrenBreadcrumb
          delete tmpRoute.children
          res.push(tmpRoute)
          const childrenRoutes = flatteningRoutes(tmp.children, childrenBreadcrumb)
          childrenRoutes.map((item: any) => res.push(item))
        }
        else {
          const tmpBreadcrumb = [...breadcrumb]
          tmpBreadcrumb.push(tmp)
          tmp.meta = tmp?.meta ?? {}
          tmp.meta.breadcrumb = tmpBreadcrumb
          res.push(tmp)
        }
      })
      return res
    }

    function toRecordRawRoute(route: any) {
      return flatteningRoutes([route])[0].meta.breadcrumb[0]
    }

    const moduleViews = import.meta.glob('../../modules/**/views/**/**.{vue,jsx,tsx}')
    const pluginViews = import.meta.glob('../../plugins/*/**/views/**/**.{vue,jsx,tsx}')

    /**
     * 菜单转路由
     * @param routerMap
     */
    function menuToRoutes(routerMap: any[]) {
      const accessedRouters: any = []
      routerMap.forEach((item: any) => {
        if (item.meta?.type !== 'B') {
          if (item.meta.type === 'I') {
            item.path = `/MineIframe/${item.name}`
            item.component = () => import(('@/layouts/components/iframe/index.tsx'))
          }

          const suffix: string = item.meta?.componentSuffix ?? '.vue'

          let component: any | null = null
          if (item.component && item.meta?.type !== 'I') {
            if (moduleViews[`../../modules/${item.component}${suffix}`]) {
              component = moduleViews[`../../modules/${item.component}${suffix}`]
            }
            else if (pluginViews[`../../plugins/${item.component}${suffix}`]) {
              component = pluginViews[`../../plugins/${item.component}${suffix}`]
            }
            else {
              // console.warn(`MineAdmin-UI: 路由 [${item.meta.title}] 找不到 ${item.component}${suffix} 页面`)
            }
          }

          const route = {
            path: item.path,
            name: item.name,
            meta: item.meta,
            children: item.children ? menuToRoutes(item.children) : null,
            component: item.meta?.type === 'I' ? item.component : component,
          }
          accessedRouters.push(route)
        }
      })
      return accessedRouters
    }
    return {
      initRoutes,
      toRecordRawRoute,
      flatteningRoutes,
      routesRaw,
      flatteningRoutesList,
    }
  },
)

export default useRouteStore
