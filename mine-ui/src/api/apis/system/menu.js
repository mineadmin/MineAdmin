import { request } from '@/utils/request.js'

export default {
    /**
     * 获取菜单树
     * @returns
     */
    getList (params = {}) {
        return request({
            url: 'system/menu/index',
            method: 'get',
            params
        })
    },

    /**
     * 从回收站获取菜单树
     * @returns
     */
    getRecycle (params = {}) {
        return request({
            url: 'system/menu/recycle',
            method: 'get',
            params
        })
    },

    /**
     * 获取菜单选择树
     * @returns
     */
    tree (params = {}) {
        return request({
            url: 'system/menu/tree',
            method: 'get',
            params
        })
    },

    /**
     * 添加菜单
     * @returns
     */
    save (params = {}) {
        return request({
            url: 'system/menu/save',
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
            url: 'system/menu/delete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 恢复数据
     * @returns
     */
    recoverys (ids) {
        return request({
            url: 'system/menu/recovery/' + ids,
            method: 'put'
        })
    },

    /**
     * 真实删除
     * @returns
     */
    realDeletes (ids) {
        return request({
            url: 'system/menu/realDelete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 更新数据
     * @returns
     */
    update (id, params = {}) {
        return request({
            url: 'system/menu/update/' + id,
            method: 'put',
            data: params
        })
    },
}
