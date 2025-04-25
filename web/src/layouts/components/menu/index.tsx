/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PropType } from 'vue'
import { TransitionGroup } from 'vue'
import type { MenuInjection, MenuProps } from './types'
import { rootMenuInjectionKey } from './types'
import SubMenu from './sub.tsx'
import MenuItem from './item.tsx'
import type { MineRoute } from '#/global'

export default defineComponent({
  name: 'MineMenu',
  props: {
    menu: Object as PropType<MineRoute.routeRecord[]>,
    value: String,
    accordion: { type: Boolean, default: true },
    defaultOpens: Array as PropType<string[]>,
    mode: { type: String as PropType<'horizontal' | 'vertical'>, default: 'vertical' },
    collapse: { type: Boolean, default: false },
    showCollapseName: { type: Boolean, default: false },
  },
  setup(props) {
    const activeIndex = ref<MenuInjection['activeIndex']>(props.value as string)
    const items = ref<MenuInjection['items']>({})
    const subMenus = ref<MenuInjection['subMenus']>({})
    const openedMenus = ref<MenuInjection['openedMenus']>(props.defaultOpens?.slice(0) as string[])
    const mouseInMenu = ref<MenuInjection['mouseInMenu']>([])
    const isMenuPopup = computed<MenuInjection['isMenuPopup']>(() => {
      return props.mode === 'horizontal' || (props.mode === 'vertical' && props.collapse)
    })

    // 解析传入的 menu 数据，并保存到 items 和 subMenus 对象中
    function initItems(menu: MenuProps['menu'], parentPaths: string[] = []) {
      menu.forEach((item) => {
        const index = item.path ?? JSON.stringify(item)
        if (item?.children && item?.children?.length > 0) {
          const indexPath = [index]
          if (parentPaths.length > 0) {
            indexPath.push(...parentPaths)
          }
          subMenus.value[index] = {
            index,
            indexPath,
            active: false,
          }
          initItems(item.children, indexPath)
        }
        else {
          const indexPath = [index, ...parentPaths]
          subMenus.value[index] = {
            index,
            indexPath,
            active: false,
          }
          items.value[index] = {
            index,
            indexPath,
          }
        }
      })
    }

    const openMenu: MenuInjection['openMenu'] = (index, indexPath) => {
      if (openedMenus.value.includes(index)) {
        return
      }
      if (props.accordion) {
        openedMenus.value = indexPath
      }
      openedMenus.value.push(...indexPath)
    }
    const closeMenu: MenuInjection['closeMenu'] = async (index) => {
      if (Array.isArray(index)) {
        await nextTick(() => {
          closeMenu(index.at(-1)!)
          if (index.length > 1) {
            closeMenu(index.slice(0, -1))
          }
        })
        return
      }
      Object.keys(subMenus.value).forEach((item) => {
        if (subMenus.value[item].indexPath.includes(index)) {
          openedMenus.value = openedMenus.value.filter(item => item !== index)
        }
      })
    }

    function setSubMenusActive(index: string) {
      for (const key in subMenus.value) {
        subMenus.value[key].active = false
      }
      subMenus.value[index]?.indexPath.forEach((idx) => {
        subMenus.value[idx].active = true
      })
      items.value[index]?.indexPath.forEach((idx) => {
        subMenus.value[idx].active = true
      })
    }

    const handleMenuItemClick: MenuInjection['handleMenuItemClick'] = (index) => {
      if (props.mode === 'horizontal' || props.collapse) {
        openedMenus.value = []
      }
      setSubMenusActive(index)
    }
    const handleSubMenuClick: MenuInjection['handleSubMenuClick'] = (index, indexPath) => {
      if (openedMenus.value.includes(index)) {
        closeMenu(index)
      }
      else {
        openMenu(index, indexPath)
      }
    }

    function initMenu() {
      const activeItem = activeIndex.value && items.value[activeIndex.value]
      setSubMenusActive(activeIndex.value)
      if (!activeItem || props.collapse) {
        return
      }

      // 展开该菜单项的路径上所有子菜单
      activeItem.indexPath.forEach((index) => {
        const subMenu = subMenus.value[index]
        subMenu && openMenu(index, subMenu.indexPath)
      })
    }

    watch(() => props.menu as MineRoute.routeRecord[], (val: MineRoute.routeRecord[]) => {
      initItems(val)
      initMenu()
    }, {
      deep: true,
      immediate: true,
    })

    watch(() => props.value, (currentValue) => {
      if (!items.value[currentValue as string]) {
        activeIndex.value = ''
      }
      const item = items.value[currentValue as string] || (activeIndex.value && items.value[activeIndex.value]) || items.value[props.value as string]
      if (item) {
        activeIndex.value = item.index
      }
      else {
        activeIndex.value = currentValue as string
      }
      initMenu()
    })

    watch(() => props.collapse, (value) => {
      if (value) {
        openedMenus.value = []
      }
      initMenu()
    })
    const renderMenu = () => {
      return (
        <div
          class={{
            'transition-all': true,
            'flex-row! w-auto!': isMenuPopup && props.mode === 'horizontal',
          }}
        >
          <TransitionGroup name="mine-menu">
            {props.menu && props.menu.map((item: MineRoute.routeRecord) => {
              if (item.children) {
                return (
                  <div key={item.name}><SubMenu menu={item} unique-key={[item.path ?? JSON.stringify(item)]} /></div>
                )
              }
              else {
                return (
                  <div key={item.name}>
                    <MenuItem
                      item={item}
                      unique-key={[item.path ?? JSON.stringify(item)]}
                      onClick={() => handleMenuItemClick(item.path ?? JSON.stringify(item))}
                    />
                  </div>
                )
              }
            })}
          </TransitionGroup>
        </div>
      )
    }

    provide(rootMenuInjectionKey, reactive({
      props: props as any,
      items,
      subMenus,
      activeIndex,
      openedMenus,
      mouseInMenu,
      isMenuPopup,
      openMenu,
      closeMenu,
      handleMenuItemClick,
      handleSubMenuClick,
    }))

    return () => renderMenu()
  },
})
