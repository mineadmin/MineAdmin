/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MineToolbarExpose, Plugin } from '#/global'
import Message from 'vue-m-message'
import pkg from '@/../package.json'

const pluginConfig: Plugin.PluginConfig = {
  install(app) {
    const $toolbars = app.config.globalProperties.$toolbars as MineToolbarExpose
    $toolbars.add({
      name: 'code-generator',
      title: '代码生成器',
      show: true,
      icon: 'heroicons:code-bracket-20-solid',
      handle: async () => {
        await useTabStore().go({ path: '/code-generator' })
      },
    })
    console.log('官方代码生成器已启动，如要关闭，请将 ./web/plugins/mine-admin/code-generator/index.ts 下的 config.enable 设置为 false')
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/code-generator',
      version: '1.0.0',
      author: 'X.Mo',
      description: 'MineAdmin 代码生成器',
    },
  },
  hooks: {
    setup: () => {
      if (!pkg?.dependencies?.vuedraggable) {
        Message.error('依赖不存在，无法使用代码生成器，请安装依赖：pnpm add vuedraggable@next')
      }
    },
  },
  views: [
    {
      name: 'MineAdminCodeGenerator',
      path: '/code-generator',
      meta: {
        title: '代码生成器',
        badge: () => 'CRUD',
        i18n: 'menu.code-generator',
        icon: 'heroicons:code-bracket-20-solid',
        type: 'M',
        breadcrumbEnable: true,
        copyright: true,
        cache: true,
      },
      component: () => import('./views/index.vue'),
    },
  ],
}

export default pluginConfig
