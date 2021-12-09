import { request } from '@/utils/request.js'

/**
 * 接口分组 API JS
 */

export default {

  /**
   * 获取接口分组分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/apiGroup/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取接口分组分页列表，无分页，下拉用
   * @returns
   */
  getSelectList (params = {}) {
    return request({
      url: 'system/apiGroup/list',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取接口分组数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/apiGroup/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加接口分组
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/apiGroup/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取接口分组
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/apiGroup/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将接口分组移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/apiGroup/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复接口分组数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/apiGroup/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除接口分组
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/apiGroup/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新接口分组数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/apiGroup/update/' + id,
      method: 'put',
      data: params
    })
  }

}