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
import useHttp from "@/hooks/auto-imports/useHttp.ts";


export type LoginParams = {
  username: string;
  password: string;
}

export type LoginResult = {
  token: string;
  expire_at: number;
}

export type UserInfo = {
  username:string,
  nickname:string,
  avatar:string,
  email:string,
  signed:string,
  dashboard:string,
  backend_setting:any[],
}

function getInfo():Promise<ResponseStruct<UserInfo>> {
  return useHttp().get('/admin/passport/getInfo')
}

function logout():Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/passport/logout')
}

/**
 * Passport login
 * @param data
 */
function loginApi(data:LoginParams):Promise<ResponseStruct<LoginResult>> {
  return useHttp().post('/admin/passport/login', data)
}

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
        loginApi(data).then(async res=>{
          token.value = res.data.token
          cache.set('token', res.data.token)
          cache.set('expire', useDayjs().unix() + res.data.expire, { exp: res.data.expire })
          await usePluginStore().callHooks('login', res.data)
          resolve(res.data)
        })
      })
    }

    async function requestUserInfo() {
      getInfo().then(res=>{
        setUserInfo(res.data)
        if ((setting.getSettings('app')?.loadUserSetting ?? true) && data.user.backend_setting) {
          setUserSetting(data.user?.backend_setting)
        }
         usePluginStore().callHooks('getUserInfo', data.user)
      }).catch(err=>{
        logout();
      })
      return ;
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
      getLocales,
      setLocales,
      saveSettingToSever,
    }
  },
)

export default useUserStore
