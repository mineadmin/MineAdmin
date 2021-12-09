import { request } from '@/utils/request.js'

/**
 * 接口文档
 */

export default {
  /**
   * 登录接口文档
   * @returns
   */
  login (params = {}) {
    return request({
      url: 'apiDoc/login',
      method: 'post',
      data: params
    })
  },

  /**
   * 获取app信息和接口列表
   * @returns
   */
   getAppAndInterfaceList (appId = null) {
    return request({
      url: 'apiDoc/getAppAndInterfaceList/' + appId,
      method: 'get'
    })
  },

  /**
   * 获取字段列表
   * @returns
   */
  getColumnList (apiId = null) {
    return request({
      url: 'apiDoc/getColumnList/' + apiId,
      method: 'get'
    })
  }
}