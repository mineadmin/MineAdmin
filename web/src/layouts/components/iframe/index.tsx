/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'

export default defineComponent({
  name: 'MineIframe',
  setup() {
    const route = useRoute()
    const routers = useRouter().getRoutes()
    const iframeStore = useIframeKeepAliveStore()
    const list = computed(() => iframeStore.iframeList ?? [])
    onMounted(() => {
      const iframeArea = document.querySelector('.mine-iframe-area') as HTMLDivElement
      iframeArea.classList.add('overflow-hidden')
      iframeArea.style.height = `${getOnlyWorkAreaHeight() + 48}px`
    })
    return () => (
      <div class="mine-layout h-full w-full">
        {iframeStore.iframeList?.map((name: string) => {
          return (
            <iframe
              class="h-full w-full"
              frameborder="0"
              src={routers.find(item => item.name === name)!.meta!.link}
              v-show={route.name === name}
            />
          )
        })}
      </div>
    )
  },
})
