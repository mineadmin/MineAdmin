/**
 * 計分業務 - 房間管理接口（方案 §7.4）
 */
import type { PageList, ResponseStruct } from '#/global'

const http = useHttp()

export interface RoomVo {
  id?: number
  room_no?: string
  owner_id?: number
  status?: number
  member_limit?: number
  current_round?: number
  current_battle_id?: number | null
  score_version?: number
  finished_at?: string | null
  battle_count?: number
  record_count?: number
  created_at?: string
}

export interface RoomSearchVo {
  room_no?: string
  owner_id?: number
  status?: number
}

export interface RoomDetailVo {
  room: RoomVo
  members: Record<string, any>[]
  records: Record<string, any>[]
  battles: Record<string, any>[]
  battle_members: Record<string, any>[]
  transfers: Record<string, any>[]
}

export function page(params: RoomSearchVo): Promise<ResponseStruct<PageList<RoomVo>>> {
  return http.get('/admin/scoring/room', { params })
}

export function detail(id: number): Promise<ResponseStruct<RoomDetailVo>> {
  return http.get(`/admin/scoring/room/${id}`)
}
