/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { vitePluginFakeServer } from 'vite-plugin-fake-server'

export default function createMock(env: any, isBuild: boolean) {
  const { VITE_BUILD_MOCK } = env
  return vitePluginFakeServer({
    logger: !isBuild,
    include: 'mock',
    infixName: false,
    enableProd: isBuild && VITE_BUILD_MOCK === 'true',
  })
}
