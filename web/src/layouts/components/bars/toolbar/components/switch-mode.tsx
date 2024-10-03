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
  name: 'switchMode',
  setup() {
    const settingStore = useSettingStore()
    const icon = computed(() => {
      return (settingStore.colorMode === 'autoMode')
        ? 'lets-icons:color-mode-light'
        : settingStore.colorMode === 'dark'
          ? 'material-symbols:dark-mode-outline'
          : 'material-symbols:sunny-outline-rounded'
    })
    return () => (
      <div class="hidden items-center lg:flex">
        <ma-svg-icon
          class="tool-icon"
          name={icon.value}
          size={20}
          onClick={async () => await settingStore.toggleColorMode()}
        />
      </div>
    )
  },
})
