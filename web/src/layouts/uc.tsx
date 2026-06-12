/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import Logo from '@/layouts/components/logo'
import ucChildren from '@/router/static-routes/ucChildren'
import './style/uc.scss'

export default defineComponent({
  name: 'MineUc',
  setup() {
    const userStore = useUserStore()
    const userInfo = computed(() => userStore.getUserInfo())
    const route = useRoute()
    const welcomeRoute = ref<string>(useSettingStore().getSettings('app')?.welcomePage?.path ?? '/welcome')
    const menuRender = () => (
      <>
        {ucChildren.map((item: any) => (
          <li>
            <router-link to={item.path} class={{ active: item.path === route.path }}>
              {item.meta?.icon && <ma-svg-icon name={item.meta?.icon} size={20} />}
              <span>{item.meta?.i18n ? useTrans(item.meta.i18n) : item.meta?.title}</span>
            </router-link>
          </li>
        ))}
      </>
    )

    const userinfoRender = () => {
      const avatar = userInfo.value?.avatar
      const username = userInfo.value?.username ?? ''
      const nickname = userInfo.value?.nickname ?? ''

      return (
        <div class="mine-uc-userinfo">
          <div class="flex items-center gap-x-3">
            {avatar && <img src={avatar} alt={username} class="mine-uc-img-avatar" />}
            {!avatar && <div class="mine-uc-text-avatar">{username[0]?.toUpperCase() ?? ''}</div>}
            <a class="mine-uc-username">
              <span class="mine-uc-u">{username}</span>
              <span ckass="mine-uc-n">{nickname}</span>
            </a>
          </div>
          <m-tooltip text={useTrans('mineAdmin.uc.backControl')}>
            <router-link className="mine-back-control" to={welcomeRoute.value}>
              <ma-svg-icon name="ri:arrow-go-back-line" size={14} />
            </router-link>
          </m-tooltip>
        </div>
      )
    }

    const handleResize = () => {
      const node = document.querySelector('.mine-uc-content') as HTMLElement
      if (document.body.offsetWidth < 1024) {
        node.style.height = `${document.body.offsetHeight - 58}px`
      }
      else {
        node.style.height = '100%'
      }
    }

    onMounted(() => window.addEventListener('resize', handleResize))
    onUnmounted(() => window.removeEventListener('resize', handleResize))

    return () => (
      <div class="mine-uc-container">
        <div class="mine-uc-aside hidden lg:block">
          <Logo title={useTrans('mineAdmin.uc.title')} />
          <ul class="mine-uc-menu">{menuRender()}</ul>
          {userinfoRender()}
        </div>

        <div class="mine-uc-content">
          <router-view />
        </div>

        <ul class="mine-uc-bottom-menu">
          {menuRender()}
          <li>
            <router-link to={welcomeRoute.value}>
              <ma-svg-icon name="ri:arrow-go-back-line" size={20} />
              <span>{useTrans('mineAdmin.uc.shortBackControl')}</span>
            </router-link>
          </li>
        </ul>
      </div>
    )
  },
})
