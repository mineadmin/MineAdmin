import axios from 'axios';
import { ElNotification, ElMessageBox } from 'element-plus';
import sysConfig from "@/config";
import tool from '@/utils/tool';
import { get, isEmpty } from 'lodash'
import qs from 'qs'

function createService () {
	// 创建一个 axios 实例
	const service = axios.create()

	// HTTP request 拦截器
	service.interceptors.request.use(
		config => config ,
		error => {
			// 失败
			return Promise.reject(error);
		}
	);

	// HTTP response 拦截器
	service.interceptors.response.use(
		response => {
			if (response.headers['content-disposition'] && response.status === 200) {
				return response
			}
			if (response.data.code !== 200 || ! response.data.success) {
				ElNotification.error({
					title: '请求错误',
					message: response.data.message
				});
			}
			return response.data;
		},
		error => {
			if (error.response) {
				if (error.response.status == 404) {
					ElNotification.error({
						title: '请求错误',
						message: "服务器资源不存在"
					})
				} else if (error.response.status == 500) {
					ElNotification.error({
						title: '请求错误',
						message: "服务器内部错误！"
					})
				} else if (error.response.status == 401) {
					ElMessageBox.confirm('登录状态已过期，是否重新登录？', '系统提示', {
						confirmButtonText: '重新登录',
						cancelButtonText: '取消',
						type: 'warning'
					}).then(() => {
						tool.data.set('token', null)
						location.href = '/'
					}).catch(() => {})
					return
				} else if (error.response.status == 403) {
					ElNotification.error({
						title: '请求错误',
						message: "没有权限访问该资源"
					})
				} else {
					ElNotification.error({
						title: '请求错误',
						message: `Status:${error.response.status}，未知错误！`
					})
				}
			} else {
				ElNotification.error({
					title: '请求错误',
					message: "请求服务器无响应！"
				})
			}
			return Promise.reject(error.response.data)
		}
	
	)
	return service
}

function stringify (data) {
	return qs.stringify(data, { allowDots: true, encode: false })
}

/**
 * @description 创建请求方法
 * @param {Object} service axios 实例
 */
function createRequest (service) {
	return function (config) {
		const token = tool.data.get('token')
		const configDefault = {
			headers: {
				Authorization: sysConfig.TOKEN_PREFIX + token,
				'Accept-Language': tool.data.get("APP_LANG") || sysConfig.LANG,
				'Content-Type': get(config, 'headers.Content-Type', 'application/json;charset=UTF-8')
			},

			timeout: 5000,
			baseURL: sysConfig.API_URL,
			data: {}
		}
		const option = Object.assign(configDefault, config)

		// 是否缓存请求
		if(!sysConfig.REQUEST_CACHE && option.method === 'get'){
			config.params = config.params || {};
			config.params['_'] = new Date().getTime();
		}

		// json
		if (!isEmpty(option.params)) {
			option.url = option.url + '?' + stringify(option.params)
			option.params = {}
		}
		
		// form
		if (!isEmpty(option.data) && option.headers['Content-Type'] === 'application/x-www-form-urlencoded;charset=UTF-8') {
			option.data = stringify(option.data)
		}
		return service(option)
	}
}
  
// 用于真实网络请求的实例和请求方法
export const service = createService()
export const request = createRequest(service)
