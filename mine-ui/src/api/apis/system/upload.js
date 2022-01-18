import { request } from '@/utils/request.js'

export default {

  /**
   * 获取目录列表
   * @returns
   */
  getDirectory (params = {}) {
    return request({
      url: 'system/getDirectory',
      method: 'get',
      params
    })
  },

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
   * 创建上传目录
   * @returns
   */
  createUploadDir (params = {}) {
    return request({
      url: 'system/createUploadDir',
      method: 'post',
      params
    })
  },

  /**
   * 删除目录
   * @returns
   */
  deleteUploadDir (data = {}) {
    return request({
      url: 'system/deleteUploadDir',
      method: 'post',
      data
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