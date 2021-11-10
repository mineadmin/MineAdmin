import { request } from '@/utils/request.js'

export default {
    /**
     * 获取部门树
     * @returns
     */
    getList (params = {}) {
        return request({
            url: 'system/dept/index',
            method: 'get',
            params
        })
    },

  
    /**
     * 从回收站获取部门树
     * @returns
     */
    getRecycleList (params = {}) {
        return request({
            url: 'system/dept/recycle',
            method: 'get',
            params
        })
    },


    /**
     * 获取部门选择树
     * @returns
     */
    tree () {
        return request({
            url: 'system/dept/tree',
            method: 'get'
        })
    },


    /**
     * 添加部门
     * @returns
     */
    save (params = {}) {
        return request({
            url: 'system/dept/save',
            method: 'post',
            data: params
        })
    },


    /**
     * 移到回收站
     * @returns
     */
    deletes (ids) {
        return request({
            url: 'system/dept/delete/' + ids,
            method: 'delete'
        })
    },


    /**
     * 恢复数据
     * @returns
     */
    recoverys (ids) {
        return request({
            url: 'system/dept/recovery/' + ids,
            method: 'put'
        })
    },


    /**
     * 真实删除
     * @returns
     */
    realDeletes (ids) {
        return request({
            url: 'system/dept/realDelete/' + ids,
            method: 'delete'
        })
    },


    /**
     * 更新数据
     * @returns
     */
    update (id, params = {}) {
        return request({
            url: 'system/dept/update/' + id,
            method: 'put',
            data: params
        })
    },

    /**
     * 更改部门状态
     * @returns
     */
    changeStatus (params = {}) {
        return request({
            url: 'system/dept/changeStatus',
            method: 'put',
            data: params
        })
    },
}
