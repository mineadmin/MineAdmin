
/**
 * 所有接口集合
 * 每个接口对象需含有以下字段
 * @url 接口的URL地址
 * @name 接口名称
 * @get|post 返回请求接口的函数
 */

const path = require.context('./apis/', true, /\.js$/)
const api = {}

path.keys().forEach(key => {
	if (key !== './index.js') {
		api[key.replace(/(\.\/|\.js)/g, '').replace(/(.+)\//g, '')] = path(key).default
	}
})

export default api;
