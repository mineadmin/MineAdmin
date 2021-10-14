import { request } from '@/utils/request.js'

export default {
    /**
     * 快捷查询字典
     * @param {*} params
     * @returns
     */
    getDict(code) {
        return request({
            url: 'system/dataDict/list?code=' + code,
            method: 'get'
        })
    },

    /**
     * 获取字典数据分页列表
     * @returns
     */
    getPageList(params = {}) {
        return request({
            url: 'system/dataDict/index',
            method: 'get',
            params
        })
    },

    /**
     * 从回收站获取字典数据
     * @returns
     */
    getRecyclePageList(params = {}) {
        return request({
            url: 'system/dataDict/recycle',
            method: 'get',
            params
        })
    },

    /**
     * 添加字典数据
     * @returns
     */
    saveDictData(params = {}) {
        return request({
            url: 'system/dataDict/save',
            method: 'post',
            data: params
        })
    },

    /**
     * 移到回收站
     * @returns
     */
    deletesDictData(ids) {
        return request({
            url: 'system/dataDict/delete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 恢复数据
     * @returns
     */
    recoverysDictData(ids) {
        return request({
            url: 'system/dataDict/recovery/' + ids,
            method: 'put'
        })
    },

    /**
     * 真实删除
     * @returns
     */
    realDeletesDictData(ids) {
        return request({
            url: 'system/dataDict/realDelete/' + ids,
            method: 'delete'
        })
    },

    /**
     * 更新数据
     * @returns
     */
    updateDictData(id, params = {}) {
        return request({
            url: 'system/dataDict/update/' + id,
            method: 'put',
            data: params
        })
    },

    /**
     * 清空缓存
     * @returns
     */
    clearCache() {
        return request({
            url: 'system/dataDict/clearCache',
            method: 'post'
        })
    }

}