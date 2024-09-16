/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { RouteRecordRaw, Router } from 'vue-router'
import { useProTableRenderPlugin } from '@mineadmin/pro-table'
import type { Plugin } from '#/global'

const pluginConfig: Plugin.PluginConfig = {
  install(app) {
    // const $toolbars = app.config.globalProperties.$toolbars as MineToolbarExpose
    // $toolbars.add({
    //   name: 'test',
    //   title: '测试',
    //   show: true,
    //   icon: 'heroicons:archive-box',
    //   handle: () => Message.info('我是在demo插件下扩展出来的哦'),
    //   // component: () => import()
    // })
    console.log('demo 插件已经启动')
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/demo',
      version: '1.0.0',
      author: 'X.Mo',
      description: '一个演示小插件',
    },
  },
  hooks: {
    start: (config): any => {
      console.log('demo 插件启动前调用了 start 钩子', `插件版本: ${config.info.version}`)
    },
    setup: () => {
      const { addPlugin, getPlugins } = useProTableRenderPlugin()
      addPlugin({
        name: 'test',
        render: () => '我是demo插件渲染出来的内容',
      })
    },
    login: (formInfo) => {
      console.log('demo 插件的登录hook，此次登录用户信息：', formInfo)
    },
    registerRoute: (router: Router, routesRaw): void => {
      console.log(
        'demo 插件注册路由钩子，但demo插件并未注册任何路由',
        routesRaw, router,
      )
    },
    routerRedirect: (route: RouteRecordRaw) => {
      console.log('demo 插件的路由跳转钩子，此次跳转路由信息：', route)
    },
    // networkRequest: (request) => {
    //   console.log('demo 插件的网络请求钩子，此次请求信息：', request)
    // },
    // networkResponse: (response) => {
    //   console.log('demo 插件的网络返回钩子，此次返回信息：', response)
    // },
  },
  views: [
    {
      name: 'demo',
      path: '/demo',
      meta: {
        title: '开发示例',
        // badge: () => 1,
        hidden: false,
        copyright: false,
      },
      children: [
        {
          name: 'demo-pro-table',
          path: '/demo/pro-table',
          component: () => import('./views/pro-table/index.vue'),
          meta: {
            title: '表格示例',
          },
        },
        {
          name: 'demo-form',
          path: '/demo/form',
          component: () => import('./views/form/index.vue'),
          meta: {
            title: '表单示例',
          },
        },
      ],
    },
  ],
}

export default pluginConfig
