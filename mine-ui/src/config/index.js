const DEFAULT_CONFIG = {
	// 模式 开发：dev | 生产：prod | 普通：normal，生产模式会隐藏掉模块管理、代码生成器、数据表设计器
	APP_MODE: 'dev',

	//标题
	APP_NAME: "MineAdmin",

	//首页地址
	DASHBOARD_URL: "/dashboard",

	//版本号
	APP_VER: "0.6.1",

	//官网地址
	APP_URL: "www.mineadmin.com",

	//接口地址
	API_URL: process.env.VUE_APP_API,

	//Token前缀，注意最后有个空格，如不需要需设置空字符串
	TOKEN_PREFIX: "Bearer ",

	//请求是否开启缓存
	REQUEST_CACHE: false,

	//布局 默认：default | 通栏：header | 经典：menu | 功能坞：dock
	//dock将关闭标签和面包屑栏
	LAYOUT: 'default',

	//菜单是否折叠
	MENU_IS_COLLAPSE: false,

	//是否开启多标签
	LAYOUT_TAGS: true,

	// 请求超时时间	 默认 5 秒
	TIMEOUT: 5000,

	//语言
	LANG: 'zh_CN',

	//主题颜色
	COLOR: '#0960bd',

	//菜单是否启用手风琴效果
	MENU_UNIQUE_OPENED: true,

	//控制台首页默认布局
	DEFAULT_GRID: {
		//默认分栏数量和宽度 例如 [24] [18,6] [8,8,8] [6,12,6]
		layout: [12, 6, 6],
		//小组件分布，com取值:views/home/components 文件名
		copmsList: [
			['welcome'],
			['about', 'ver'],
			['time', 'progress']
		]
	},

	// 文件存储URL地址
	STORAGE_URL: {
		LOCAL: 'http://127.0.0.1:9501',
		OSS: '',
		COS: '',
		QINIU: ''
	}

}

// 如果生产模式，就合并动态的APP_CONFIG
// public/config.js
// if(process.env.NODE_ENV === 'production'){
// 	Object.assign(DEFAULT_CONFIG, APP_CONFIG)
// }

module.exports = DEFAULT_CONFIG
