import { request } from '@/utils/request.js'

export default {

    /**
     * 获取接口日志分页列表
     * @returns
     */
    getPageList (params = {}) {
        return request({
            url: 'system/logs/getApiLogPageList',
            method: 'get',
            params
        })
    },
  
    /**
     * 删除
     * @returns
     */
    deletes (ids) {
        return request({
            url: 'system/logs/deleteApiLog/' + ids,
            method: 'delete'
        })
    }
}