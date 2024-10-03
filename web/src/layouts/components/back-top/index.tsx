/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { Teleport, Transition } from 'vue'

export default defineComponent({
  name: 'BackTop',
  setup() {
    function handleClick() {
      document.querySelector('.mine-main')?.scrollTo({
        top: 0,
        behavior: 'smooth',
      })
    }

    const transitionClass = {
      enterActiveClass: 'ease-out duration-300',
      enterFromClass: 'opacity-0 translate-y-4 lg-translate-y-0 lg-scale-95',
      enterToClass: 'opacity-100 translate-y-0 lg-scale-100',
      leaveActiveClass: 'ease-in duration-200',
      leaveFromClass: 'opacity-100 translate-y-0 lg-scale-100',
      leaveToClass: 'opacity-0 translate-y-4 lg-translate-y-0 lg-scale-95',
    }

    const scrollTop = ref<number | null>(null)
    function handleScroll() {
      scrollTop.value = document.querySelector('.mine-main')?.scrollTop ?? 0
    }

    onMounted(() => {
      document.querySelector('.mine-main')?.addEventListener('scroll', handleScroll)
      handleScroll()
    })

    onBeforeUnmount(() => {
      document.querySelector('.mine-main')?.removeEventListener('scroll', handleScroll)
    })

    const renderBackTop = () => (
      <div
        class="mine-back-top"
        onClick={() => handleClick()}
      >
        <ma-svg-icon name="i-tdesign:backtop" size={26} />
      </div>
    )

    return () => (
      <Teleport to="body">
        <Transition {...transitionClass}>
          {(unref(scrollTop) ?? 0) > 200 && renderBackTop()}
        </Transition>
      </Teleport>
    )
  },
})
