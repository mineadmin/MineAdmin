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
import type { ProviderService } from '#/global'
import useGlobal from '@/hooks/auto-imports/useGlobal.ts'

const pluginConfigs = {}
async function getPluginConfig() {
  const plugins = import.meta.glob('../../plugins/**/*/index.ts')
  for (const path in plugins) {
    const { default: plugin }: any = await plugins[path]()
    pluginConfigs[plugin.config.info.name] = plugin
  }
}

const provider: ProviderService.Provider = {
  name: 'plugins',
  async init() {
    await getPluginConfig()
  },
  setProvider(app: App) {
    app.config.globalProperties.$plugins = pluginConfigs
  },
  getProvider(): any {
    return useGlobal().$plugins
  },
}

export default provider as ProviderService.Provider
