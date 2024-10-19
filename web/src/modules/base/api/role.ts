/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PageList, ResponseStruct } from '#/global'

export interface RoleVo {
  id?: number
  name?: string
  code?: string
  data_scope?: number
  status?: number
  sort?: number
  remark?: string
}

export interface RoleSearchVo {
  name?: string
  code?: string
  status?: number
  [key: string]: any
}

export function page(data: RoleSearchVo): Promise<ResponseStruct<PageList<RoleVo>>> {
  return useHttp().get('/admin/role/list', { params: data })
}

export function create(data: RoleVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/role', data)
}

export function save(id: number, data: RoleVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/role/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/role', { data: ids })
}

export function getRolePermission(id: number): Promise<ResponseStruct<null>> {
  return useHttp().get(`/admin/role/${id}/permissions`)
}

export function setRolePermission(id: number, permissions: string[]): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/role/${id}/permissions`, { permissions })
}
