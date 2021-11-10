import { request } from '@/utils/request.js'

export default {

  /**
   * 获取系统组配置 （不验证身份和权限）
   * @returns
   */
  getSysConfig () {
    return request({
      url: 'setting/config/getSysConfig',
      method: 'get',
    })
  },

  /**
   * 获取系统组配置
   * @returns
   */
  getSystemConfig () {
    return request({
      url: 'setting/config/getSystemConfig',
      method: 'get',
    })
  },

  /**
   * 获取扩展组配置
   * @returns
   */
  getExtendConfig () {
    return request({
      url: 'setting/config/getExtendConfig',
      method: 'get',
    })
  },

  /**
   * 清除缓存
   * @returns
   */
  clearCache (params = {}) {
    return request({
      url: 'setting/config/clearCache',
      method: 'post',
      data: params
    })
  },

  /**
   * 保存系统组配置
   * @returns
   */
  saveSystemConfig (params = {}) {
    return request({
      url: 'setting/config/saveSystemConfig',
      method: 'post',
      data: params
    })
  },

  /**
   * 删除配置
   * @returns
   */
  delete (key) {
    return request({
      url: 'setting/config/delete/' + key,
      method: 'delete',
    })
  },

  /**
   * 保存配置
   * @returns
   */
  save (params = {}) {
    return request({
      url: 'setting/config/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 保存配置
   * @returns
   */
  update (params = {}) {
    return request({
      url: 'setting/config/update',
      method: 'post',
      data: params
    })
  },

  /**
   * 删除配置
   * @returns
   */
  getConfigByKey (data) {
    return request({
      url: 'setting/config/getConfigByKey',
      method: 'post',
      data
    })
  },
}