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
import useHttp from '@/hooks/auto-imports/useHttp.ts'

export interface UserLoginVo {
  id: number // 主键
  username: string
  ip: string
  os: string
  browser: string
  status: number // 登录状态 (1成功 2失败)
  message: string
  login_time: string
  remark: string
}

export interface RequestLogInfoVo {
  // 主键
  id: number
  // 用户名
  username: string
  // 请求方式，例如GET、POST等
  method: string
  // 请求的路由地址
  router: string
  // 业务名称
  service_name: string
  // 请求的IP地址
  ip: string
  created_at: string
  updated_at: string
  // 备注信息
  remark: string
}

export class UserLoginLog {
  public static page(params: UserLoginVo): Promise<ResponseStruct<UserLoginVo[]>> {
    return useHttp().get('/admin/user-login-log/list', { params })
  }

  public static delete(ids: number[]): Promise<ResponseStruct<null>> {
    return useHttp().delete('/admin/user-login-log', { data: { ids } })
  }
}

export class UserOperatorLog {
  public static page(params: RequestLogInfoVo): Promise<ResponseStruct<RequestLogInfoVo[]>> {
    return useHttp().get('/admin/user-operation-log/list', { params })
  }

  public static delete(ids: number[]): Promise<ResponseStruct<null>> {
    return useHttp().delete('/admin/user-operation-log', { data: { ids } })
  }
}
