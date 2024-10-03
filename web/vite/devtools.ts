/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import VueDevTools from 'vite-plugin-vue-devtools'

export default function createDevtools(env) {
  const { VITE_OPEN_DEVTOOLS } = env
  return VITE_OPEN_DEVTOOLS === 'true' && VueDevTools()
}
