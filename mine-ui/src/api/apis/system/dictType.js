import { request } from '@/utils/request.js'

export default {

    /**
     * 获取字典类型，无分页
     * @returns
     */
    getTypeList(params = {}) {
        return request({
            url: 'system/dictType/index',
            method: 'get',
            params
        })
    },

    /**
     * 从回收站获取字典类型
     * @returns
     */
    getRecycleTypeList(params = {}) {
        return request({
            url: 'system/dictType/recycle',
            method: 'get',
            params
        })
    },

    /**
     * 添加字典类型
     * @returns
     */
    save(params = {}) {
        return request({
            url: 'system/dictType/save',
            method: 'post',
            data: params
        })
    },

    /**
     * 移到回收站
     * @returns
     */
    deletes(ids) {
        return request({
            url: 'system/dictType/delete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 恢复数据
     * @returns
     */
    recoverys(ids) {
        return request({
            url: 'system/dictType/recovery/' + ids,
            method: 'put'
        })
    },

    /**
     * 真实删除
     * @returns
     */
    realDelete(ids) {
        return request({
            url: 'system/dictType/realDelete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 更新数据
     * @returns
     */
    update(id, params = {}) {
        return request({
            url: 'system/dictType/update/' + id,
            method: 'put',
            data: params
        })
    },
}