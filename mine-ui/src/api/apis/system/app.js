import { request } from '@/utils/request.js'

/**
 * 应用管理 API JS
 */

export default {

  /**
   * 获取应用管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/app/index',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取应用管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/app/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加应用管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/app/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取应用管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/app/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将应用管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/app/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复应用管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/app/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除应用管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/app/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新应用管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/app/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
   * 获取appid
   * @returns
   */
  getAppId () {
    return request({
      url: 'system/app/getAppId',
      method: 'get'
    })
  },

  /**
   * 获取app secret
   * @returns
   */
   getAppSecret () {
    return request({
      url: 'system/app/getAppSecret',
      method: 'get'
    })
  },

  /**
   * 绑定接口
   * @returns
   */
  bind (id, params = {}) {
    return request({
      url: 'system/app/bind/' + id,
      method: 'put',
      data: params
    })
  },

  /**
   * 获取已绑定接口
   * @returns
   */
   getBindApiList (params = {}) {
    return request({
      url: 'system/app/getApiList',
      method: 'get',
      params
    })
  },


  
}