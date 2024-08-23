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
import MaResourcePanel from './panel.vue'

defineOptions({ name: 'MaResourceDialog' })

const emit = defineEmits(['cancel', 'select'])

const dialogVisible = defineModel<boolean>('visible')

function onCancel() {
  dialogVisible.value = false
  emit('cancel')
}

function onSelect(data: any[]) {
  dialogVisible.value = false
  emit('select', data)
}
</script>

<template>
  <el-dialog
    v-model="dialogVisible"
    title="资源选择器"
    width="800"
    append-to-body
    draggable
    destroy-on-close
    align-center
  >
    <div class="h-[600px]">
      <MaResourcePanel v-bind="$attrs" @cancel="onCancel" @select="onSelect" />
    </div>
  </el-dialog>
</template>

<style scoped lang="scss">

</style>
