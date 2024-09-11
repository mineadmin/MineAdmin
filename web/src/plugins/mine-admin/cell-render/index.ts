/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { App } from 'vue'
import { useProTableRenderPlugin } from '@mineadmin/pro-table'
import type { RouteRecordRaw, Router } from 'vue-router'
import { components } from './components/index.js'
import type { Plugin } from '#/global'

const pluginConfig: Plugin.PluginConfig = {
  install(app: App) {
  },
  hooks: {
    setup: () => {
      const { addPlugin } = useProTableRenderPlugin()
      const prefix = 'ma-'
      Object.keys(components).forEach(async (key) => {
        const component: any = components[key]
        const name = `${prefix}${key}`
        addPlugin({
          name,
          render: (scope, options: any) => h(component, { scope, options }),
        })
      })
    },
    registerRoute: (router: Router, routesRaw): void => {
      router.addRoute({
        name: 'cell-render',
        path: '/cell-render',
        component: () => import('./practice.vue'),
      })
    },
    routerRedirect: (route: RouteRecordRaw) => {
      // 劫持welcome路由返回practice.vue
      if (route.path === '/welcome') {
        // router.push('/cell-render')
      }
    },
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/cell-render',
      version: '1.0.0',
      author: '陈展杰',
      description: '表格渲染组件',
    },
  },
}

export default pluginConfig
