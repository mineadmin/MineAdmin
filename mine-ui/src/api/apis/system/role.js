import { request } from '@/utils/request.js'

export default {
  /**
   * 获取角色分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'system/role/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取角色列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/role/list',
      method: 'get',
      params
    })
  },

  /**
   * 通过角色获取菜单
   * @returns
   */
  getMenuByRole (id) {
    return request({
      url: 'system/role/getMenuByRole/' + id,
      method: 'get'
    })
  },

  /**
   * 通过角色获取部门
   * @returns
   */
  getDeptByRole (id) {
    return request({
      url: 'system/role/getDeptByRole/' + id,
      method: 'get'
    })
  },

  /**
   * 从回收站获取角色
   * @returns
   */
  getRecyclePageList (params = {}) {
    return request({
      url: 'system/role/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加角色
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/role/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/role/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/role/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/role/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/role/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
   * 更改角色状态
   * @returns
   */
   changeStatus (params = {}) {
    return request({
      url: 'system/role/changeStatus',
      method: 'put',
      data: params
    })
  }

}