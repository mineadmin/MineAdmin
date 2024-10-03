/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import MineTabbar from './tabbar'
import MineToolbar from './toolbar'

export default defineComponent({
  name: 'Bars',
  setup() {
    const settingStore = useSettingStore()
    return () => (
      <div class="mine-bars">
        <MineToolbar />
        {settingStore.getSettings('tabbar').enable && <MineTabbar />}
      </div>
    )
  },
})
