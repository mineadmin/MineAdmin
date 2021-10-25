import sysConfig from "@/config"
import tool from '@/utils/tool'
import { createI18n } from 'vue-i18n'
import el_zh_cn from 'element-plus/lib/locale/lang/zh-cn'
import el_en from 'element-plus/lib/locale/lang/en'

import zh_cn from './lang/zh_cn.js'
import en from './lang/en.js'

const messages = {
	'zh_CN': {
		el: el_zh_cn,
		...zh_cn
	},
	'en': {
		el: el_en,
		...en
	}
}

const i18n = createI18n({
	locale: tool.data.get("APP_LANG") || sysConfig.LANG,
	fallbackLocale: 'zh_CN',
	globalInjection: true,
	messages,
})

export default i18n;
