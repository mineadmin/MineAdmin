import config from "./config"
import api from './api'
import tool from './utils/tool'

import hasPermission from './utils/permission'
import hasRole from './utils/role'

import maTable from './components/maTable'          // 原scTable，进行了系统适配优化，与原版scTable并存
import maPhoto from './components/maPhoto'
import maImport from './components/maImport'
import maDictTag from './components/maDictTag'
import maSelectUser from './components/maSelectUser'

import scTable from './components/scTable'
import scFilterBar from './components/scFilterBar'
import scUpload from './components/scUpload'
import scUploadMultiple from './components/scUpload/multiple'
import scFileSelect from './components/scFileSelect'
import scFormTable from './components/scFormTable'
import scTableSelect from './components/scTableSelect'
import scPageHeader from './components/scPageHeader'
import scSelect from './components/scSelect'
import scForm from './components/scForm'
import scTitle from './components/scTitle'
import scWaterMark from './components/scWaterMark'
import scQrCode from './components/scQrCode'
import scStatusIndicator from './components/scMini/scStatusIndicator'
import scTrend from './components/scMini/scTrend'

import auth from './directives/auth'
import role from './directives/role'
import time from './directives/time'
import copy from './directives/copy'
import errorHandler from './utils/errorHandler'

import * as elIcons from '@element-plus/icons'
import * as maIcons from './assets/maicons'
import * as scIcons from './assets/icons'

import useClipboard from 'vue-clipboard3'

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
		app.config.globalProperties.getDicts = api.dataDict.getDicts

		// 全局挂载显示图片方法
		app.config.globalProperties.viewImage = tool.viewImage

		// 全局挂载城市代码翻译成名称方法
		app.config.globalProperties.codeToCity = tool.codeToCity

		// 全局挂载复制方法
		app.config.globalProperties.clipboard = useClipboard().toClipboard

		// 全局挂载获取Token
		app.config.globalProperties.getToken = tool.getToken

		//注册全局组件
		app.component('maTable', maTable)
		app.component('maDictTag', maDictTag)
		app.component('maImport', maImport)
		app.component('maPhoto', maPhoto)
		app.component('maSelectUser', maSelectUser)
		app.component('scTable', scTable)
		app.component('scFilterBar', scFilterBar)
		app.component('scUpload', scUpload)
		app.component('scUploadMultiple', scUploadMultiple)
		app.component('scFormTable', scFormTable)
		app.component('scFileSelect', scFileSelect)
		app.component('scTableSelect', scTableSelect)
		app.component('scPageHeader', scPageHeader)
		app.component('scSelect', scSelect);
		app.component('scForm', scForm);
		app.component('scTitle', scTitle);
		app.component('scWaterMark', scWaterMark);
		app.component('scQrCode', scQrCode);
		app.component('scStatusIndicator', scStatusIndicator);
		app.component('scTrend', scTrend);

		//注册全局指令
		app.directive('auth', auth)
		app.directive('role', role)
		app.directive('time', time)
		app.directive('copy', copy)

		//统一注册el-icon图标
		for(let icon in elIcons){
			app.component(`ElIcon${icon}`, elIcons[icon])
		}
		//统一注册ma-icon图标
		for(let icon in maIcons){
			app.component(`MaIcon${icon}`, maIcons[icon])
		}
		//统一注册sc-icon图标
		for(let icon in scIcons){
			app.component(`ScIcon${icon}`, scIcons[icon])
		}

		//全局代码错误捕捉
		app.config.errorHandler = errorHandler

		tool.capsule('MineAdmin', `v${config.APP_VER}`)
		console.log('MineAdmin 官网  https://www.mineadmin.com')
		console.log('MineAdmin 文档  https://doc.mineadmin.com')
		console.log('MineAdmin Gitee https://gitee.com/xmo/MineAdmin')
		console.log('请不要吝啬您的 star，谢谢 ~')
	}
}
