import type { Plugin } from '#/global'

export default defineComponent({
  name: 'SystemInfo',
  setup() {
    const { getDropdownMenu } = useUserStore()
    const dropdownMenuState = getDropdownMenu()
    const { pkg, lastBuildTime } = __MINE_SYSTEM_INFO__
    const plugins = usePluginStore().getPluginConfig()

    return () => (
      <>
        <m-drawer
          contentClass="w-380px lg:w-[450px] overflow-hidden"
          v-model={dropdownMenuState.systemInfo}
          title={useTrans('mineAdmin.userBar.systemInfo')}
        >
          <div class="mb-5 mt-2 text-left text-lg">
            {useTrans('mineAdmin.runtime.coreInfo')}
          </div>
          <div class="mine-desc-info">
            <div class="mine-desc-label">{useTrans('mineAdmin.runtime.lastBuildTime')}</div>
            <div class="mine-desc-value">{lastBuildTime}</div>
          </div>
          <div class="mine-desc-info">
            <div class="mine-desc-label">{useTrans('mineAdmin.runtime.systemVersion')}</div>
            <div class="mine-desc-value">{`v${pkg.version}`}</div>
          </div>
          <div class="mine-desc-info">
            <div class="mine-desc-label">{useTrans('mineAdmin.runtime.pluginCount')}</div>
            <div class="mine-desc-value">{Object.keys(plugins)?.length ?? 0}</div>
          </div>
          <div class="my-5 text-left text-lg">
            {useTrans('mineAdmin.runtime.pluginList')}
          </div>
          {
            Object.keys(plugins)?.map((key) => {
              const plugin = plugins[key] as Plugin.PluginConfig
              const config = plugin.config
              return (
                <>
                  <div class="mine-desc-info">
                    <div class="mine-desc-label !w-5/12">{config?.info.name}</div>
                    <div class="mine-desc-value flex items-center justify-end gap-x-1.5 !w-7/12">
                      <span>
                        {config?.info.author}
                      </span>
                      <span> / </span>
                      <span>
                        {useTrans(config?.enable ? 'mineAdmin.plugin.enabled' : 'mineAdmin.plugin.disabled')}
                      </span>
                      <span> / </span>
                      <span>
                        {config?.info.version}
                      </span>
                    </div>
                  </div>
                  <div>

                  </div>
                </>
              )
            })
          }
          <div class="my-5 text-left text-lg">
            {useTrans('mineAdmin.runtime.dependencies')}
          </div>
          {
            Object.keys(pkg.dependencies)?.map((name: string) => (
              <div class="mine-desc-info">
                <div class="mine-desc-label">{name}</div>
                <div class="mine-desc-value">
                  {pkg.dependencies[name]}
                </div>
              </div>
            ))
          }
          <div class="my-5 text-left text-lg">
            {useTrans('mineAdmin.runtime.devDependencies')}
          </div>
          {
            Object.keys(pkg.devDependencies)?.map((name: string) => (
              <div class="mine-desc-info">
                <div class="mine-desc-label">{name}</div>
                <div class="mine-desc-value">
                  {pkg.devDependencies[name]}
                </div>
              </div>
            ))
          }
          <div class="h-3"></div>
        </m-drawer>
      </>
    )
  },
})
