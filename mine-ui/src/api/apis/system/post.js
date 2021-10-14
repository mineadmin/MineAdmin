import { request } from '@/utils/request.js'

export default {
  /**
   * 获取岗位分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'system/post/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取岗位列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/post/list',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取岗位
   * @returns
   */
  getRecyclePageList (params = {}) {
    return request({
      url: 'system/post/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加岗位
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/post/save',
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
      url: 'system/post/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/post/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/post/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/post/update/' + id,
      method: 'put',
      data: params
    })
  }

}