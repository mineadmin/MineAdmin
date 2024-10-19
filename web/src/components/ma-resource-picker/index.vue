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
import { omit } from 'lodash-es'
import MaResourcePanel from './panel.vue'
import type { Resource } from './type.ts'

defineOptions({ name: 'MaResourcePicker' })

const emit = defineEmits<{
  cancel: []
  confirm: [selected: Resource[]]
}>()
const dialogVisible = defineModel<boolean>('visible', { default: false })
function onCancel() {
  dialogVisible.value = false
  emit('cancel')
}

function onConfirm(selected: any[]) {
  dialogVisible.value = false
  emit('confirm', selected)
}

// 获得所有attrs
const attrs = omit(useAttrs(), ['onConfirm', 'onCancel'])
</script>

<template>
  <MaDialog
    v-model="dialogVisible"
    title="资源选择器"
    append-to-body
    destroy-on-close
    align-center
    :footer="false"
  >
    <div class="h-[595px]">
      <MaResourcePanel v-bind="attrs" @cancel="onCancel" @confirm="onConfirm" />
    </div>
  </MaDialog>
</template>

<style scoped lang="scss">

</style>
