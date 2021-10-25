import config from "./config"
import api from './api'
import tool from './utils/tool'

import hasPermission from './utils/permission'
import hasRole from './utils/role'

import maTable from './components/maTable'          // 原scTable，进行了系统适配优化，与原版scTable并存
import maPhoto from './components/maPhoto'
import maImport from './components/maImport'
import maResourceSelect from './components/maResourceSelect'

import scTable from './components/scTable'
import scFilterBar from './components/scFilterBar'
import scUpload from './components/scUpload'
import scUploadMultiple from './components/scUpload/multiple'
import scFormTable from './components/scFormTable'
import scTableSelect from './components/scTableSelect'
import scPageHeader from './components/scPageHeader'
import scSelect from './components/scSelect'
import scDialog from './components/scDialog'
import scForm from './components/scForm'
import scTitle from './components/scTitle'
import auth from './directives/auth'
import role from './directives/role'
import time from './directives/time'
import copy from './directives/copy'
import errorHandler from './utils/errorHandler'

export default {
	install(app) {
		//挂载全局对象
		app.config.globalProperties.$CONFIG = config
		app.config.globalProperties.$TOOL = tool
		app.config.globalProperties.$API = api
		app.config.globalProperties.$AUTH = hasPermission
		app.config.globalProperties.$ROLE = hasRole
		
		// 全局挂载获取字典数据方法
		app.config.globalProperties.getDict = api.dataDict.getDict

		//注册全局组件
		app.component('maTable', maTable)
		app.component('maImport', maImport)
		app.component('maPhoto', maPhoto)
		app.component('maResourceSelect', maResourceSelect)
		app.component('scTable', scTable)
		app.component('scSelect', scSelect)
		app.component('scDialog', scDialog)
		app.component('scFilterBar', scFilterBar)
		app.component('scUpload', scUpload)
		app.component('scUploadMultiple', scUploadMultiple)
		app.component('scFormTable', scFormTable)
		app.component('scTableSelect', scTableSelect)
		app.component('scPageHeader', scPageHeader)
		app.component('scSelect', scSelect);
		app.component('scDialog', scDialog);
		app.component('scForm', scForm);
		app.component('scTitle', scTitle);

		//注册全局指令
		app.directive('auth', auth)
		app.directive('role', role)
		app.directive('time', time)
		app.directive('copy', copy)

		//全局代码错误捕捉
		app.config.errorHandler = errorHandler

		tool.capsule('MineAdmin', `v${config.APP_VER}`)
		console.log('MineAdmin 官网  https://www.mineadmin.com')
		console.log('MineAdmin 文档  https://doc.mineadmin.com')
		console.log('MineAdmin Gitee https://gitee.com/xmo/MineAdmin')
		console.log('请不要吝啬您的 star，谢谢 ~')
	}
}
