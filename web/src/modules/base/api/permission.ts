/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
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
