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
    onMounted(() => {
      const workerArea = document.querySelector('.mine-worker-area') as HTMLDivElement
      workerArea.classList.add('overflow-hidden')
      workerArea.style.height = `${getOnlyWorkAreaHeight() + 48}px`
    })
    return () => (
      <div class="mine-layout h-full w-full">
        {(route.meta?.type === 'I' && route.meta?.link) && <iframe class="h-full w-full" frameborder="0" src={route.meta?.link}></iframe>}
      </div>
    )
  },
})
