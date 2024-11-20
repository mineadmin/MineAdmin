/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import image403 from '@/assets/images/403.svg'
import image404 from '@/assets/images/404.svg'

export default defineComponent({
  name: 'MineSystemError',
  setup() {
    const route = useRoute()
    const router = useRouter()
    return () => (
      <div class="relative mx-auto w-full flex flex-col-center">
        <div class="absolute h-600px w-full"></div>
        {route.fullPath !== '/403' && <img src={image404} width="600" alt="404" />}
        {route.fullPath === '/403' && <img src={image403} width="600" alt="404" />}
        <div class="flex">
          <m-button
            onClick={() => router.replace('/')}
          >
            <ma-svg-icon name="i-material-symbols:home-outline-rounded" size={20} />
            {useTrans('mineAdmin.goHome')}
          </m-button>
        </div>
      </div>
    )
  },
})
