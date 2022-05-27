<template>
  <el-config-provider :locale="$i18n.messages[$i18n.locale].el" :button="{autoInsertSpace: false}">
    <router-view></router-view>
  </el-config-provider>
</template>

<script>
  import colorTool from '@/utils/color'

  export default {
    name: 'App',
    created() {
      //设置主题颜色
      const app_color = this.$TOOL.data.get('APP_COLOR') || this.$CONFIG.COLOR
      const theme = this.$TOOL.data.get('APP_THEME')
      if(app_color){
        document.documentElement.style.setProperty('--el-color-primary', app_color);
        for (let i = 1; i <= 9; i++) {
          if (theme === 'default') {
            document.documentElement.style.setProperty(
              `--el-color-primary-light-${i}`,
              colorTool.lighten(app_color, i / 10)
            )
          } else {
            document.documentElement.style.removeProperty(`--el-color-primary-light-${i}`)
          }
        }
        for (let i = 1; i <= 2; i++) {
          document.documentElement.style.setProperty(
            `--el-color-primary-dark-${i}`,
            colorTool.darken(app_color, i / 10)
          )
        }
      }
      // 获取配置信息
      this.$API.config.getSysConfig().then(res => {
        res.data.map(item => {
          this.$TOOL.data.set(item.key, item.value)
        })
      })
    }
  }
</script>

<style lang="scss">
  @import '@/style/style.scss';
</style>
