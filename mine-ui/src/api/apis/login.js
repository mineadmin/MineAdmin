import { request } from '@/utils/request.js'


export default {

    /**
     * 获取验证码
     * @returns
     */
    getCaptch () {
        return request({
            url: 'system/captcha',
            method: 'get'
        })
    },

    /**
     * 用户登录
     * @param {object} params
     * @returns
     */
    Login (params = {}) {
        return request({
            url: 'system/login',
            method: 'post',
            data: params
        })
    },

    /**
     * 用户退出
     * @param {object} params
     * @returns
     */
    Logout (params = {}) {
        return request({
            url: 'system/logout',
            method: 'post',
            data: params
        })
    },

    /**
     * 获取登录用户信息
     * @param {object} params
     * @returns
     */
    getInfo (params = {}) {
        return request({
            url: 'system/getInfo',
            method: 'get',
            data: params
        })
    }
}
