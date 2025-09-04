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
import useHttp from '@/hooks/auto-imports/useHttp.ts'
import * as PermissionApi from '~/base/api/permission.ts'
import type { MenuVo, RoleVo } from '~/base/api/permission.ts'
import { recursionGetKey } from '@/utils/recursionGetKey.ts'

export interface LoginParams {
  username: string
  password: string
}

export interface LoginResult {
  access_token: string
  expire_at: number
  refresh_token: string
}

export interface UserInfo {
  username: string
  nickname: string
  avatar: string
  phone: string
  email: string
  signed: string
  dashboard: string
  backend_setting: any[]
}

function getInfo(): Promise<ResponseStruct<UserInfo>> {
  return useHttp().get('/admin/passport/getInfo')
}

function logoutApi(): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/passport/logout')
}

/**
 * Passport login
 * @param data
 */
function loginApi(data: LoginParams): Promise<ResponseStruct<LoginResult>> {
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
    const menu = ref<MenuVo[]>([])
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

    function getMenu() {
      return menu.value
    }

    function setMenu(list: MenuVo[]) {
      menu.value = list
    }

    function getDropdownMenuState(key: string) {
      return dropdownMenuState.value[key] !== undefined ? dropdownMenuState.value[key] : undefined
    }

    async function refreshRole() {
      const res = await PermissionApi.getRoles()
      setRoles(res.data)
    }

    async function refreshMenu() {
      const res = await PermissionApi.getMenus()
      setMenu(res.data)
    }

    async function login(data: { username: string, password: string, code: string, [key: string]: any }) {
      await usePluginStore().callHooks('loginBefore', data)
      return new Promise((resolve, reject) => {
        loginApi(data).then(async (res) => {
          token.value = res.data.access_token
          cache.set('token', res.data.access_token)
          cache.set('expire', useDayjs().unix() + res.data.expire_at, { exp: res.data.expire_at })
          cache.set('refresh_token', res.data.refresh_token)
          await usePluginStore().callHooks('login', { username: data.username, ...res.data })
          resolve(res.data)
        }).catch((error) => {
          reject(error)
        })
      })
    }
    async function requestUserInfo(): Promise<void> {
      try {
        const routeStore = useRouteStore()
        const { data } = await getInfo()
        setUserInfo(data)
        if ((setting.getSettings('app')?.loadUserSetting ?? true) && data.backend_setting) {
          const raw = data?.backend_setting
          const normalized = raw && !Array.isArray(raw) ? raw : null
          await setUserSetting(normalized)
        }
        await refreshMenu()
        await refreshRole()
        await routeStore.initRoutes(router, getMenu())
        const codes: string[] = recursionGetKey(getMenu(), 'name')
        getRoles().includes('SuperAdmin') && codes.unshift('*')
        setPermissions(codes)
        await usePluginStore().callHooks('getUserInfo', data)
      }
      // eslint-disable-next-line unused-imports/no-unused-vars
      catch (e) {
        await logout()
      }
    }

    async function logout(redirect = router.currentRoute.value.fullPath) {
      await usePluginStore().callHooks('logout')
      useTabStore().clearTab()
      clearInfo()
      await router.push({
        name: 'login',
        query: {
          ...(router.currentRoute.value.name !== 'login' && { redirect }),
        },
      })
    }

    function setLanguage(langName: string) {
      if (!langName || typeof langName !== 'string' || !langName.trim()) return false
      language.value = langName.trim()
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

    function setRoles(roleArray: RoleVo[]): boolean {
      roles.value = roleArray.map(item => item.code) as string[]
      return true
    }

    async function setUserSetting(settings: any) {
      settings && setting.setSettings(settings)
      setting.initColorMode()

      await nextTick()
      useThemeColor().initThemeColor()
      const locale = settings?.app?.useLocale ?? (language.value?.trim() || 'zh_CN')
      setLanguage(locale)
    }

    function saveSettingToSever() {
      const backend_setting = setting.getSettings()
      useHttp().post('/admin/permission/update', { backend_setting }).then(() => {
        cache.set('sys_settings', backend_setting)
      }).catch((error) => {
        console.log(error)
      })
    }

    async function clearCache() {
      // await useHttp().post('/mock/system/clearCache')
    }

    function clearInfo() {
      cache.remove('token')
      cache.remove('refresh_token')
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
      getDropdownMenuState,
      setDropdownMenuState,
      clearCache,
      setLanguage,
      getLanguage,
      requestUserInfo,
      getUserInfo,
      setPermissions,
      getPermissions,
      getRoles,
      getLocales,
      setLocales,
      saveSettingToSever,
      getMenu,
    }
  },
)

export default useUserStore
