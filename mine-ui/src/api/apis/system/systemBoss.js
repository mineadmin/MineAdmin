import { request } from '@/utils/request.js'

/**
 * 老板管理 API JS
 */

export default {

  /**
   * 获取老板管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/boss/index',
      method: 'get',
      params
    })
  },

  /**
   * 添加老板管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/boss/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 更新老板管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/boss/update/' + id,
      method: 'put',
      data: params
    })
  },

  /**
   * 读取老板管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/boss/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将老板管理删除，有软删除则移动到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/boss/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 从回收站获取老板管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/boss/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 恢复老板管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/boss/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除老板管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/boss/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更改老板管理数据
   * @returns
   */
  changeStatus (data = {}) {
    return request({
      url: 'system/boss/changeStatus',
      method: 'put',
      data
    })
  },

  /**
   * 修改老板管理数值数据，自增自减
   * @returns
   */
  numberOperation (data = {}) {
    return request({
      url: 'system/boss/numberOperation',
      method: 'put',
      data
    })
  },

  /**
    * 老板管理导入
    * @returns
    */
  importExcel (data = {}) {
    return request({
      url: 'system/boss/import',
      method: 'post',
      data
    })
  },

  /**
   * 老板管理下载模板
   * @returns
   */
  downloadTemplate () {
    return request({
      url: 'system/boss/downloadTemplate',
      method: 'post',
      responseType: 'blob'
    })
  },

  /**
   * 老板管理导出
   * @returns
   */
  exportExcel (params = {}) {
    return request({
      url: 'system/boss/export',
      method: 'post',
      responseType: 'blob',
      params
    })
  },


}