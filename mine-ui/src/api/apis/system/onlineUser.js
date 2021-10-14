import { request } from '@/utils/request.js'

export default {

    /**
     * 获取在线用户列表
     * @param {*} params 
     * @returns 
     */
    getPageList (params = {}) {
        return request({
            url: 'system/onlineUser/index',
            method: 'get',
            params
        })
    },

    /**
     * 强退用户 （踢下线）
     * @param {*} params 
     * @returns 
     */
    kickUser (params = {}) {
        return request({
            url: 'system/onlineUser/kick',
            method: 'post',
            data: params
        })
    }

}