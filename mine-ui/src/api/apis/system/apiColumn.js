import { request } from '@/utils/request.js'

/**
 * 接口字段管理 API JS
 */

export default {

  /**
   * 获取接口字段管理分页列表
   * @returns
   */
  getList (params = {}) {
    return request({
      url: 'system/apiColumn/index',
      method: 'get',
      params
    })
  },

  /**
   * 从回收站获取接口字段管理数据列表
   * @returns
   */
  getRecycleList (params = {}) {
    return request({
      url: 'system/apiColumn/recycle',
      method: 'get',
      params
    })
  },

  /**
   * 添加接口字段管理
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'system/apiColumn/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取接口字段管理
   * @returns
   */
  read (params = {}) {
    return request({
      url: 'system/apiColumn/read',
      method: 'post',
      data: params
    })
  },

  /**
   * 将接口字段管理移到回收站
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'system/apiColumn/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 恢复接口字段管理数据
   * @returns
   */
  recoverys (ids) {
    return request({
      url: 'system/apiColumn/recovery/' + ids,
      method: 'put'
    })
  },

  /**
   * 真实删除接口字段管理
   * @returns
   */
  realDeletes (ids) {
    return request({
      url: 'system/apiColumn/realDelete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新接口字段管理数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'system/apiColumn/update/' + id,
      method: 'put',
      data: params
    })
  },

	/**
	 * 导出
	 * @returns
	 */
	exportExcel (params = {}) {
		return request({
			url: 'system/apiColumn/export',
			method: 'post',
			responseType: 'blob',
			params
		})
	},

	/**
	 * 导入
	 * @returns
	 */
	importExcel (data = {}) {
		return request({
			url: 'system/apiColumn/import',
			method: 'post',
			data
		})
	},

	/**
	 * 下载模板
	 * @returns
	 */
	downloadTemplate () {
		return request({
			url: 'system/apiColumn/downloadTemplate',
			method: 'post',
			responseType: 'blob'
		})
	},

}
