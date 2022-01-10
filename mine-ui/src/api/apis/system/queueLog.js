import { request } from '@/utils/request.js'

export default {

    /**
     * 获取队列日志分页列表
     * @returns
     */
    getPageList (params = {}) {
        return request({
            url: 'system/logs/getQueueLogPageList',
            method: 'get',
            params
        })
    },
  
    /**
     * 删除队列日志
     * @returns
     */
    deletes (ids) {
        return request({
            url: 'system/logs/deleteQueueLog/' + ids,
            method: 'delete'
        })
    }
}