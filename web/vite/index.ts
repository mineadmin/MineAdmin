/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PluginOption } from 'vite'
import vueLegacy from '@vitejs/plugin-legacy'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import createArchiver from './archiver'
import createAutoImport from './auto-import'
import createChunkSplit from './chunk'
import createComponents from './components'
import createCompression from './compression'
import createDevtools from './devtools'
import createI18nMessage from './i18n-message'
import startInfo from './start-info'
import createSvgIcon from './svg-icon'
import createUnocss from './unocss'

export default function createVitePlugins(viteEnv: any, isBuild = false) {
  const vitePlugins: (PluginOption | PluginOption[])[] = [
    vue(),
    vueJsx(),
    startInfo(),
    vueLegacy({
      renderLegacyChunks: false,
      modernPolyfills: [
        'es.array.at',
        'es.array.find-last',
      ],
    }),
  ]
  vitePlugins.push(createDevtools(viteEnv))
  vitePlugins.push(createAutoImport())
  vitePlugins.push(createComponents())
  vitePlugins.push(createUnocss())
  vitePlugins.push(createSvgIcon(isBuild))
  vitePlugins.push(...createCompression(viteEnv, isBuild))
  vitePlugins.push(createArchiver(viteEnv))
  vitePlugins.push(createI18nMessage())
  vitePlugins.push(createChunkSplit())
  return vitePlugins
}
