/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import useCache from '@/hooks/useCache.ts'
import type { ResponseStruct } from '#/global'
import useThemeColor from '@/hooks/useThemeColor.ts'

const useUserStore = defineStore(
  'useUserStore',
  () => {
    const cache = useCache()
    const router = useRouter()
    const setting = useSettingStore()

    const token = ref<string | null>(cache.get('token', null))
    const locales = ref<any[]>([])
    const language = ref(cache.get('language', 'zh_CN'))
    const isLogin = computed(() => !!token.value)
    const userInfo = ref<any | null>(null)
    const permissions = ref<string[]>([])
    const roles = ref<string[]>([])
    const posts = ref<string[]>([])
    const depts = ref<string[]>([])
    const dropdownMenuState = ref<{
      shortcuts: boolean
      systemInfo: boolean
    }>({
      shortcuts: false,
      systemInfo: false,
    })

    function getDropdownMenu() {
      return dropdownMenuState.value
    }

    function setDropdownMenuState(key: string, state: boolean) {
      if (dropdownMenuState.value[key] !== undefined) {
        dropdownMenuState.value[key] = state
      }
    }

    function getDropdownMenuState(key: string) {
      return dropdownMenuState.value[key] !== undefined ? dropdownMenuState.value[key] : undefined
    }

    function login(data: { username: string, password: string, code: string }) {
      return new Promise((resolve, reject) => {
        useHttp().post('/mock/system/login', data).then(async (response: ResponseStruct) => {
          const data: any = response.data
          cache.set('token', data.token)
          cache.set('expire', useDayjs().unix() + data.expire, { exp: data.expire })
          token.value = response.data.token
          await usePluginStore().callHooks('login', data)
          resolve(data)
        }).catch((error) => {
          reject(error)
        })
      })
    }

    async function requestUserInfo() {
      const { data } = await useHttp().get('/mock/system/getInfo')
      data === null
        ? await logout()
        : (
            setUserInfo(data.user)
            && setPermissions(data.permissions as string[])
            && setRoles(data.roles)
          )
      if ((setting.getSettings('app')?.loadUserSetting ?? true) && data.user?.backend_setting) {
        setUserSetting(data.user?.backend_setting)
      }

      await usePluginStore().callHooks('getUserInfo', data.user)
      return data.routes
    }

    async function logout(redirect = router.currentRoute.value.fullPath) {
      await usePluginStore().callHooks('logout')
      clearInfo()
      await router.push({
        name: 'login',
        query: {
          ...(router.currentRoute.value.name !== 'login' && { redirect }),
        },
      })
    }

    function setLanguage(langName: string) {
      language.value = langName
      cache.set('language', language.value)
      return true
    }

    function getLanguage() {
      return language.value
    }

    function getLocales(): any[] {
      return locales.value
    }

    function setLocales(localeArray: any[]): boolean {
      locales.value = localeArray
      return true
    }

    function getUserInfo(): any {
      return userInfo.value
    }

    function setUserInfo(data: any): boolean {
      userInfo.value = data
      return true
    }

    function getPermissions(): string[] {
      return permissions.value
    }

    function setPermissions(permissionArray: string[]): boolean {
      permissions.value = permissionArray
      return true
    }

    function getRoles(): string[] {
      return roles.value
    }

    function setRoles(roleArray: string[]): boolean {
      roles.value = roleArray
      return true
    }

    function getPosts(): string[] {
      return posts.value
    }

    function setPosts(postArray: string[]): boolean {
      posts.value = postArray
      return true
    }

    function getDepts(): string[] {
      return depts.value
    }

    function setDepts(deptArray: string[]): boolean {
      depts.value = deptArray
      return true
    }

    function setUserSetting(settings: any) {
      setting.setSettings(settings)
      setting.initColorMode()
      useThemeColor().initThemeColor()
    }

    function saveSettingToSever() {
      const settings = setting.getSettings()
      useHttp().post('/mock/system/saveSetting', settings).then((response: ResponseStruct) => {
        cache.set('sys_settings', settings)
      }).catch((error) => {
        console.log(error)
      })
    }

    async function clearCache() {
      await useHttp().post('/mock/system/clearCache')
    }

    function clearInfo() {
      cache.remove('token')
      cache.remove('language')
      cache.remove('expire')
      token.value = null
      language.value = ''
      userInfo.value = null
      permissions.value = []
      roles.value = []
    }

    return {
      token,
      isLogin,
      login,
      logout,
      getDropdownMenu,
      setDropdownMenuState,
      getDropdownMenuState,
      clearCache,
      clearInfo,
      setLanguage,
      getLanguage,
      requestUserInfo,
      getUserInfo,
      setUserInfo,
      getPermissions,
      setPermissions,
      getRoles,
      setRoles,
      getPosts,
      setPosts,
      getDepts,
      setDepts,
      getLocales,
      setLocales,
      saveSettingToSever,
    }
  },
)

export default useUserStore
