import { request } from '@/utils/request.js'

/**
 * 接口管理 API JS
 */

export default {

  /**
   * 获取接口管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/api/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取模块列表
   * @returns
   */
  getModuleList () {
    return request({
      url: 'system/api/getModuleList',
      method: 'get'
    })
  },

  /**
   * 从回收站获取接口管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/api/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加接口管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/api/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取接口管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/api/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将接口管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/api/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复接口管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/api/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除接口管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/api/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新接口管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/api/update/' + id,
      method: 'put',
      data: params
    })
  }

}