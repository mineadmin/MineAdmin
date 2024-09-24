export default defineComponent({
  name: 'shortcutsDesc',
  setup() {
    const { getDropdownMenu } = useUserStore()
    const dropdownMenuState = getDropdownMenu()

    return () => (
      <>
        <m-modal
          contentClass="w-[380px] lg:w-450px justify-start"
          v-model={dropdownMenuState.shortcuts}
          title={useTrans('mineAdmin.userBar.shortcuts')}
        >
          <div class="mine-shortcuts-block">
            <div class="title">{useTrans('mineAdmin.shortcuts.searchBar')}</div>
            <div class="short-list">
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>s</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.searchOpen')}</div>
              </div>
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Esc</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.close')}</div>
              </div>
            </div>
          </div>
          <div class="mine-shortcuts-block mt-10">
            <div class="title">{useTrans('mineAdmin.shortcuts.tabs')}</div>
            <div class="short-list">
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>1 ~ 9</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.switchN')}</div>
              </div>
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>0</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.switchLast')}</div>
              </div>
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>↑</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.toMax')}</div>
              </div>
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>↓</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.exitMax')}</div>
              </div>
              <div class="flex items-center text-sm">
                <div class="short-key">
                  <span>Alt</span>
                  +
                  <span>c</span>
                </div>
                <div>{useTrans('mineAdmin.shortcuts.close')}</div>
              </div>
            </div>
          </div>
        </m-modal>
      </>
    )
  },
})
