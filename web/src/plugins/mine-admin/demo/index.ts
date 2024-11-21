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
import { useProTableRenderPlugin } from '@mineadmin/pro-table'
import type { MineToolbarExpose, Plugin } from '#/global'
import Message from 'vue-m-message'
import { ElButton } from 'element-plus'

const pluginConfig: Plugin.PluginConfig = {
  install(app) {
    const $toolbars = app.config.globalProperties.$toolbars as MineToolbarExpose
    $toolbars.add({
      name: 'test',
      title: '测试',
      show: true,
      icon: 'heroicons:archive-box',
      handle: () => Message.info('我是在demo插件下扩展出来的哦'),
      // component: () => import()
    })
    console.log('demo 插件已经启动')
  },
  config: {
    enable: false,
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
      const { addPlugin } = useProTableRenderPlugin()
      addPlugin({
        name: 'test',
        render: () => {
          return h(ElButton, null, 'test')
        },
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
    routerRedirect: (routes, router: Router) => {
      router.getRoutes()
      console.log('demo 插件的路由跳转钩子，此次跳转旧路由信息：', routes.oldRoute)
      console.log('demo 插件的路由跳转钩子，此次跳转新路由信息：', routes.newRoute)
    },
    networkRequest: (request) => {
      console.log('demo 插件的网络请求钩子，此次请求信息：', request)
    },
    networkResponse: (response) => {
      console.log('demo 插件的网络返回钩子，此次返回信息：', response)
    },
  },
}

export default pluginConfig
