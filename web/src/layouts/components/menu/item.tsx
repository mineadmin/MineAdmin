import { useI18n } from 'vue-i18n'
import type { PropType } from 'vue'
import type { SubMenuItemProps } from './types'
import { rootMenuInjectionKey } from './types'
import '@/layouts/style/menu.scss'

import type { MineRoute } from '#/global'

export default defineComponent({
  name: 'MenuItem',
  props: {
    uniqueKey: Array as PropType<string[]>,
    item: Object as PropType<MineRoute.routeRecord>,
    level: { type: Number, default: 0 },
    subMenu: { type: Boolean, default: false },
    expand: { type: Boolean, default: false },
  },
  setup(props, { expose }) {
    const { t } = useI18n()
    const rootMenu = inject(rootMenuInjectionKey)!
    const itemRef = ref<HTMLElement>()

    const { uniqueKey, item, level, subMenu, expand } = unref(props) as SubMenuItemProps
    const isActive = computed(() => {
      return subMenu
        ? rootMenu.subMenus[uniqueKey!.at(-1)!].active
        : rootMenu.activeIndex === uniqueKey!.at(-1)!
    })

    const isItemActive = computed(() => {
      return isActive.value && (!subMenu || rootMenu.isMenuPopup)
    })

    // 缩进样式
    const indentStyle = computed(() => {
      return !rootMenu.isMenuPopup
        ? `padding-left: ${15 * (level ?? 0)}px`
        : ''
    })

    const arrowIcon = computed(() => {
      return {
        'relative ml-1 w-[10px] after:(absolute h-[1.5px] w-[6px] bg-current transition-transform-200 content-empty -translate-y-[1px]) before:(absolute h-[1.5px] w-[6px] bg-current transition-transform-200 content-empty -translate-y-[1px])': true,
        'before:(-rotate-45 -translate-x-[2px]) after:(rotate-45 translate-x-[2px])': props.expand,
        'before:(rotate-45 -translate-x-[2px]) after:(-rotate-45 translate-x-[2px])': !props.expand,
        'opacity-0': rootMenu.isMenuPopup && level === 0,
        '-rotate-90 -top-[1.5px]': rootMenu.isMenuPopup && level !== 0,
      }
    })

    expose({ ref: itemRef })

    return () => (
      <div ref={itemRef} class={{ 'mine-menu-item': true, 'active': isItemActive.value }}>
        <router-link custom to={uniqueKey?.at(-1) ?? ''}>
          {({ href, navigate }) => (
            <>
              <m-tooltip
                enable={rootMenu.isMenuPopup && level === 0 && !subMenu}
                text={item?.meta?.i18n ? t(item?.meta?.i18n as string) : item?.meta?.title}
                placement="right"
                class="h-full w-full"
              >
                {h(
                  subMenu ? 'div' : 'a',
                  {
                    class: {
                      'mine-menu-link': true,
                      'active': isItemActive.value,
                      'px-3!': rootMenu.isMenuPopup && level === 0,
                      'no-underline': !subMenu,
                    },
                    title: item?.meta?.i18n ? t(item.meta?.i18n as string) : item?.meta?.title,
                    ...(!subMenu && {
                      href: item?.meta?.type === 'L' ? item.meta.link : href,
                      target: item?.meta?.type === 'L' ? '_blank' : '_self',
                      click: navigate,
                    }),
                  },
                  (
                    <>
                      <div class="mine-menu-link-left" style={unref(indentStyle)}>
                        {item?.meta?.icon && <ma-svg-icon name={item.meta.icon} size={20} class="mine-menu-icon" async />}
                        {
                          !(rootMenu.isMenuPopup && level === 0 && !rootMenu.props.showCollapseName)
                          && (
                            <span
                              class={{
                                'title transition-height transition-opacity transition-width': true,
                                'opacity-0 w-0 h-0': rootMenu.isMenuPopup && level === 0 && !rootMenu.props.showCollapseName,
                                'w-full text-center': rootMenu.isMenuPopup && level === 0 && rootMenu.props.showCollapseName,
                              }}
                            >
                              {item?.meta?.i18n ? t(item.meta?.i18n as string) : item?.meta?.title ?? 'unknown'}
                            </span>
                          )
                        }
                      </div>
                      <div
                        class={{
                          'mine-menu-badge': true,
                          'absolute right-10': (subMenu && !(rootMenu.isMenuPopup && level === 0)),
                          'hidden': item.meta?.badge === undefined,
                        }}
                      >
                        {item.meta?.badge?.()}
                      </div>
                      {
                        (subMenu && !(rootMenu.isMenuPopup && level === 0))
                        && <i class={arrowIcon.value} />
                      }
                    </>
                  ),
                )}
              </m-tooltip>
            </>
          )}
        </router-link>
      </div>
    )
  },
})
