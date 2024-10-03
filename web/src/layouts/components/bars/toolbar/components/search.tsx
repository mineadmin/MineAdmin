/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export default defineComponent({
  name: 'search',
  setup() {
    const openSearchPanel = async () => {
      useSettingStore().setSearchPanelEnable(true)
      await nextTick()
      const dom = document.querySelector('.mine-search-input') as HTMLElement
      dom?.focus()
    }
    return () => (
      <ma-svg-icon
        class="tool-icon animate-ease-in"
        name="heroicons:magnifying-glass-20-solid"
        size={20}
        onClick={() => openSearchPanel()}
      />
    )
  },
})
