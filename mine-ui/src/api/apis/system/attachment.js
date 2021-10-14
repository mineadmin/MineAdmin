import { request } from '@/utils/request.js'

export default {

  /**
   * 获取文件分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'system/attachment/index',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取文件
   * @returns
   */
  getRecyclePageList (params = {}) {
    return request({
      url: 'system/attachment/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/attachment/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/attachment/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/attachment/realDelete/' + ids,
      method: 'delete'
    })
  }
}