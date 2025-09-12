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
import type { Ref, UnwrapRef } from 'vue'
import type { MaFormExpose, MaFormItem, MaFormOptions } from '@mineadmin/form'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import type { UseDrawerExpose } from '@/hooks/useDrawer.ts'
import useDialog from '@/hooks/useDialog.ts'
import useDrawer from '@/hooks/useDrawer.ts'

defineOptions({ name: 'MaFormOpen' })

const { modalType = 'dialog', modalOptions = {}, formOptions = {}, items = [] } = defineProps<{
  modalType?: 'dialog' | 'drawer'
  modalOptions?: Record<string, any>
  formOptions?: MaFormOptions
  items?: MaFormItem[]
}>()
const emit = defineEmits<{
  (e: 'submit', value: {
    data: unknown
    proxy: {
      modal: Ref<UnwrapRef<UseDialogExpose | UseDrawerExpose>, UnwrapRef<UseDialogExpose | UseDrawerExpose> | UseDialogExpose | UseDrawerExpose>
      maForm: Ref<MaFormExpose | undefined>
      [key: string]: any
    }
  })
  (e: 'update:modelValue', value: Record<string, any>): void
}>()
const dialog: UseDialogExpose = useDialog()
const drawer: UseDrawerExpose = useDrawer()

const model = defineModel()
const modal = ref(modalType === 'dialog' ? dialog : drawer)

const form = ref<MaFormExpose>()

const modalOpt = ref(Object.assign({
  ok: (args: any[], okLoadingState: any) => {
    const elForm = form?.value?.getElFormRef()
    elForm?.validate().then(() => {
      emit('submit', { data: model.value, proxy: { modal, maForm: form, args: { ...args }, okLoadingState } })
    }).catch()
  },
}, modalOptions))

defineExpose({
  modal,
  maForm: form,
})
</script>

<template>
  <div>
    <slot name="default" />
    <component :is="modalType === 'dialog' ? dialog.Dialog : drawer.Drawer" v-bind="modalOpt">
      <ma-form ref="form" v-model="model" :options="formOptions" :items="items" />
    </component>
  </div>
</template>

<style scoped lang="scss">

</style>
