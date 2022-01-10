import {createRouter, createWebHashHistory} from 'vue-router'
import { ElNotification } from 'element-plus';
import config from "@/config"
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import tool from '@/utils/tool'
import store from '@/store/index'
import sysRouter from './systemRouter';
import {beforeEach, afterEach} from './scrollBehavior'

//系统路由
const routes = sysRouter.systemRouter

const whiteList = ['login', 'doc', 'interfaceList', 'interfaceCode', 'signature', 'test']
const defaultRoutePath = '/dashboard'

//系统特殊路由
const routes_404 = {
	path: "/:pathMatch(.*)*",
	hidden: true,
	component: () => import(/* webpackChunkName: "404" */ '@/layout/other/404'),
}

const router = createRouter({
	history: createWebHashHistory(),	// createWebHistory 暂时不可用，多层次路由地址有bug。
	routes: routes
})

//设置标题
document.title = config.APP_NAME

router.beforeEach(async (to, from, next) => {

	NProgress.start()
	
	//动态标题
	document.title = to.meta.title ? `${to.meta.title} - ${config.APP_NAME}` : `${config.APP_NAME}`

	let token = tool.data.get('token');

	if (token && token !== 'undefined') {
		
		//整页路由处理
		if(to.meta.fullpage){
			to.matched = [to.matched[to.matched.length-1]]
		}

		if (tool.data.get('lockScreen') && to.name !== 'lockScreen') {
			next({ name: 'lockScreen' })
		} else if (! tool.data.get('lockScreen') && to.name === 'lockScreen' ) {
			next({ path: defaultRoutePath })
		} else if (to.name === 'login'){
			next({ path: defaultRoutePath })
		} else if (! store.state.user.routers) {
			await store.dispatch('getUserInfo').then( res => {
				if (res.routers.length !== 0) {
					let routers = res.routers
					const apiRouter = flatAsyncRoutes(routers)
					apiRouter.forEach(item => {
						router.addRoute("layout", item)
					})
					router.addRoute(routes_404)
					if (to.matched.length == 0) {
						router.push(to.fullPath)
					}
				}
			}).catch(() => {
				next({ name: 'login', query: { redirect: to.fullPath } })
				store.commit('SET_ROUTERS', undefined)
				tool.data.clear()
			})
			beforeEach(to, from)
			next()
		} else {
			beforeEach(to, from)
			next()
		}
	} else {
		if (whiteList.includes(to.name)) {
			beforeEach(to, from)
			next()
		} else {
			next({ name: 'login', query: { redirect: to.fullPath } })
		}
	}
});

router.afterEach((to, from) => {
	afterEach(to, from)
	NProgress.done()
});

router.onError((error) => {
	NProgress.done();
	const pattern = /Loading chunk (\d)+ failed/g;
	const isChunkLoadFailed = error.message.match(pattern);
	const targetPath = router.history.pending.fullPath;
	ElNotification.error({ title: '路由错误', message: '路由未定义或路由页面不存在' });
	if (isChunkLoadFailed) {
		router.replace(targetPath);
	}
});

//路由扁平化
function flatAsyncRoutes(routes, breadcrumb=[]) {
	let res = []
	routes.forEach(route => {
		const tmp = {...route}
        if (tmp.children) {
            let childrenBreadcrumb = [...breadcrumb]
            childrenBreadcrumb.push(route)
            let tmpRoute = { ...route }
            tmpRoute.meta.breadcrumb = childrenBreadcrumb
            delete tmpRoute.children
            res.push(tmpRoute)
            let childrenRoutes = flatAsyncRoutes(tmp.children, childrenBreadcrumb)
            childrenRoutes.map(item => {
                res.push(item)
            })
        } else {
            let tmpBreadcrumb = [...breadcrumb]
            tmpBreadcrumb.push(tmp)
            tmp.meta.breadcrumb = tmpBreadcrumb
            res.push(tmp)
        }
    })
    return res
}

export default router
