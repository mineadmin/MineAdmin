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

export interface DepartmentVo {
  id?: number
  name?: string
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
