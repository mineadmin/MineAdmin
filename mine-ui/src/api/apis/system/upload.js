import { request } from '@/utils/request.js'

export default {

  /**
   * 获取所有文件
   * @returns
   */
  getAllFiles (params = {}) {
    return request({
      url: 'system/getAllFiles',
      method: 'get',
      params
    })
  },

  /**
   * 上传图片接口
   * @returns
   */
  uploadImage (data = {}) {
    return request({
      url: 'system/uploadImage',
      method: 'post',
      timeout: 30000,
      data
    })
  },

  /**
   * 上传文件接口
   * @returns
   */
  uploadFile (data = {}) {
    return request({
      url: 'system/uploadFile',
      method: 'post',
      timeout: 30000,
      data
    })
  },

  /**
   * 保存网络图片
   * @returns
   */
  saveNetWorkImage (data = {}) {
    return request({
      url: 'system/saveNetworkImage',
      method: 'post',
      data
    })
  },
}