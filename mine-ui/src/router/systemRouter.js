//系统路由
const routes = [{
		name: "layout",
		path: "/",
		component: () => import(/* webpackChunkName: "layout" */ '@/layout'),
		redirect: '/dashboard',
		children: [
			{
				name: "home",
				path: "/home",
				component: () => import(`@/views/other/empty`),
				meta: {
					title: "首页",
					icon: "el-icon-s-home"
				},
				children: [
					{
						name: "dashboard",
						path: "/dashboard",
						meta: {
							title: "控制台",
							icon: "el-icon-monitor",
							affix: true
						},
						component: () => import(/* webpackChunkName: "home" */ '@/views/home'),
					},{
						name: "userCenter",
						path: "/usercenter",
						meta: {
							title: "个人信息",
							icon: "el-icon-user"
						},
						component: () => import(/* webpackChunkName: "usercenter" */ '@/views/userCenter'),
					},{
						name: "system",
						path: "/system",
						meta: {
							title: "系统配置",
							icon: "el-icon-setting",
						},
						component: () => import(/* webpackChunkName: "usercenter" */ '@/views/setting/index'),
					}
				]
			}
		]
	},
	{
		path: "/login",
		name: 'login',
		component: () => import(/* webpackChunkName: "login" */ '@/views/userCenter/login'),
		meta: {
			title: "登录"
		}
	}
]

export default routes;
