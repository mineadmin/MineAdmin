import { request } from '@/utils/request.js'

export default {
  /**
   * 获取表设计的必要系统信息
   * @returns
   */
  getSystemInfo () {
    return request({
      url: 'setting/table/getSystemInfo',
      method: 'get'
    })
  },

  /**
   * 保存表
   * @returns
   */
  save (data = {}) {
    return request({
      url: 'setting/table/save',
      method: 'put',
      data
    })
  }
}

