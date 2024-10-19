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
