<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<i18n lang="yaml">
zh_CN:
  pluginNotExists: '要安装的插件：%{name} 不存在'
zh_TW:
  pluginNotExists: '要安裝的外掛程式：%{name} 不存在'
en:
  pluginNotExists: 'Plugin to install：%{name} Does not exist'
</i18n>

<script setup lang="ts">
import type { MaTableExpose } from '@mineadmin/table'

const tableRef = ref<MaTableExpose>()
const storeMeta = inject('storeMeta') as Record<string, any>
const dataList = inject('dataList') as Record<string, any>

nextTick(() => {
  const data = Object.keys(dataList.value.local).map((name) => {
    return {
      name,
      status: dataList.value.local[name].status,
      version: dataList.value.local[name].version,
    }
  })
  tableRef.value?.setData(data)
  tableRef.value?.setColumns([
    { label: '名称', prop: 'name', width: '300px', headerAlign: 'left', align: 'left' },
    { label: '状态', prop: 'status' },
    { label: '版本', prop: 'version' },
  ])
})
</script>

<template>
  <div class="mine-card">
    <ma-table ref="tableRef" />
  </div>
</template>

<style scoped lang="scss">

</style>
