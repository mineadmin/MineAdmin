/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { ProviderService } from '#/global'
import type { App } from 'vue'
import useGlobal from '@/hooks/auto-imports/useGlobal.ts'

const pluginList = {}
async function getPluginList() {
  const plugins = import.meta.glob('../../plugins/*/*/index.ts')
  for (const path in plugins) {
    const { default: plugin }: any = await plugins[path]()
    pluginList[plugin.config.info.name] = plugin
  }
}

const pluginConfig = {}
async function getPluginConfig() {
  const configs = import.meta.glob('./config/**.ts')
  for (const path in configs) {
    const { default: config } = await configs[path]()
    const matches = path.match(/[^config/][\w-]+/g)
    const name = (matches[0] + matches[1]).replace('.', '/')
    pluginConfig[name] = config
  }
}

const provider: ProviderService.Provider = {
  name: 'plugins',
  async init() {
    await getPluginList()
    await getPluginConfig()
  },
  setProvider(app: App) {
    app.config.globalProperties.$plugins = pluginList
    app.config.globalProperties.$pluginsConfig = pluginConfig
  },
  getProvider(): any {
    return useGlobal().$plugins
  },
}

export default provider as ProviderService.Provider
