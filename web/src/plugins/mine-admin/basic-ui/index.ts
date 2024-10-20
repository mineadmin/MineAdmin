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
import FloatingVue from 'floating-vue'
import type { Plugin } from '#/global'
import 'floating-vue/dist/style.css'

const pluginConfig: Plugin.PluginConfig = {
  install(app: App) {
    app.use(FloatingVue, { distance: 12 })
    const components = import.meta.glob('./components/**/*.vue')
    Object.keys(components).forEach(async (key) => {
      const component: any = await components[key]()
      app.component(component.default.name, component.default)
    })
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/basic-ui',
      version: '1.0.0',
      author: 'X.Mo',
      description: 'MineAdmin基础UI',
      order: 99999,
    },
  },
}

export default pluginConfig
