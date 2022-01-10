import { request } from '@/utils/request.js'

/**
 * 系统公告 API JS
 */

export default {

  /**
   * 获取系统公告分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/notice/index',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取系统公告数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/notice/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加系统公告
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/notice/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取系统公告
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/notice/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将系统公告移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/notice/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复系统公告数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/notice/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除系统公告
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/notice/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新系统公告数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/notice/update/' + id,
      method: 'put',
      data: params
    })
  }

}