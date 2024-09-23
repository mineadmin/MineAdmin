import type { ResponseStruct } from '#/global'

export interface AttachmentVo {
  id?: number
  storage_mode?: string
  origin_name?: string
  object_name?: string
  hash?: string
  mime_type?: string
  storage_path?: string
  suffix?: string
  size_byte?: number
  size_info?: string
  url?: string
  remark?: string
}

export function upload(file: File): Promise<ResponseStruct<AttachmentVo>> {
  const formData = new FormData()
  formData.append('file', file)
  return useHttp().post('/admin/attachment/upload', formData)
}

export function pageList(params: AttachmentVo): Promise<ResponseStruct<AttachmentVo[]>> {
  return useHttp().get('/admin/attachment/list', { params })
}

export function deleteById(id: number): Promise<ResponseStruct<null>> {
  return useHttp().delete(`/admin/attachment/${id}`)
}
