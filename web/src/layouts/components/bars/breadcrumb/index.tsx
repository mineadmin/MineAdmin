/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { ComputedRef, VNode } from 'vue'
import { TransitionGroup } from 'vue'
import type { MineRoute } from '#/global'
import checkRouteIsRedirect from '@/utils/checkRouteIsRedirect.ts'

export default defineComponent({
  name: 'Breadcrumb',
  setup() {
    const settingStore = useSettingStore()
    const router = useRouter()
    const currentRoute = useRoute()
    const { watchRoute } = useMenuStore()
    const breadcrumbs: ComputedRef<(MineRoute.routeRecord | undefined)[]> = computed(() => {
      if (!watchRoute?.meta?.breadcrumb) {
        return []
      }
      return watchRoute.meta?.breadcrumb?.map((route: MineRoute.routeRecord) => {
        if (route.meta?.breadcrumbEnable !== false) {
          return route
        }
      })
    })

    const renderBreadcrumbs = (route: MineRoute.routeRecord, idx: number): VNode => {
      return (
        <div class="flex items-center">
          <a
            class={{
              'cursor-default': true,
              'cursor-pointer': (route?.children?.length ?? 0) > 0,
            }}
            onClick={async () => {
              if (checkRouteIsRedirect(route, 'redirect')) {
                await router.push({ path: route?.redirect as string })
              }
              if (checkRouteIsRedirect(route, 'component') && idx + 1 !== breadcrumbs.value.length) {
                await router.push({ path: route.path })
              }
            }}
          >
            {route?.meta?.icon && <ma-svg-icon name={route?.meta?.icon} size={18} />}
            {route?.meta?.i18n ? useTrans(route?.meta?.i18n) : route?.meta?.title}
          </a>
          {
            (route?.children?.length ?? 0) > 0
            && (<ma-svg-icon name="material-symbols:arrow-right-rounded" class="icon" size={22} />)
          }
        </div>
      )
    }
    return () => (
      <div class="breadcrumb">
        <router-link to={settingStore.getSettings('welcomePage').path}>
          <ma-svg-icon name={settingStore.getSettings('welcomePage').icon} />
          {useTrans('menu.welcome')}
          { (currentRoute.name !== settingStore.getSettings('welcomePage').name && breadcrumbs.value.length > 0)
          && <ma-svg-icon name="material-symbols:arrow-right-rounded" className="icon" size={22} />}
        </router-link>
        {breadcrumbs.value.length > 0
        && (
          <TransitionGroup name="breadcrumb-animate">
            {breadcrumbs.value.map((route: any, _: number) =>
              (
                <div
                  class={{
                    'breadcrumb-list': route?.children?.length > 0,
                    'flex items-center': true,
                  }}
                  key={`${_}_${route?.path}_${route?.meta?.title}`}
                >
                  {
                    (_ + 1) === breadcrumbs.value.length
                      ? renderBreadcrumbs(route, _)
                      : (
                        <m-dropdown
                          class="min-w-[5rem] p-1"
                          v-slots={{
                            default: () => renderBreadcrumbs(route, _),
                            popper: () => {
                              if (Array.isArray(route?.children) && route.children.length > 0) {
                                return route.children
                                  .filter((item: MineRoute.routeRecord) => !item?.meta?.hidden)
                                  .map((item: MineRoute.routeRecord) => (
                                    <m-dropdown-item
                                      type="default"
                                      key={item.path}
                                      handle={async () => {
                                        if (checkRouteIsRedirect(item, 'redirect')) {
                                          await router.push({path: item?.redirect as string})
                                        }
                                        if (checkRouteIsRedirect(item, 'component') && item.name !== route.name) {
                                          await router.push({path: item.path})
                                        }
                                      }}
                                      v-slots={{
                                        'default': () =>
                                          <span>{item?.meta?.i18n ? useTrans(item?.meta?.i18n) : item?.meta?.title}</span>,
                                        'prefix-icon': () => item?.meta?.icon
                                          && <ma-svg-icon name={item?.meta?.icon} size={18}/>,
                                      }}
                                    />
                                  ))
                              }
                            },
                          }}
                        />
                      )
                  }
                </div>
              ),
            )}
          </TransitionGroup>
          )}
      </div>
    )
  },
})
