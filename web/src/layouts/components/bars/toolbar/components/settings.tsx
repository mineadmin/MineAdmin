/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MineToolbar, SystemSettings } from '#/global'
import useThemeColor from '@/hooks/useThemeColor.ts'
import { useSortable } from '@vueuse/integrations/useSortable'
import { useI18n } from 'vue-i18n'
import Message from 'vue-m-message'

export default defineComponent({
  name: 'settings',
  setup() {
    const display = ref(false)
    const settingStore = useSettingStore()
    const toolbarHook = useGlobal().$toolbars
    const keys = ref<Record<string, unknown>>({
      colorMode: settingStore.getSettings('app')?.colorMode,
      tabMode: settingStore.getSettings('tabbar')?.mode,
    })

    const el = ref<HTMLElement | null>(null)
    const { t } = useI18n()

    const toolbarList = computed(() => {
      const toolbarSettings = settingStore.getSettings('toolBars') || []
      return toolbarHook.toolbars.value.map((item: MineToolbar) => {
        const savedItem = toolbarSettings.find((setting: MineToolbar) => setting.name === item.name)
        return {
          ...item,
          show: savedItem ? savedItem.show : item.show, // 统一处理 show 值
        }
      })
    })

    const divider = (title: string) => {
      return (
        <div class="relative my-5 h-6 flex items-center">
          <div class="h-[1px] w-full bg-gray-2 dark:bg-dark-1" />
          <div class="absolute left-1/2 bg-white px-3 text-[14px] text-gray-8 -top-[-1px] -translate-x-1/2 dark-bg-dark-5 dark-text-gray-1">
            {title}
          </div>
        </div>
      )
    }

    const tabsRender = (key: string, options: { icon?: string, label: string, value: any }[], change: (value: any) => void, className: string = 'h-8 text-sm') => {
      return (
        <m-tabs
          class={className}
          v-model={keys.value[key]}
          options={options}
          onChange={(v: any) => change(v)}
        />
      )
    }

    const colorModeSettings = () => {
      return tabsRender(
        'colorMode',
        [
          { icon: 'material-symbols:sunny-outline-rounded', label: useTrans('mineAdmin.settings.colorModes.light') as string, value: 'light' },
          { icon: 'material-symbols:dark-mode-outline', label: useTrans('mineAdmin.settings.colorModes.dark') as string, value: 'dark' },
          { icon: 'lets-icons:color-mode-light', label: useTrans('mineAdmin.settings.colorModes.system') as string, value: 'autoMode' },
        ],
        async (v: any) => await settingStore.toggleColorMode(v),
      )
    }

    const colorListRender = () => {
      const colorList: string[] = [
        '#3790ff',
        '#EF4444',
        '#2563EB',
        '#A855F7',
        '#15803D',
        '#EAB308',
        '#EC4899',
        '#F97316',
        '#10B981',
        '#F59E0B',
        '#F43F5E',
        '#549f08',
        '#0C0A09',
      ]

      return (
        <>
          {
            colorList.map((color: string) => (
              <li
                style={`background-color:${color}`}
                onClick={() => {
                  useThemeColor().setThemeColor(color)
                  settingStore.getSettings('app').primaryColor = color
                }}
              >
                {(settingStore.getSettings('app').primaryColor === color) && <ma-svg-icon class="icon" name="i-material-symbols:check-small" />}
              </li>
            ))
          }
        </>
      )
    }

    const layoutsRender = () => {
      return (
        <div class="mine-layout-setting">
          <m-tooltip text={useTrans('mineAdmin.settings.layouts.classic')}>
            <div
              class={{ 'classic': true, 'mine-selected-layout': settingStore.isClassicLayout() }}
              onClick={() => settingStore.getSettings('app').layout = 'classic'}
            >
              <div class="h-full w-[25%] rounded-l bg-[rgb(var(--ui-primary))]"></div>
              <div class="h-full w-[75%] rounded-r bg-[rgb(var(--ui-primary)/40%)]"></div>
            </div>
          </m-tooltip>
          <m-tooltip text={useTrans('mineAdmin.settings.layouts.columns')}>
            <div
              class={{ 'columns': true, 'mine-selected-layout': settingStore.isColumnsLayout() }}
              onClick={() => settingStore.getSettings('app').layout = 'columns'}
            >
              <div class="h-full w-[10%] rounded-l bg-[rgb(var(--ui-primary))]"></div>
              <div class="h-full w-[20%] bg-[rgb(var(--ui-primary)/40%)]"></div>
              <div class="h-full w-[70%] rounded-r bg-[rgb(var(--ui-primary)/15%)]"></div>
            </div>
          </m-tooltip>
          <m-tooltip text={useTrans('mineAdmin.settings.layouts.mixed')}>
            <div
              class={{ 'mixed': true, 'mine-selected-layout': settingStore.isMixedLayout() }}
              onClick={() => settingStore.getSettings('app').layout = 'mixed'}
            >
              <div class="h-full w-full flex flex-col gap-y-0.5">
                <div class="h-[15%] w-full rounded-t bg-[rgb(var(--ui-primary))]"></div>
                <div class="h-[85%] w-full flex gap-x-0.5">
                  <div class="h-full w-[20%] rounded-l rounded-t-none bg-[rgb(var(--ui-primary)/40%)]"></div>
                  <div class="h-full w-[80%] rounded-r rounded-t-none bg-[rgb(var(--ui-primary)/15%)]"></div>
                </div>
              </div>
            </div>
          </m-tooltip>
        </div>
      )
    }

    const settingsRender = (name: SystemSettings.settingType, options: { label: string, value: string, change?: (e: boolean) => void }[]) => {
      const setting = settingStore.getSettings(name)
      return (
        <>
          {options.map((item: any) => (
            <div class="mine-setting-description">
              <div class="desc-label">{item.label}</div>
              <div class="desc-value">
                <m-switch v-model={setting[item.value]} onChange={(e: boolean) => item?.change?.(e)} />
              </div>
            </div>
          ))}
        </>
      )
    }

    const tabSettingRender = () => {
      return (
        <>
          {
            settingsRender('tabbar', [
              { label: useTrans('mineAdmin.settings.tabbars.enable') as string, value: 'enable' },
            ])
          }
          <div class="mine-setting-description">
            <div class="desc-label">{useTrans('mineAdmin.settings.tabbars.mode')}</div>
            <div class="desc-value">
              {tabsRender('tabMode', [
                { label: useTrans('mineAdmin.settings.tabbars.modeDefault') as string, value: 'rectangle' },
                { label: useTrans('mineAdmin.settings.tabbars.modeCard') as string, value: 'card' },
              ], (v: string) => settingStore.getSettings('tabbar').mode = v, 'h-6 text-[12px]')}
            </div>
          </div>
        </>
      )
    }

    const toolbarRender = () => {
      return (
        <>
          {toolbarList.value.map((item: MineToolbar) => {
            return (
              <div class="mine-setting-description">
                <div class="desc-label">{useTrans(item.title)}</div>
                <div class="desc-value">
                  <m-switch
                    v-model={item.show}
                    disabled={item.name === 'settings'}
                    onUpdate:modelValue={(v: boolean) => {
                      settingStore.setToolBar(item.name, v)
                    }}
                  />
                </div>
              </div>
            )
          })}
          <div class="mt-3 flex items-center justify-between rounded p-2 ring-2 ring-[rgb(var(--ui-primary))]">
            <div class="text-sm">{useTrans('mineAdmin.toolbars.sorts')}</div>
            <div class="flex items-center gap-x-3" ref={el}>
              {toolbarHook.getShowToolbar().map((item: MineToolbar) => (
                <ma-svg-icon name={item.icon} size={20} class="cursor-e-resize" />
              ))}
            </div>
          </div>
        </>
      )
    }

    const copyrightRender = () => {
      const setting = settingStore.getSettings('copyright')
      const options = [
        { label: useTrans('mineAdmin.settings.copyRights.date') as string, value: 'dates' },
        { label: useTrans('mineAdmin.settings.copyRights.company') as string, value: 'company' },
        { label: useTrans('mineAdmin.settings.copyRights.website') as string, value: 'website' },
        { label: useTrans('mineAdmin.settings.copyRights.putOnRecord') as string, value: 'putOnRecord' },
      ]
      return (
        <>
          {
            settingsRender('copyright', [
              { label: useTrans('mineAdmin.settings.copyRights.enable') as string, value: 'enable' },
            ])
          }
          { options.map((item: any) => (
            <div class="mine-setting-description">
              <div class="desc-label !w-6/12">{item.label}</div>
              <div class="desc-value !w-6/12">
                <m-input
                  class="mb-1 !h-6 !py-1"
                  v-model={setting[item.value]}
                  onInput={(v: InputEvent) => {
                    setting[item.value] = (v.target as HTMLInputElement).value
                  }}
                />
              </div>
            </div>
          ))}
        </>
      )
    }

    return () => (
      <div class="flex items-center">
        <m-drawer
          v-model={display.value}
          contentClass="w-380px"
          title={useTrans('mineAdmin.settings.title')}
          v-slots={{
            trigger: () => (
              <ma-svg-icon
                class="tool-icon animate-spin animate-duration-[5s]"
                name="heroicons:cog-solid"
                size={20}
                onClick={async () => {
                  display.value = true
                  await nextTick()
                  useSortable(el, toolbarHook.toolbars, { animation: 300 })
                }}
              />
            ),
            default: () => (
              <div class="pb-10">
                {divider(useTrans('mineAdmin.settings.colorMode') as string)}
                <div class="mx-auto mt-3 w-[70%]">
                  {colorModeSettings()}
                </div>
                {keys.value.colorMode !== 'dark' && settingsRender('app', [
                  { label: useTrans('mineAdmin.settings.asideDark') as string,
                    value: 'asideDark',
                    change: (v: boolean) => settingStore.setAsideDark(v),
                  },
                ])}

                {divider(useTrans('mineAdmin.settings.primaryColorSetting') as string)}
                <ul class="mine-setting-color-list">
                  {colorListRender()}
                </ul>

                {divider(useTrans('mineAdmin.settings.layoutSetting') as string)}
                {layoutsRender()}

                {divider(useTrans('mineAdmin.settings.mainAsideSetting') as string)}
                {
                  settingsRender('mainAside', [
                    { label: useTrans('mineAdmin.settings.mainAsides.mainMenuTitle') as string, value: 'showTitle' },
                    { label: useTrans('mineAdmin.settings.mainAsides.autoToFirstMenu') as string, value: 'enableOpenFirstRoute' },
                  ])
                }

                {divider(useTrans('mineAdmin.settings.tabBarSettings') as string)}
                {tabSettingRender()}

                {divider(useTrans('mineAdmin.settings.toolBarSettings') as string)}
                {toolbarHook.state && toolbarRender()}

                {divider(useTrans('mineAdmin.settings.copyRightSettings') as string)}
                {copyrightRender()}

                {divider(useTrans('mineAdmin.settings.otherSettings') as string)}
                {
                  settingsRender('app', [
                    { label: useTrans('mineAdmin.settings.enableBreadcrumb') as string, value: 'showBreadcrumb' },
                    { label: useTrans('mineAdmin.settings.enableWatermark') as string, value: 'enableWatermark' },
                  ])
                }
                <div class="mine-setting-description">
                  <div class="desc-label !w-6/12">{useTrans('mineAdmin.settings.watermarkText')}</div>
                  <div class="desc-value !w-6/12">
                    <m-input
                      class="mb-1 !h-6 !py-1"
                      v-model={settingStore.getSettings('app').watermarkText}
                      onInput={(v: InputEvent) => {
                        const value = (v.target as HTMLInputElement).value
                        settingStore.getSettings('app').watermarkText = value.includes(',') ? value.split(',') : value
                        settingStore.openGlobalWatermark()
                      }}
                    />
                  </div>
                </div>
              </div>
            ),
            footer: () => (
              <m-button
                class="block w-full !py-2"
                onClick={() => {
                  useUserStore().saveSettingToSever()
                  Message.success(t('mineAdmin.common.saveSuccess'), { zIndex: 9999 })
                }}
              >
                {useTrans('mineAdmin.settings.save')}
              </m-button>
            ),
          }}
        />
      </div>
    )
  },
})
