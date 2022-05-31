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
      colorTool.setPrimaryColor(app_color, theme)
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
