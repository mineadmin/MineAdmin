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
import type { LeaderVo } from '~/base/api/leader.ts'
import type { PositionVo } from '~/base/api/position.ts'

export interface DepartmentUserPivotVo {
  dept_id?: number
  user_id?: number
}

export interface DepartmentUserVo {
  id?: number
  username?: string
  nickname?: string
  avatar?: string
  phone?: string
  email?: string
  pivot?: DepartmentUserPivotVo
  [key: string]: any
}

export interface DepartmentVo {
  id?: number
  name?: string
  parent_id?: number | null
  created_at?: string | null
  updated_at?: string | null
  deleted_at?: string | null
  children?: DepartmentVo[]
  leader?: LeaderVo[]
  positions?: PositionVo[]
  department_users?: DepartmentUserVo[]
  [key: string]: any
}

export interface DepartmentSearchVo {
  name?: string
  [key: string]: any
}

export function page(data: DepartmentSearchVo | null = null): Promise<ResponseStruct<PageList<DepartmentVo>>> {
  return useHttp().get('/admin/department/list?level=1', { params: data })
}

export function create(data: DepartmentVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/department', data)
}

export function save(id: number, data: DepartmentVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/department/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/department', { data: ids })
}
