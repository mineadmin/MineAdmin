/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MineTabbar } from '#/global'
import useTabCollection from '@/hooks/useTabCollection.ts'
import Logo from '@/layouts/components/logo'
import MineBreadcrumb from '../breadcrumb'
import MineRightBar from './components/right-bar.tsx'
import '@/layouts/style/toolbar.scss'

export default defineComponent({
  name: 'Toolbar',
  setup() {
    const settingStore = useSettingStore()
    const { tabCollection, addToCollection, removeCollection } = useTabCollection()
    const appSetting = settingStore.getSettings('app')
    return () => (
      <div
        class={{
          'mine-toolbar overflow-hidden': true,
          '!h-0 !b-b-0': settingStore.isMixedLayout() && !settingStore.getUserBarState() && !settingStore.getMobileState(),
        }}
      >
        <div class="hidden gap-x-4 lg:flex">
          <m-dropdown
            class="min-w-[18rem] p-3"
            v-slots={{
              default: () => <ma-svg-icon name="i-fluent:star-line-horizontal-3-16-regular" size={20} class="h-6 cursor-pointer" />,
              popper: () => (
                <div class="collection-wrapper">
                  <div class="title-bar">
                    <div>{useTrans('mineAdmin.tab.favorites')}</div>
                    <a class="cursor-pointer" title={useTrans('mineAdmin.tab.addFavorite')} onClick={() => addToCollection()}>
                      <ma-svg-icon name="fluent:collections-16-regular" size={20} />
                    </a>
                  </div>
                  <div class={{
                    list: tabCollection.value.length > 0,
                    flex: tabCollection.value.length === 0,
                  }}
                  >
                    {tabCollection.value?.map((item: MineTabbar) => (
                      <div class="div-button" onClick={() => useTabStore().go(item)}>
                        <div class="menu">
                          {item.icon && <ma-svg-icon name={item.icon} />}
                          <div>{item.i18n ? useTrans(item.i18n) : item.title}</div>
                        </div>
                        <ma-svg-icon name="streamline:recycle-bin-2" className="icon" size={14} onClick={() => removeCollection(item)} />
                      </div>
                    ))}
                    {tabCollection.value.length === 0 && (
                      <div class="h-13 w-full flex items-center justify-center">
                        {useTrans('mineAdmin.tab.noFavorite')}
                      </div>
                    )}
                  </div>
                </div>
              ),
            }}
          />
          { appSetting.showBreadcrumb && <MineBreadcrumb /> }
        </div>
        <div class="flex items-center gap-x-3 lg:hidden">
          <Logo showTitle={false} />
          <ma-svg-icon name="material-symbols:menu-rounded" size={20} class="cursor-pointer" onClick={() => settingStore.setMobileSubmenuState(true)} />
        </div>
        <MineRightBar />
      </div>
    )
  },
})
