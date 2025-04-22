/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { merge } from 'lodash-es'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import zhTw from 'element-plus/dist/locale/zh-tw.mjs'
import en from 'element-plus/dist/locale/en.mjs'

export default defineComponent({
  name: 'MineProvider',
  setup(_, { attrs, slots }) {
    interface Locale {
      [key: string]: string
    }
    const locales: Locale = {
      zh_CN: zhCn,
      zh_TW: zhTw,
      en,
    }
    const userStore = useUserStore()
    useMenuStore().init()
    const attrsMerged: any = ref(merge({ locale: locales[userStore.getLanguage()], button: { autoInsertSpace: true } }, attrs))

    watch(() => userStore.getLanguage(), (lang: string) => {
      attrsMerged.value.locale = locales[lang]
    }, { immediate: true })

    onMounted(async () => await usePluginStore().callHooks('setup'))
    return () => (
      <el-config-provider {...attrsMerged.value}>
        {slots.default?.()}
      </el-config-provider>
    )
  },
})
