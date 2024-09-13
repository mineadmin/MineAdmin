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
import type { Plugin } from '#/global'

const pluginConfig: Plugin.PluginConfig = {
  install(app: App) {
    // // 加载mock

  },
  hooks: {
    setup: () => {
      // 加载插件
      const { addPlugin } = useProTableRenderPlugin()
      const components = import.meta.glob('./components/**/*.vue')
      Object.keys(components).forEach(async (key) => {
        const component: any = await components[key]()
        addPlugin({
          name: component.default.name,
          render: (scope, options: any) => h(component.default, { scope, options }),
        })
      })

      // 将./mock目录下的东西加载到mock上
      // import('./mock')
    },
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/cell-render',
      version: '1.0.0',
      author: 'amazes',
      description: '表格渲染组件',
    },
  },
  views: [
    {
      name: 'cell-render-demo',
      path: '/cell-render-demo',
      component: () => import('./views/demo.vue'),
      meta: {
        title: '单元格渲染demo',
        badge: () => 1,
        hidden: true,
        copyright: false,
      },
    },
  ],
}

export default pluginConfig
