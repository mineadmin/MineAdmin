import {PageList, ResponseStruct} from "#/global";

interface Menu {
  id?: number;
  parent_id?: number;
  name: string;
  code: string;
  icon: string;
  route: string;
  component: string;
  redirect: string;
  is_hidden: 1|2;
  type: string;
  status: number;
  sort: number;
  remark: string;
}


export function page(data:Menu):Promise<ResponseStruct<PageList<Menu>>>
{
  return useHttp().get('/admin/menu/list', {params: data})
}


export function create(data:Menu):Promise<ResponseStruct<null>>
{
  return useHttp().post('/admin/menu', data)
}

export function save(id:number,data:Menu):Promise<ResponseStruct<null>>
{
  return useHttp().put(`/admin/menu/${id}`, data)
}

export function deleteByIds(ids:number):Promise<ResponseStruct<null>>
{
  return useHttp().delete('/admin/menu',ids)
}
