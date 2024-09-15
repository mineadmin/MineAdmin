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
import { ElDialog } from 'element-plus'
import { omit } from 'lodash-es'
import MaResourcePanel from './panel.vue'
import type { ResourcePickerEmits } from '@/components/ma-resource-picker/type.ts'

defineOptions({ name: 'MaResourcePicker' })

const emit = defineEmits<ResourcePickerEmits>()

const dialogVisible = defineModel<boolean>('visible')

function onCancel() {
  dialogVisible.value = false
  emit('cancel')
}

function onConfirm(data: any[]) {
  dialogVisible.value = false
  emit('confirm', data)
}

// 获得所有attrs
const attrs = omit(useAttrs(), ['onConfirm', 'onCancel'])
</script>

<template>
  <ElDialog
    v-model="dialogVisible"
    title="资源选择器"
    width="800"
    append-to-body
    draggable
    destroy-on-close
    align-center
  >
    <div class="h-[595px]">
      <MaResourcePanel v-bind="attrs" @cancel.stop="onCancel" @confirm.stop="onConfirm" />
    </div>
  </ElDialog>
</template>

<style scoped lang="scss">

</style>
