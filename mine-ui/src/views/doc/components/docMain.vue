<template>
  <el-container>
    <el-header>
      <div class="logo">
        <img src="img/logo.svg" width="54" /> <span class="title">API Document</span>
        <span class="sys_name">MineAdmin</span>
      </div>
      <el-menu :default-active="active" mode="horizontal" style="top: -1px">
        <el-menu-item index="interfaceList" @click="$router.push({ name: 'interfaceList' })">接口列表</el-menu-item>
        <el-menu-item index="interfaceCode" @click="$router.push({ name: 'interfaceCode' })">代码释义</el-menu-item>
        <el-menu-item index="signature" @click="$router.push({ name: 'signature' })">签名算法</el-menu-item>
        <el-submenu index="parent">
          <el-menu-item index="switchApp" @click="switchApp()">
            <el-icon><el-icon-refresh /></el-icon>切换应用
          </el-menu-item>
          <el-menu-item index="dashboard" @click="$router.push('/')">
            <el-icon><ma-icon-mineadmin /></el-icon>返回后台
          </el-menu-item>
        </el-submenu>
      </el-menu>
    </el-header>

    <el-main>
      <router-view />
    </el-main>

    <el-footer class="footer">
      MineAdmin {{ $CONFIG.APP_VER }} Copyright © 2021-2022 mineadmin.com All Rights Reserved.
    </el-footer>
  </el-container>
</template>

<script>
export default {
  created () {
    this.active = this.$route.name
    this.$router.push({ name: 'interfaceList' })
  },
  data () {
    return {
      active: ''
    }
  },
  methods: {
    switchApp () {
      this.$TOOL.data.set('apiAuth', false)
      this.$TOOL.data.set('appId', null)
      this.$router.push({ name: 'doc' })
    }
  }
}
</script>

<style scoped lang="scss">
.logo {
  img {
    position: relative;
    top: 6px;
    margin-right: 10px;
  }
  .title {
    font-size: 28px; margin-right: 5px;
  }
  .sys_name {
    color: #fff; background: rgb(220,38,38);
    padding: 0 4px; border-radius: 2px; font-family: inherit;
  }
}
.footer {
  text-align: center; line-height: 32px;
}
</style>