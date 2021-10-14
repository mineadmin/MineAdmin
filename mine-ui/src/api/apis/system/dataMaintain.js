import { request } from '@/utils/request.js'

export default {
  /**
   * 获取数据表分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'system/dataMaintain/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取表字段列表
   * @returns
   */
  getDetailed (table) {
    return request({
      url: 'system/dataMaintain/detailed?table=' + table,
      method: 'get'
    })
  },

  /**
   * 优化表
   * @returns
   */
  optimize (data = {}) {
    return request({
      url: 'system/dataMaintain/optimize',
      method: 'post',
      data
    })
  },

  /**
   * 清理表碎片
   * @returns
   */
  fragment (data = {}) {
    return request({
      url: 'system/dataMaintain/fragment',
      method: 'post',
      data
    })
  }
}