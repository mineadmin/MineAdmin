import { request } from '@/utils/request.js'


export default {

    /**
     * 获取用户列表
     * @returns
     */
    getUserList (params = {}) {
        return request({
            url: 'system/common/getUserList',
            method: 'get',
            params
        })
    },

    /**
     * 获取部门列表
     * @returns
     */
    getDeptTreeList (params = {}) {
        return request({
            url: 'system/common/getDeptTreeList',
            method: 'get',
            params
        })
    },

    /**
     * 获取角色列表
     * @returns
     */
    getRoleList (params = {}) {
        return request({
            url: 'system/common/getRoleList',
            method: 'get',
            params
        })
    },

    /**
     * 获取岗位列表
     * @returns
     */
    getPostList (params = {}) {
        return request({
            url: 'system/common/getPostList',
            method: 'get',
            params
        })
    },
}
