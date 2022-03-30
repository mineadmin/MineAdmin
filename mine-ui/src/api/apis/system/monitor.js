import { request } from '@/utils/request.js'

export default {

  /**
   * 获取服务器信息
   * @returns
   */
  getServerInfo () {
    return request({
      url: 'system/server/monitor',
      method: 'get'
    })
  },

  /**
   * 获取依赖包列表
   * @returns
   */
  getPackagePageList (params = {}) {
    return request({
      url: 'system/rely/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取依赖包详细
   * @returns
   */
  getPackageDetail (name) {
    return request({
      url: 'system/rely/detail?name='+name,
      method: 'get',
    })
  },

  /**
   * 获取在线用户列表
   * @param {*} params 
   * @returns 
   */
  getOnlineUserPageList (params = {}) {
    return request({
      url: 'system/onlineUser/index',
      method: 'get',
      params
    })
  },

  /**
   * 强退用户 （踢下线）
   * @param {*} params 
   * @returns 
   */
  kickUser (params = {}) {
    return request({
      url: 'system/onlineUser/kick',
      method: 'post',
      data: params
    })
  },

  /**
   * 获取缓存信息
   * @returns
   */
  getCacheInfo () {
    return request({
      url: 'system/cache/monitor',
      method: 'get'
    })
  },

  /**
   * 查看key内容
   * @returns
   */
  view (data) {
    return request({
      url: 'system/cache/view',
      method: 'post',
      data
    })
  },
  

  /**
   * 删除一个缓存
   * @returns
   */
  deleteKey (data) {
    return request({
      url: 'system/cache/delete',
      method: 'delete',
      data
    })
  },

  /**
   * 清空缓存
   * @returns
   */
  clear () {
    return request({
      url: 'system/cache/clear',
      method: 'delete'
    })
  },
}