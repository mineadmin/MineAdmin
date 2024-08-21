export default defineComponent({
  name: 'shortcutsDesc',
  setup() {
    const { getDropdownMenu } = useUserStore()
    const dropdownMenuState = getDropdownMenu()

    return () => (
      <>
        <m-modal
          class="max-w-[450px] overflow-hidden"
          v-model={dropdownMenuState.shortcuts}
          title={useTrans('mineAdmin.userBar.shortcuts')}
        >
          快捷键介绍
        </m-modal>
      </>
    )
  },
})
