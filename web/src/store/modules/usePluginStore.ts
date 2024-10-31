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
import type { Plugin } from '#/global'

const usePluginStore = defineStore(
  'usePluginStore',
  () => {
    interface keyPlugins { [key: string]: Plugin.PluginConfig }
    const plugins = ref<keyPlugins>({})
    const useList = ref<Record<string, any>>({})
    const instance = ref<App>()

    async function registerPlugin(app: App) {
      instance.value = app
      plugins.value = app.config.globalProperties.$plugins as keyPlugins
      Object.keys(plugins.value).map(async (name: string) => {
        const plugin: Plugin.PluginConfig = plugins.value[name]
        if (plugin.config?.enable && plugin?.hooks && plugin?.hooks?.start) {
          await plugin.hooks.start(plugin.config)
        }
        if (plugin?.config?.enable) {
          useList.value[name] = app.use(plugin.install)
        }
      })
    }

    /**
     * 调用插件hooks
     * @param hookName
     * @param args
     */
    async function callHooks(hookName: string, ...args: any[]) {
      await Promise.all(Object.keys(plugins.value as keyPlugins).map(async (name: string) => {
        const plugin: Plugin.PluginConfig = plugins.value[name]
        if (plugin.config?.enable && plugin?.hooks && plugin.hooks[hookName]) {
          await plugin.hooks[hookName](...args)
        }
      }))
    }

    function getPluginConfig(): keyPlugins {
      return plugins.value as keyPlugins
    }

    function enabled(pluginName: string) {
      if (plugins.value[pluginName]) {
        const plg: Plugin.PluginConfig = plugins.value[pluginName]
        plg.config.enable = true
        if (plg?.hooks && plg?.hooks?.start) {
          plg.hooks.start(plg.config)
        }
        if (!useList.value[pluginName]) {
          useList.value[pluginName] = instance.value?.use(plg.install)
        }
      }
    }

    function disabled(pluginName: string) {
      if (plugins.value[pluginName]) {
        const plg = plugins.value[pluginName]
        plg.config.enable = false
      }
    }

    return {
      registerPlugin,
      callHooks,
      getPluginConfig,
      enabled,
      disabled,
    }
  },
)

export default usePluginStore
