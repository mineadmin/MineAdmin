/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import App from './App.vue'
import MineBootstrap from './bootstrap'

const app = createApp(App)

MineBootstrap(app).then(() => {
  app.mount('#app')
}).catch((err) => {
  console.error('MineAdmin-UI start fail', err)
})
