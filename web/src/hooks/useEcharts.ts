import useEcharts from '@mineadmin/echarts'

function themeMode() {
  return useSettingStore().getSettings('app').colorMode === 'dark' ? 'mineDark' : 'default'
}

export {
  themeMode,
  useEcharts,
}
