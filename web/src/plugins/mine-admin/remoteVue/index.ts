/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Plugin } from '#/global'

const pluginConfig: Plugin.PluginConfig = {
  install() {
    console.log('MineAdmin 远程加载视图插件已启动')
  },
  config: {
    enable: true,
    info: {
      name: 'mine-admin/remote-load-vue',
      version: '1.0.0',
      author: 'X.Mo',
      description: '提供远程加载Vue文件（服务器端渲染）能力',
    },
  },
}

export default pluginConfig
