/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { ResponseStruct } from '#/global'

export interface MenuVo {
  id?: number
  parent_id?: number
  name?: string
  path?: string
  meta?: Record<string, any>
  component?: string
  redirect?: string
  status?: number
  sort?: number
  remark?: string
  btnPermission?: MenuVo[]
  [key: string]: any
}

export function page(): Promise<ResponseStruct<MenuVo[]>> {
  return useHttp().get('/admin/menu/list')
}

export function create(data: MenuVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/menu', data)
}

export function save(id: number, data: MenuVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/menu/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/menu', { data: ids })
}
