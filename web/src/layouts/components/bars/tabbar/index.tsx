/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import '@/layouts/style/tabbar.scss'
import Message from 'vue-m-message'
import { useI18n } from 'vue-i18n'
import { TransitionGroup } from 'vue'
import { useSortable } from '@vueuse/integrations/useSortable'
import ContextMenu from '@imengyu/vue3-context-menu'
import useSettingStore from '@/store/modules/useSettingStore.ts'
import '@imengyu/vue3-context-menu/lib/vue3-context-menu.css'
import type { MineTabbar } from '#/global'
import useTabStore from '@/store/modules/useTabStore.ts'

export default defineComponent({
  name: 'Tabbar',
  setup() {
    const tabStore = useTabStore()
    const { getSettings } = useSettingStore()
    const route = useRoute()
    const { t } = useI18n()
    const el = ref<HTMLElement | null>(null)

    useSortable(el, tabStore.tabList)

    const executeContextmenu = (e: MouseEvent, item: MineTabbar) => {
      e.preventDefault()
      ContextMenu.showContextMenu({
        x: e.x,
        y: e.y,
        zIndex: 1050,
        iconFontClass: '',
        customClass: 'mine-tab-contextmenu',
        items: [
          {
            label: t('mineAdmin.tab.refresh') as string,
            icon: 'i-ri:refresh-line',
            onClick: async () => tabStore.refreshTab(item),
          },
          {
            label: t('mineAdmin.tab.close') as string,
            icon: 'i-ri:close-line',
            disabled: tabStore.tabList.length <= 1 || item.affix,
            divided: true,
            onClick: () => tabStore.closeTab(item),
          },
          {
            label: t('mineAdmin.tab.fixed') as string,
            icon: 'i-ri:pushpin-2-line',
            disabled: item.affix,
            onClick: () => tabStore.affixTab(item),
          },
          {
            label: t('mineAdmin.tab.fullscreen') as string,
            icon: 'i-material-symbols:fullscreen',
            divided: true,
            onClick: () => tabStore.maxSizeTab(item),
          },
          {
            label: t('mineAdmin.tab.closeOther') as string,
            icon: 'i-ic:outline-cancel',
            onClick: () => tabStore.closeOtherTab(item),
          },
          {
            label: t('mineAdmin.tab.closeLeft') as string,
            icon: 'i-material-symbols:skip-previous-outline-rounded',
            onClick: () => tabStore.closeLeftTab(item),
          },
          {
            label: t('mineAdmin.tab.closeRight') as string,
            icon: 'i-material-symbols:skip-next-outline-rounded',
            onClick: () => tabStore.closeRightTab(item),
          },
        ],
      })
    }

    const handlerWheelScroll = (e: WheelEvent) => {
      e.preventDefault()
      const node = document.querySelector('.mine-tabbar') as HTMLElement
      node.scrollBy({
        left: e.deltaY || e.detail,
      })
    }

    const redirect = async (e: MouseEvent, item: MineTabbar) => {
      e.preventDefault()
      await tabStore.go(item)
    }

    watch(() => route, async () => {
      useTabStore().addTab({
        name: route.name as string,
        path: route.path as string,
        fullPath: route?.fullPath ?? route.path,
        title: route.meta?.title as string,
        i18n: route.meta?.i18n as string,
        icon: route.meta?.icon as string,
        affix: route.meta?.affix === true,
      } as MineTabbar)

      await nextTick(async () => {
        document.querySelector('.tab-item.active')?.scrollIntoView({
          behavior: 'smooth',
          block: 'center',
        })
        // document.querySelector('.mine-main')?.scrollTo({ top: 0 })
      })
    }, { immediate: true, deep: true })

    return () => (
      <div ref="tabsRef" class="mine-tab-container !hidden !lg:flex">
        <TransitionGroup name="tabbar" tag="div" class="mine-tabbar" ref={el} onWheel={(e: WheelEvent) => handlerWheelScroll(e)}>
          {tabStore.tabList?.map((item: any) => {
            return (
              <a
                key={item.fullPath}
                class={{
                  'tab-item': true,
                  'rectanglePlan': getSettings('tabbar').mode === 'rectangle',
                  'cardPlan': getSettings('tabbar').mode === 'card',
                  'active': (route.fullPath === item.fullPath || route.path === item.path),
                }}
                title={item?.i18n ? useTrans(item.i18n) : item.title}
                onClick={(e: MouseEvent) => redirect(e, item)}
                onContextmenu={(e: MouseEvent) => executeContextmenu(e, item)}
              >
                {item.icon && <ma-svg-icon name={item.icon} class="menu-icon" /> }
                <span class="title">
                  {item?.i18n ? useTrans(item.i18n) : item.title}
                </span>
                {item.affix
                && (
                  <ma-svg-icon
                    name="ic:baseline-push-pin"
                    class="icon"
                    onClick={(e: Event) => {
                      e.stopPropagation()
                      if (item.name === tabStore.defaultTab.name) {
                        Message.error(t('mineAdmin.tab.cannotUnfixed'))
                        return
                      }
                      tabStore.cancelAffixTab(item)
                    }}
                  />
                )}
                {!item.affix
                && (
                  <ma-svg-icon
                    name="material-symbols:close-rounded"
                    class="icon"
                    onClick={(e: Event) => {
                      e.stopPropagation()
                      tabStore.closeTab(item)
                    }}
                  />
                )}
              </a>
            )
          })}
        </TransitionGroup>

        {/* { tabStore.tabList.length > 1 */}
        {/* && ( */}
        {/*  <div class="mine-tab-more-operation"><ma-svg-icon name="i-ic:twotone-more-horiz" size={24} /></div> */}
        {/* )} */}
      </div>
    )
  },
})
