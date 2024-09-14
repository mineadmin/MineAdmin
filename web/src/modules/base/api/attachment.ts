import {ResponseStruct} from "#/global";

interface Attachment {
  id: number;
  storage_mode: string;
  origin_name: string;
  object_name: string;
  hash: string;
  mime_type: string;
  storage_path: string;
  suffix: string;
  size_byte: number;
  size_info: string;
  url: string;
  remark: string;
}

function upload(file:File):Promise<ResponseStruct<Attachment>>
{
  const formData = new FormData()
  formData.append('file', file)
  return useHttp().post('/admin/attachment/upload', formData)
}

function pageList(params:Attachment):Promise<ResponseStruct<Attachment[]>>
{
  return useHttp().get('/admin/attachment/list', {params})
}

function deleteById(id:number):Promise<ResponseStruct<null>>
{
  return useHttp().delete(`/admin/attachment/${id}`)
}
