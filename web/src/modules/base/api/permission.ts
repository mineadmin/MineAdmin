// This is the type of the permission object
import {ResponseStruct} from "#/global";

export type MenuType = 'M' | 'B' | 'I'

export type Menu = {
  id:number,
  parent_id:number,
  name:string,
  code:string,
  icon:null|string,
  route:null|string,
  component:null|string,
  redirect:null|string,
  is_hidden:number,
  type:MenuType,
  status:number,
  sort:number,
  children:null|Menu[],
}

export type Role = {
  name:string,
  code:string,
  remark:string
}

/**
 * Get Current User's Menu
 */
export function getMenus():Promise<ResponseStruct<Menu[]>> {
  return useHttp().get('/admin/permission/menus')
}

/**
 * Get Current User's Roles
 */
export function getRoles():Promise<ResponseStruct<Role[]>> {
  return useHttp().get('/admin/permission/roles')
}
