<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="ts">
import { download, getDetail, install, unInstall } from '../api/app.ts'
import type { AppVo } from '../api/app.ts'

const dataList = inject('dataList')

const dataState = ref<Record<string, boolean>>({
  visible: false,
  loading: false,
  buttonLoading: false,
  buttonLoading: false,
  isInstall: false,
  isUpdated: false,
})

function open(identifier) {
  dataState.value.visible = true
  dataState.value.loading = true
  dataState.value.buttonLoading = false
  getDetail({ identifier }).then((res: AppVo) => {
    loading.value = false
    data.value = res.data.data
    const key = `${data.value.app.identifier}`
    if (checkInstallStatus(key)) {
      isInstall.value = true
    }
    if (dataList.value.local[key]) {
      if (versionCompare(dataList.value.local[key]?.version, data.value.version[0].version) < 0) {
        isUpdated.value = true
      }
    }
  })
}

function checkInstallStatus(name) {
  return !isUndefined(dataList.value.local[name]) && dataList.value.local[name].status
}

function openPage() {
  window.open(`https://www.mineadmin.com/store/${data.value.app.identifier}`)
}

async function downloadAndInstall() {
  if (!data.value) {
    Message.error('获取应用信息失败，无法安装，重新打开应用详情获取数据后再试')
    return false
  }

  const body = {
    identifier: data.value.app.identifier,
    version: data.value.version[0].version,
  }

  buttonLoading.value = true
  installButtonText.value = '应用下载中...'
  const downloadRes = download(body).then(async (_) => {
    Message.success('应用下载成功，现在开始安装...')
  }).catch((e) => {
    return false
  })

  if (await downloadRes === false) {
    Message.error('应用下载失败')
    installButtonText.value = installBtnDefaultText
    buttonLoading.value = false
    return false
  }

  installButtonText.value = '安装应用中...'
  const installRes = install(body).then((_) => {
    Message.success('应用安装成功，请手动重启后端服务')
    buttonLoading.value = false
    isInstall.value = true
  }).catch((e) => {
    return false
  })

  if (await installRes === false) {
    Message.error('应用安装失败')
    installButtonText.value = installBtnDefaultText
    buttonLoading.value = false
    return false
  }
}

function unInstallApp() {
  if (!data.value) {
    Message.error('获取应用信息失败，无法卸载，重新打开应用详情获取数据后再试')
    return false
  }

  const body = {
    identifier: data.value.app.identifier,
    version: data.value.version[0].version,
  }

  buttonLoading.value = true
  unInstallBtnDefaultText.value = '应用卸载中...'
  unInstall(body).then((_) => {
    Message.success('应用卸载成功')
    buttonLoading.value = false
    isInstall.value = false
  }).catch((e) => {
    return false
  })
}

function updatedApp() {
  if (!data.value) {
    Message.error('获取应用信息失败，无法更新，重新打开应用详情获取数据后再试')
    return false
  }
}

defineExpose({ open })
</script>

<template>
  <el-drawer v-model="dataState.visible" append-to-body>
    asd
  </el-drawer>
</template>

<style scoped lang="scss">

</style>
