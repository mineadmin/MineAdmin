import type { ResponseStruct } from '#/global'

export interface MenuVo {
  id?: number
  parent_id?: number
  name: string
  meta?: Record<string, any>
  component?: string
  redirect?: string
  status?: number
  sort?: number
  remark?: string
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
