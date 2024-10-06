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
import type { RoleVo } from '~/base/api/role'
import { create, save } from '~/base/api/role'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

defineOptions({ name: 'permission:user:form' })

const { formType = 'add', data = null } = defineProps<{
  formType: 'add' | 'edit'
  data?: RoleVo | null
}>()

const t = useTrans().globalTrans
const userForm = ref<MaFormExpose>()
const userModel = ref<RoleVo>({})

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
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(userModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(userModel.value.id as number, userModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: userForm,
})
</script>

<template>
  <ma-form ref="userForm" v-model="userModel" />
</template>

<style scoped lang="scss">

</style>
