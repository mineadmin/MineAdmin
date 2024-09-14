import {PageList, ResponseStruct} from "#/global";

interface User {
  id: number;
  username: string;
  user_type: number;
  nickname: string;
  phone: string;
  email: string;
  avatar: string;
  signed: string;
  dashboard: string;
  status: 1|2;
  login_ip: string;
  login_time: string;
  backend_setting: any[];
  remark: string;
  password: string;
}

export interface UserSearch {
  username?: string;
  nickname?: string;
  phone?: string;
  email?: string;
  status?: number;
}

export function page(data:UserSearch):Promise<ResponseStruct<PageList<User>>>
{
  return useHttp().get('/admin/user/list', {params: data})
}

export function create(data:User):Promise<ResponseStruct<null>>
{
  return useHttp().post('/admin/user', data)
}

export function save(id:number,data:User):Promise<ResponseStruct<null>>
{
  return useHttp().put(`/admin/user/${id}`, data)
}

export function deleteByIds(ids:number):Promise<ResponseStruct<null>>
{
  return useHttp().delete('/admin/user',ids)
}

export function batchGrantRolesForUser(id:number,role_codes:string[]):Promise<ResponseStruct<null>>
{
  return useHttp().post(`/admin/user/${id}/roles`, {role_codes: role_codes})
}
