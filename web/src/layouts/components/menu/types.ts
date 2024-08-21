import { createInjectionKey } from '@/utils/injectionKeys'
import type { MineRoute } from '#/global'

export interface MenuItem {
  index: string
  indexPath: string[]
  active?: boolean
}

export interface MenuProps {
  menu: MineRoute.routeRecord[]
  value: string
  accordion?: boolean
  defaultOpens?: string[]
  mode?: 'horizontal' | 'vertical'
  collapse?: boolean
  showCollapseName?: boolean
}

export interface MenuInjection {
  props: MenuProps
  items: Record<string, MenuItem>
  subMenus: Record<string, MenuItem>
  activeIndex: MenuProps['value']
  openedMenus: string[]
  mouseInMenu: string[]
  isMenuPopup: boolean
  openMenu: (index: string, indexPath: string[]) => void
  closeMenu: (index: string | string[]) => void
  handleMenuItemClick: (index: string) => void
  handleSubMenuClick: (index: string, indexPath: string[]) => void
}

export const rootMenuInjectionKey = createInjectionKey<MenuInjection>('rootMenu')

export interface SubMenuProps {
  uniqueKey: string[]
  menu: MineRoute.routeRecord
  level?: number
}

export interface SubMenuItemProps {
  uniqueKey: string[]
  item: MineRoute.routeRecord
  level?: number
  subMenu?: boolean
  expand?: boolean
}
