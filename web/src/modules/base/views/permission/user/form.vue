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
import type { UserVo } from '~/base/api/user'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'

defineOptions({ name: 'permission:user:form' })

const { formType = 'add', data = null } = defineProps<{
  formType: 'add' | 'edit'
  data?: UserVo | null
}>()

const t = useTrans().globalTrans
const userForm = ref<MaFormExpose>()
const userModel = ref<UserVo>({})

useForm('userForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      userModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, userModel.value))
  form.setOptions({
    labelWidth: '80px',
  })
})

// 创建操作
function create() {

}

// 更新操作
function save() {

}

defineExpose({
  create,
  save,
  model: userModel,
  maForm: userForm,
})
</script>

<template>
  <ma-form ref="userForm" v-model="userModel" />
</template>

<style scoped lang="scss">

</style>
