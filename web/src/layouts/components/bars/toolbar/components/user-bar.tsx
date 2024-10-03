/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useI18n } from 'vue-i18n'
import Message from 'vue-m-message'
import MineShortcutsDesc from './dropdownMenuComponents/shortcuts-desc.tsx'
import MineSystemInfo from './dropdownMenuComponents/system-info.tsx'

export default defineComponent({
  name: 'UserBar',
  setup() {
    const userStore = useUserStore()
    const router = useRouter()
    const userInfo = userStore.getUserInfo()
    const { t } = useI18n()

    const links: any[] = [
      {
        label: 'mineAdmin.userBar.uc',
        icon: 'material-symbols:account-circle-outline',
        handle: () => router.push({ path: '/uc' }),
      },
      {
        label: 'mineAdmin.userBar.clearCache',
        icon: 'mingcute:broom-line',
        handle: async () => {
          await userStore.clearCache()
          Message.success(t('mineAdmin.common.clearCache'))
        },
      },
      { label: 'divider' },
      {
        label: 'mineAdmin.userBar.shortcuts',
        icon: 'i-material-symbols:keyboard-keys',
        handle: () => userStore.setDropdownMenuState('shortcuts', true),
      },
      {
        label: 'mineAdmin.userBar.systemInfo',
        icon: 'i-bi:info-circle',
        handle: () => userStore.setDropdownMenuState('systemInfo', true),
      },
      { label: 'divider' },
      {
        label: 'mineAdmin.userBar.logout',
        icon: 'hugeicons:logout-04',
        handle: () => userStore.logout(),
      },
    ]

    return () => (
      <div class="mine-user-bar">
        <m-dropdown
          class="min-w-[6rem] p-1"
          v-slots={{
            default: () => (
              <div class="mine-userinfo">
                {userInfo.avatar && <img src={userInfo.avatar} alt={userInfo.username} class="mine-img-avatar" />}
                {!userInfo.avatar && <div class="mine-text-avatar">{userInfo.username[0].toUpperCase()}</div>}
                <a class="username hidden lg:flex">
                  {userInfo.username}
                  <ma-svg-icon name="material-symbols:keyboard-arrow-down-rounded" className="icon" size={20} />
                </a>
              </div>
            ),
            popper: () => (
              <div>
                {links.map((item: any) => (
                  <div>
                    {item.label !== 'divider' && (
                      <m-dropdown-item
                        type="default"
                        handle={item.handle}
                        v-slots={{
                          'default': () => <span>{ useTrans(item.label) }</span>,
                          'prefix-icon': () => <ma-svg-icon name={item.icon} size={18} />,
                        }}
                      />
                    )}
                    {item.label === 'divider' && <m-dropdown-divider />}
                  </div>
                ),
                )}
              </div>
            ),
          }}
        />

        <MineSystemInfo />
        <MineShortcutsDesc />
      </div>
    )
  },
})
