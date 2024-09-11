export default {
  user: {
    id: 1,
    username: 'superAdmin',
    user_type: '100',
    nickname: '超级管理员',
    phone: '13888888888',
    email: 'admin123@adminmine.com',
    avatar: 'https://demo.mineadmin.com/upload/uploadfile/20230330/499990370432749568.jpg',
    signed: 'Today is very good！',
    dashboard: 'statistics',
    status: 2,
    login_ip: '221.0.40.165',
    login_time: '2024-07-06 08:52:57',
    backend_setting: {
      app: {
        colorMode: 'auto',
        useLocale: 'zh_CN',
        whiteRoute: [
          'login',
        ],
        layout: 'classic',
        pageAnimate: 'ma-slide-down',
        enableWatermark: false,
        primaryColor: '#15803D',
        showBreadcrumb: true,
        loadUserSetting: false,
        watermarkText: 'MineAdmin',
      },
      welcomePage: {
        name: 'welcome',
        path: '/welcome',
        title: '欢迎页',
        icon: 'icon-park-outline:jewelry',
      },
      mainAside: {
        showIcon: true,
        showTitle: true,
        enableOpenFirstRoute: false,
      },
      subAside: {
        showIcon: true,
        showTitle: true,
        fixedAsideState: false,
        showCollapseButton: true,
      },
      tabbar: {
        enable: true,
        mode: 'rectangle',
      },
      copyright: {
        enable: true,
        dates: '2024',
        company: 'MineAdmin Team',
        website: 'https://www.mineadmin.com',
        putOnRecord: '豫ICP备00000000号-1',
      },
    },
    created_by: 0,
    updated_by: 1,
    created_at: '2022-08-01 02:35:14',
    updated_at: '2024-07-06 08:52:57',
    remark: null,
  },
  roles: [
    'superAdmin',
  ],
  routes: [
    // {
    //   id: 1000,
    //   parent_id: 0,
    //   name: 'permission',
    //   component: '',
    //   path: '/permission',
    //   redirect: null,
    //   meta: {
    //     type: 'M',
    //     icon: 'icon-park-outline:permissions',
    //     breadcrumbEnable: true,
    //     title: '权限',
    //
    //   },
    //   children: [
    //     {
    //       id: 1100,
    //       parent_id: 1000,
    //       name: 'system:user',
    //       component: 'system/views/user/index.vue',
    //       path: '/user',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         breadcrumbEnable: true,
    //         icon: 'material-symbols:manage-accounts-outline',
    //         title: '用户管理',
    //         cache: true,
    //       },
    //     },
    //     {
    //       id: 1400,
    //       parent_id: 1000,
    //       name: 'system:role',
    //       component: 'system/views/role/index.vue',
    //       path: '/role',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'material-symbols:deployed-code-account-outline',
    //         title: '角色管理',
    //
    //       },
    //     },
    //     {
    //       id: 1300,
    //       parent_id: 1000,
    //       name: 'system:dept',
    //       component: 'system/views/dept/index.vue',
    //       path: '/dept',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'mingcute:department-line',
    //         title: '部门管理',
    //
    //       },
    //     },
    //     {
    //       id: 1200,
    //       parent_id: 1000,
    //       name: 'system:menu',
    //       component: null,
    //       path: '/menu',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ic:baseline-menu',
    //         title: '菜单管理',
    //       },
    //     },
    //     {
    //       id: 1500,
    //       parent_id: 1000,
    //       name: 'system:post',
    //       component: 'system/views/post/index.vue',
    //       path: '/post',
    //       meta: {
    //         type: 'M',
    //         breadcrumbEnable: true,
    //         icon: 'streamline:play-station',
    //         title: '岗位管理',
    //         cache: true,
    //       },
    //     },
    //   ],
    // },
    // {
    //   id: 2000,
    //   parent_id: 0,
    //   name: 'dataCenter',
    //   component: '.vue',
    //   path: '/dataCenter',
    //   redirect: null,
    //   meta: {
    //     type: 'M',
    //     icon: 'material-symbols:database',
    //     title: '数据',
    //
    //   },
    //   children: [
    //     {
    //       id: 2100,
    //       parent_id: 2000,
    //       name: 'system:dict',
    //       component: 'system/views/dict/index.vue',
    //       path: '/dict',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ma-icon-dict',
    //         title: '数据字典',
    //
    //       },
    //     },
    //     {
    //       id: 2200,
    //       parent_id: 2000,
    //       name: 'system:attachment',
    //       component: 'system/views/attachment/index.vue',
    //       path: '/attachment',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ma-icon-attach',
    //         title: '附件管理',
    //
    //       },
    //     },
    //     {
    //       id: 2300,
    //       parent_id: 2000,
    //       name: 'system:dataMaintain',
    //       component: 'system/views/dataMaintain/index.vue',
    //       path: '/dataMaintain',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ma-icon-db',
    //         title: '数据表维护',
    //
    //       },
    //     },
    //     {
    //       id: 2700,
    //       parent_id: 2000,
    //       name: 'system:notice',
    //       component: 'system/views/notice/index.vue',
    //       path: '/notice',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-bulb',
    //         title: '系统公告',
    //
    //       },
    //     },
    //   ],
    // },
    // {
    //   id: 3000,
    //   parent_id: 0,
    //   name: 'monitor',
    //   component: '',
    //   path: '/monitor',
    //   redirect: null,
    //   meta: {
    //     type: 'M',
    //     icon: 'material-symbols:screen-search-desktop-outline',
    //     title: '监控',
    //
    //   },
    //   children: [
    //     {
    //       id: 3200,
    //       parent_id: 3000,
    //       name: 'system:monitor:server',
    //       component: 'system/views/monitor/server.vue',
    //       path: '/server',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-thunderbolt',
    //         title: '服务监控',
    //
    //       },
    //     },
    //     {
    //       id: 3600,
    //       parent_id: 3000,
    //       name: 'system:onlineUser',
    //       component: 'system/views/monitor/onlineUser.vue',
    //       path: '/onlineUser',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ma-icon-online',
    //         title: '在线用户',
    //
    //       },
    //     },
    //     {
    //       id: 3700,
    //       parent_id: 3000,
    //       name: 'system:cache',
    //       component: 'system/views/monitor/cache.vue',
    //       path: '/cache',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-dice',
    //         title: '缓存监控',
    //
    //       },
    //     },
    //     {
    //       id: 3300,
    //       parent_id: 3000,
    //       name: 'logs',
    //       component: '',
    //       path: '/logs',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-book',
    //         title: '日志监控',
    //
    //       },
    //       children: [
    //         {
    //           id: 3850,
    //           parent_id: 3300,
    //           name: 'system:queueLog',
    //           component: 'system/views/logs/queueLog.vue',
    //           path: '/queueLog',
    //           redirect: null,
    //           meta: {
    //             type: 'M',
    //             icon: 'icon-layers',
    //             title: '队列日志',
    //
    //           },
    //         },
    //         {
    //           id: 3400,
    //           parent_id: 3300,
    //           name: 'system:loginLog',
    //           component: 'system/views/logs/loginLog.vue',
    //           path: '/loginLog',
    //           redirect: null,
    //           meta: {
    //             type: 'M',
    //             icon: 'icon-idcard',
    //             title: '登录日志',
    //
    //           },
    //         },
    //         {
    //           id: 3500,
    //           parent_id: 3300,
    //           name: 'system:operLog',
    //           component: 'system/views/logs/operLog.vue',
    //           path: '/operLog',
    //           redirect: null,
    //           meta: {
    //             type: 'M',
    //             icon: 'icon-robot',
    //             title: '操作日志',
    //
    //           },
    //         },
    //         {
    //           id: 3800,
    //           parent_id: 3300,
    //           name: 'system:apiLog',
    //           component: 'system/views/logs/apiLog.vue',
    //           path: '/apiLog',
    //           redirect: null,
    //           meta: {
    //             type: 'M',
    //             icon: 'icon-calendar',
    //             title: '接口日志',
    //
    //           },
    //         },
    //       ],
    //     },
    //   ],
    // },
    // {
    //   id: 4000,
    //   parent_id: 0,
    //   name: 'devTools',
    //   component: '',
    //   path: '/devTools',
    //   redirect: null,
    //   meta: {
    //     type: 'M',
    //     icon: 'material-symbols:tools-wrench-outline',
    //     title: '开发工具',
    //
    //   },
    //   children: [
    //     {
    //       id: 4100,
    //       parent_id: 4000,
    //       name: 'setting:module',
    //       component: 'system/views/module/index.vue',
    //       path: '/module',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-folder',
    //         title: '模块管理',
    //
    //       },
    //     },
    //     {
    //       id: 4200,
    //       parent_id: 4000,
    //       name: 'setting:code',
    //       component: 'system/views/code/index.vue',
    //       path: '/code',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'ma-icon-code',
    //         title: '代码生成器',
    //
    //       },
    //     },
    //     {
    //       id: 4621,
    //       parent_id: 4000,
    //       name: 'setting:datasource',
    //       component: 'system/views/datasource/index.vue',
    //       path: '/setting/datasource',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-storage',
    //         title: '数据源管理',
    //
    //       },
    //     },
    //     {
    //       id: 4400,
    //       parent_id: 4000,
    //       name: 'setting:crontab',
    //       component: 'system/views/crontab/index.vue',
    //       path: '/crontab',
    //       redirect: '',
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-schedule',
    //         title: '定时任务',
    //       },
    //     },
    //     {
    //       id: 4600,
    //       parent_id: 4000,
    //       name: 'systemInterface',
    //       component: 'system/views/systemInterface/index.vue',
    //       path: '/systemInterface',
    //       redirect: null,
    //       meta: {
    //         type: 'M',
    //         icon: 'icon-compass',
    //         title: '系统接口',
    //       },
    //     },
    //   ],
    // },
    // {
    //   id: 4500,
    //   parent_id: 0,
    //   name: 'setting:config',
    //   component: 'system/views/config/index.vue',
    //   path: '/system',
    //   meta: {
    //     type: 'M',
    //     icon: 'material-symbols:settings-suggest',
    //     title: '系统设置',
    //   },
    // },
  ],
  permissions: [
    '*',
  ],
}
