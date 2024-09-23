import type { PageList, ResponseStruct } from '#/global'

interface Role {
  id?: number
  name: string
  code: string
  data_scope: number
  status: number
  sort: number
  remark: string
}

export interface RoleSearch {
  name?: string
  code?: string
  status?: number
}

export function page(data: RoleSearch): Promise<ResponseStruct<PageList<Role>>> {
  return useHttp().get('/admin/role/list', { params: data })
}

export function create(data: Role): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/role', data)
}

export function save(id: number, data: Role): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/role/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/role', { data: ids })
}

export function batchGrantPermissionsForRole(id: number, permission_ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().post(`/admin/role/${id}/permissions`, permission_ids)
}
