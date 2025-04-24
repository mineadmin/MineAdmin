import { useTimeoutFn } from '@vueuse/core'
import type { PropType } from 'vue'
import type { OverlayScrollbarsComponentRef } from 'overlayscrollbars-vue'
import { Teleport, Transition } from 'vue'
import { OverlayScrollbarsComponent } from 'overlayscrollbars-vue'
import type { SubMenuProps } from './types'
import { rootMenuInjectionKey } from './types'
import type { MineRoute } from '#/global'
import '@/layouts/style/menu.scss'
import Item from '@/layouts/components/menu/item.tsx'
import SubMenu from '@/layouts/components/menu/sub.tsx'

export default defineComponent({
  name: 'SubMenu',
  props: {
    uniqueKey: Array as PropType<string[]>,
    menu: Object as PropType<MineRoute.routeRecord>,
    level: { type: Number, default: 0 },
  },
  setup(props) {
    const { uniqueKey, menu, level } = unref(props) as SubMenuProps

    const index = menu?.path ?? JSON.stringify(menu)
    const itemRef = shallowRef()
    const subMenuRef = shallowRef<OverlayScrollbarsComponentRef>()
    const rootMenu = inject(rootMenuInjectionKey)!

    const opened = computed(() => {
      return rootMenu.openedMenus.includes(uniqueKey.at(-1)!)
    })

    const transitionEvent = computed(() => {
      return rootMenu.isMenuPopup
        ? {
            onEnter(el: HTMLElement) {
              if (el.offsetHeight > window.innerHeight) {
                el.style.height = `${window.innerHeight}px`
              }
            },
            onAfterEnter: () => {},
            onBeforeLeave: (el: HTMLElement) => {
              el.style.overflow = 'hidden'
              el.style.maxHeight = `${el.offsetHeight}px`
            },
            onLeave: (el: HTMLElement) => {
              el.style.maxHeight = '0'
            },
            onAfterLeave(el: HTMLElement) {
              el.style.overflow = ''
              el.style.maxHeight = ''
            },
          }
        : {
            onEnter(el: HTMLElement) {
              const memorizedHeight = el.offsetHeight
              el.style.maxHeight = '0'
              el.style.overflow = 'hidden'
              void el.offsetHeight
              el.style.maxHeight = `${memorizedHeight}px`
            },
            onAfterEnter(el: HTMLElement) {
              el.style.overflow = ''
              el.style.maxHeight = ''
            },
            onBeforeLeave(el: HTMLElement) {
              el.style.overflow = 'hidden'
              el.style.maxHeight = `${el.offsetHeight}px`
            },
            onLeave(el: HTMLElement) {
              el.style.maxHeight = '0'
            },
            onAfterLeave(el: HTMLElement) {
              el.style.overflow = ''
              el.style.maxHeight = ''
            },
          }
    })

    const transitionClass = computed(() => {
      return rootMenu.isMenuPopup
        ? {
            enterActiveClass: 'ease-in-out duration-300',
            enterFromClass: 'opacity-0 translate-x-4',
            enterToClass: 'opacity-100',
            leaveActiveClass: 'ease-in-out duration-300',
            leaveFromClass: 'opacity-100',
            leaveToClass: 'opacity-0',
          }
        : {
            enterActiveClass: 'ease-in-out duration-300',
            enterFromClass: 'opacity-0',
            enterToClass: 'opacity-100',
            leaveActiveClass: 'ease-in-out duration-300',
            leaveFromClass: 'opacity-100',
            leaveToClass: 'opacity-0',
          }
    })

    const hasChildren = computed(() => {
      let flag = true
      if (menu.children) {
        if (menu.children.every((item: any) => item.meta?.menu === false)) {
          flag = false
        }
        let hiddenLen = 0
        menu.children.map((item: any) => {
          if (item.meta?.hidden === true) {
            hiddenLen++
          }
        })

        if (hiddenLen === menu.children.length) {
          flag = false
        }
      }
      else {
        flag = false
      }
      return flag
    })

    function handleClick() {
      if (rootMenu.isMenuPopup && hasChildren.value) {
        return
      }
      if (hasChildren.value) {
        rootMenu.handleSubMenuClick(index, uniqueKey)
      }
      else {
        rootMenu.handleMenuItemClick(index)
      }
    }

    let timeout: (() => void) | undefined

    function handleMouseenter() {
      if (!rootMenu.isMenuPopup) {
        return
      }
      rootMenu.mouseInMenu = uniqueKey
      timeout?.()
      ;({ stop: timeout } = useTimeoutFn(async () => {
        if (hasChildren.value) {
          rootMenu.openMenu(index, uniqueKey)
          await nextTick(() => {
            const el = itemRef.value.ref
            let top = 0
            let left = 0
            if (rootMenu.props.mode === 'vertical' || level !== 0) {
              top = el.getBoundingClientRect().top + el.scrollTop
              left = el.getBoundingClientRect().left + el.getBoundingClientRect().width
              if (top + subMenuRef.value!.getElement()!.offsetHeight > window.innerHeight) {
                top = window.innerHeight - subMenuRef.value!.getElement()!.offsetHeight
              }
            }
            else {
              top = el.getBoundingClientRect().top + el.getBoundingClientRect().height
              left = el.getBoundingClientRect().left
              if (top + subMenuRef.value!.getElement()!.offsetHeight > window.innerHeight) {
                subMenuRef.value!.getElement()!.style.height = `${window.innerHeight - top}px`
              }
            }
            if (left + subMenuRef.value!.getElement()!.offsetWidth > document.documentElement.clientWidth) {
              left = el.getBoundingClientRect().left - el.getBoundingClientRect().width
            }
            subMenuRef.value!.getElement()!.style.top = `${top}px`
            subMenuRef.value!.getElement()!.style.left = `${left}px`
          })
        }
        else {
          const path = menu.children ? rootMenu.subMenus[index].indexPath.at(-1)! : rootMenu.items[index].indexPath.at(-1)!
          rootMenu.openMenu(path, rootMenu.subMenus[path].indexPath)
        }
      }, 300))
    }

    function handleMouseleave() {
      if (!rootMenu.isMenuPopup) {
        return
      }
      rootMenu.mouseInMenu = []
      timeout?.()
      ;({ stop: timeout } = useTimeoutFn(() => {
        if (rootMenu.mouseInMenu.length === 0) {
          rootMenu.closeMenu(uniqueKey)
        }
        else {
          if (hasChildren.value) {
            !rootMenu.mouseInMenu.includes(uniqueKey.at(-1)!) && rootMenu.closeMenu(uniqueKey.at(-1)!)
          }
        }
      }, 300))
    }

    return () => (
      <>
        <Item
          ref={itemRef}
          unique-key={uniqueKey}
          item={menu}
          level={level}
          sub-menu={hasChildren.value}
          expand={opened.value}
          onClick={() => handleClick()}
          onMouseenter={() => handleMouseenter()}
          onMouseleave={() => handleMouseleave()}
        />
        {
          hasChildren.value
          && (
            <Teleport to="body" disabled={!rootMenu.isMenuPopup}>
              <Transition
                {...transitionClass.value}
                {...transitionEvent.value as any}
              >
                {opened.value && (
                  <OverlayScrollbarsComponent
                    defer
                    ref={subMenuRef}
                    options={{ scrollbars: { visibility: 'hidden' } }}
                    class={{
                      'mine-sub-menu': true,
                      'fixed z-3000 min-w-[150px] max-w-[240px] ing-1 shadow-lg rounded-md pb-[7px] pt-[2px] b-1 b-solid b-gray-2 dark-b-dark-4': rootMenu.isMenuPopup,
                      'mx-2': rootMenu.isMenuPopup && (rootMenu.props.mode === 'vertical' || level !== 0),
                    }}
                  >
                    {
                      menu.children && menu.children?.map((item: MineRoute.routeRecord) => (
                        <>
                          {(item?.meta?.type !== 'B') && (
                            <SubMenu
                              key={item.path ?? JSON.stringify(item)}
                              unique-key={[...uniqueKey, item.path ?? JSON.stringify(item)]}
                              menu={item}
                              level={(level as number) + 1}
                            />
                          )}
                        </>
                      ))
                    }
                  </OverlayScrollbarsComponent>
                )}
              </Transition>
            </Teleport>
          )
        }
      </>
    )
  },
})
