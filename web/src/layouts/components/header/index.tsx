/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { Transition } from 'vue'
import '@/layouts/style/header.scss'
import Logo from '@/layouts/components/logo'
import MainAside from '@/layouts/components/main-aside'

export default defineComponent({
  name: 'Header',
  setup() {
    const settingStore = useSettingStore()
    return () => {
      return (
        <Transition name="mine-header">
          {settingStore.showMineHeader() && (
            <div class="mine-header-main hidden lg:flex">
              <Logo class="ml-2 overflow-hidden !w-[var(--mine-g-sub-aside-width)]" />
              <div class="w-[calc(100%-var(--mine-g-sub-aside-width))]">
                { settingStore.getSettings('app').layout === 'mixed' && <MainAside /> }
              </div>
            </div>
          )}
        </Transition>
      )
    }
  },
})
