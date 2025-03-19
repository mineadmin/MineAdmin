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
import type { MineToolbar } from '#/global'

export default defineComponent({
  name: 'RightBar',
  setup() {
    const settingStore = useSettingStore()
    const toolbarHook = useGlobal().$toolbars
    const toolbars = ref<Component[]>([])
    // 计算处理后的工具栏数据
    const toolbarList = computed(() =>
      toolbarHook.toolbars.value.map((item: MineToolbar) => ({
        ...item,
        show: settingStore.getSettings('toolBars')?.find((setting: MineToolbar) => setting.name === item.name)?.show ?? item.show,
      })),
    )
    watch(() => toolbarList.value, () => {
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
