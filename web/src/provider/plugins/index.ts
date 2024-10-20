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
import { sort } from 'radash'

const pluginList = {}
async function getPluginList() {
  const plugins = import.meta.glob('../../plugins/*/*/index.ts')
  const sortedPlugins: any[] = []
  for (const path in plugins) {
    const { default: plugin }: any = await plugins[path]()
    sortedPlugins.push(plugin)
  }

  sort(sortedPlugins, f => f.config.info.order ?? 0, true).map((item) => {
    pluginList[item.config.info.name] = item
  })
}

const pluginConfig = {}
async function getPluginConfig() {
  const configs = import.meta.glob('./config/**.ts')
  for (const path in configs) {
    const { default: config }: any = await configs[path]()
    const matches = path.match(/[^config/][\w-]+/g) as string[]
    const name = (matches?.[0] + matches?.[1]).replace('.', '/')
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
