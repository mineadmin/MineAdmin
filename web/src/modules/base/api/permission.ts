// This is the type of the permission object
import type { MenuVo } from './menu'
import type { RoleVo } from './role'
import type { ResponseStruct } from '#/global'

/**
 * Get Current User's Menu
 */
export function getMenus(): Promise<ResponseStruct<MenuVo[]>> {
  return useHttp().get('/admin/permission/menus')
}

/**
 * Get Current User's Roles
 */
export function getRoles(): Promise<ResponseStruct<RoleVo[]>> {
  return useHttp().get('/admin/permission/roles')
}

export {
  MenuVo,
  RoleVo,
}
