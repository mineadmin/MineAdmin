import login from '@/api/apis/login'
import tool from '@/utils/tool'
import sysRouter from '@/router/systemRouter'
import store from '@/store'
import i18n from '@/locales'
import colorTool from '@/utils/color'

export default {
  state: {
    routers: undefined
  },
  mutations: {
    SET_ROUTERS(state, routers) {
      state.routers = routers
    },
  },
  actions: {
    getUserInfo({ commit }) {
      return new Promise((resolve, reject) => {
        login.getInfo().then(response => {
          if (response.data.roles && response.data.routers.length > 0) {
            response.data.routers = filterAsyncRouter(response.data.routers)
            response.data.routers.unshift(sysRouter.dashboard)
            let user = {
              codes: response.data.codes,
              roles: response.data.roles,
              user: response.data.user
            }
            if (user.user && user.user.backend_setting) {
              let backend_setting = user.user.backend_setting
              // let backend_setting = JSON.parse(user.user.backend_setting)
              tool.data.set('LAYOUT_TAGS', backend_setting.layoutTags)
              tool.data.set('APP_LAYOUT', backend_setting.layout)
              tool.data.set('APP_LANG', backend_setting.lang)
              tool.data.set('APP_THEME', backend_setting.theme)
              tool.data.set('APP_COLOR', backend_setting.colorPrimary)
              // 设置tag 
              if (store.state.global.layoutTags !== backend_setting.layoutTags) {
                commit('TOGGLE_layoutTags')
              }
              // 设置语言
              i18n.locale = backend_setting.lang
              // 设置布局
              commit("SET_layout", backend_setting.layout)
              // 是否黑夜模式
              document.body.setAttribute('data-theme', backend_setting.theme)
              // 设置主题颜色
              document.documentElement.style.setProperty('--el-color-primary', backend_setting.colorPrimary);
              for (let i = 1; i <= 9; i++) {
                document.documentElement.style.setProperty(
                  `--el-color-primary-light-${i}`,
                  colorTool.lighten(backend_setting.colorPrimary, i / 10)
                );
              }
              document.documentElement.style.setProperty(
                `--el-color-primary-darken-1`,
                colorTool.darken(backend_setting.colorPrimary, 0.1)
              );
            }
            // commit('TOGGLE_layoutTags')
            commit('SET_ROUTERS', response.data.routers)
            tool.data.set('user', user)
            resolve(response.data)
          }
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
}

//转换
function filterAsyncRouter(routerMap) {
  const accessedRouters = []
  routerMap.forEach(item => {
    if (item.meta.type == 'B') {
      return
    }
    item.meta = item.meta ? item.meta : {};
    //处理外部链接特殊路由
    if (item.meta.type == 'I') {
      item.meta.url = item.path
      item.path = `/i/${item.name}`
    }

    //MAP转路由对象
    const route = {
      path: item.path,
      name: item.name,
      meta: item.meta,
      redirect: item.redirect,
      children: item.children ? filterAsyncRouter(item.children) : null,
      component: loadComponent(item.component)
    }
    accessedRouters.push(route)
  })
  return accessedRouters
}

function loadComponent(component) {
  if (component) {
    return () => import(/* webpackChunkName: "[request]" */ `@/views/${component}`)
  } else {
    return () => import(`@/layout/other/empty`)
  }
}
