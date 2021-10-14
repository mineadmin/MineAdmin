import { request } from '@/utils/request.js'

export default {
  /**
   * 获取本地模块分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'setting/module/index',
      method: 'get',
      params
    })
  },

  /**
   * 创建新模块
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'setting/module/save',
      method: 'put',
      params
    })
  },

  /**
   * 删除模块
   * @returns
   */
  deletes (name) {
    return request({
      url: 'setting/module/delete/' + name,
      method: 'delete'
    })
  }
}
