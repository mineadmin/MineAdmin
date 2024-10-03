/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import { useFullscreen } from '@vueuse/core'

export default defineComponent({
  name: 'fullscreen',
  setup() {
    const { isFullscreen, toggle } = useFullscreen(document.body, { autoExit: true })
    return () => (
      <div class="hidden items-center lg:flex">
        <ma-svg-icon
          class="tool-icon"
          name={isFullscreen.value ? 'mingcute:fullscreen-exit-line' : 'mingcute:fullscreen-line'}
          size={20}
          onClick={() => toggle()}
        />
      </div>
    )
  },
})
