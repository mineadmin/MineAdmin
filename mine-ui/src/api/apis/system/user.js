import { request } from '@/utils/request.js'

export default {

    /**
     * 获取用户
     * @returns
     */
    getPageList (params = {}) {
        return request({
            url: 'system/user/index',
            method: 'get',
            params
        })
    },
    
    /**
     * 从回收站获取用户
     * @returns
     */
    getRecyclePageList (params = {}) {
        return request({
            url: 'system/user/recycle',
            method: 'get',
            params
        })
    },
    
    /**
     * 读取一个用户
     * @returns
     */
    read (id) {
        return request({
            url: 'system/user/read/' + id,
            method: 'get'
        })
    },
    
    /**
     * 添加用户
     * @returns
     */
    save (params = {}) {
        return request({
            url: 'system/user/save',
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
            url: 'system/user/delete/' + ids,
            method: 'delete'
        })
    },
    
    /**
     * 恢复数据
     * @returns
     */
    recoverys (ids) {
        return request({
            url: 'system/user/recovery/' + ids,
            method: 'put'
        })
    },
    
    /**
     * 真实删除
     * @returns
     */
    realDeletes (ids) {
        return request({
            url: 'system/user/realDelete/' + ids,
            method: 'delete'
        })
    },
    
    /**
     * 更新数据
     * @returns
     */
    update (id, params = {}) {
        return request({
            url: 'system/user/update/' + id,
            method: 'put',
            data: params
        })
    },
    
    /**
     * 更改用户状态
     * @returns
     */
    changeStatus (params = {}) {
        return request({
            url: 'system/user/changeStatus',
            method: 'put',
            data: params
        })
    },

    /**
     * 清除用户缓存
     * @returns
     */
    clearCache (params = {}) {
        return request({
            url: 'system/user/clearCache',
            method: 'post',
            data: params
        })
    },

    /**
     * 设置用户首页
     * @returns
     */
    setHomePage (params = {}) {
        return request({
            url: 'system/user/setHomePage',
            method: 'post',
            data: params
        })
    },
    
    /**
     * 初始化用户密码
     * @returns
     */
    initUserPassword (id) {
        return request({
            url: 'system/user/initUserPassword/' + id,
            method: 'put'
        })
    },

    /**
     * 用户更新个人资料
     * @returns
     */
    updateInfo (params = {}) {
        return request({
            url: 'system/user/updateInfo',
            method: 'post',
            data: params
        })
    },

    /**
     * 用户修改密码
     * @returns
     */
    modifyPassword (params = {}) {
        return request({
            url: 'system/user/modifyPassword',
            method: 'post',
            data: params
        })
    },

    /**
     * 导出
     * @returns
     */
    exportExcel (params = {}) {
        return request({
            url: 'system/user/export',
            method: 'post',
            responseType: 'blob',
            params
        })
    },

    /**
     * 导入
     * @returns
     */
    importExcel (data = {}) {
        return request({
            url: 'system/user/import',
            method: 'post',
            data
        })
    },

    /**
     * 下载模板
     * @returns
     */
    downloadTemplate () {
        return request({
            url: 'system/user/downloadTemplate',
            method: 'post',
            responseType: 'blob'
        })
    },

    /**
     * 清除自身缓存
     * @returns
     */
    clearSelfCache (params = {}) {
        return request({
            url: 'system/user/clearSelfCache',
            method: 'post',
            data: params
        })
    },
}
