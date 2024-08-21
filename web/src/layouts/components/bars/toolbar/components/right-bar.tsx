/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { Component } from 'vue'
import MineUserBar from './user-bar.tsx'

export default defineComponent({
  name: 'RightBar',
  setup() {
    const toolbarHook = useGlobal().$toolbars
    const toolbars = ref<Component[]>([])
    watch(() => toolbarHook.toolbars.value, () => {
      toolbarHook.state = false
      toolbarHook.render().then((res: any) => {
        toolbars.value = res as Component[]
      })
      toolbarHook.state = true
    }, { immediate: true, deep: true })
    return () => (
      <div class="right-bar">
        {toolbars.value}
        <MineUserBar />
      </div>
    )
  },
})
