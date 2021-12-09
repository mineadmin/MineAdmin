import { request } from '@/utils/request.js'

export default {
  /**
   * 获取分页列表
   * @returns
   */
  getPageList (params = {}) {
    return request({
      url: 'setting/crontab/index',
      method: 'get',
      params
    })
  },

  /**
   * 获取任务日志列表
   * @returns
   */
  getLogPageList (params = {}) {
    return request({
      url: 'setting/crontab/logPageList',
      method: 'get',
      params
    })
  },
  
  /**
   * 删除定时任务日志
   * @returns
   */
  deleteLog (ids) {
    return request({
      url: 'setting/crontab/deleteCrontabLog/' + ids,
      method: 'delete'
    })
  },

  /**
   * 立刻执行一次定时任务
   * @returns
   */
   run (params = {}) {
    return request({
      url: 'setting/crontab/run',
      method: 'post',
      data: params
    })
  },

  /**
   * 添加
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'setting/crontab/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 删除
   * @returns
   */
  deletes (ids) {
    return request({
      url: 'setting/crontab/delete/' + ids,
      method: 'delete'
    })
  },

  /**
   * 更新数据
   * @returns
   */
  update (id, params = {}) {
    return request({
      url: 'setting/crontab/update/' + id,
      method: 'put',
      data: params
    })
  }

}