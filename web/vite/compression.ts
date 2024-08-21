/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { compression } from 'vite-plugin-compression2'
import type { PluginOption } from 'vite'

export default function createCompression(env: any, isBuild: boolean) {
  const plugin: (PluginOption | PluginOption[])[] = []
  if (isBuild) {
    const { VITE_BUILD_COMPRESS } = env
    const compressList = VITE_BUILD_COMPRESS.split(',')
    if (compressList.includes('gzip')) {
      plugin.push(
        compression(),
      )
    }
    if (compressList.includes('brotli')) {
      plugin.push(
        compression({
          exclude: [/\.(br)$/, /\.(gz)$/],
          algorithm: 'brotliCompress',
        }),
      )
    }
  }
  return plugin
}
