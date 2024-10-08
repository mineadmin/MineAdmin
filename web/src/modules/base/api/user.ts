import type { PageList, ResponseStruct } from '#/global'

export interface UserVo {
  id?: number
  username?: string
  user_type?: number
  nickname?: string
  phone?: string
  email?: string
  avatar?: string
  signed?: string
  dashboard?: string
  status?: 1 | 2
  login_ip?: string
  login_time?: string
  backend_setting?: Record<string, any>
  remark?: string
  password?: string
}

export interface UserSearchVo {
  username?: string
  nickname?: string
  phone?: string
  email?: string
  status?: number
}

export function page(data: UserSearchVo): Promise<ResponseStruct<PageList<UserVo>>> {
  return useHttp().get('/admin/user/list', { params: data })
}

export function create(data: UserVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/user', data)
}

export function save(id: number, data: UserVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/user/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/user', { data: ids })
}

export function resetPassword(id: number): Promise<ResponseStruct<null>> {
  return useHttp().put('/admin/user/password', { id })
}

export function updateInfo(data: UserVo): Promise<ResponseStruct<null>> {
  return useHttp().put('/admin/user/info', data)
}

export function getUserRole(id: number): Promise<ResponseStruct<any[]>> {
  return useHttp().get(`/admin/user/${id}/roles`)
}

export function setUserRole(id: number, role_codes: string[]): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/user/${id}/roles`, { role_codes })
}
