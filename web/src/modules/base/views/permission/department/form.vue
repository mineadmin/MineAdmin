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
import type { DepartmentVo } from '~/base/api/department.ts'
import { create, save } from '~/base/api/department'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import { useMessage } from '@/hooks/useMessage.ts'

defineOptions({ name: 'permission:department:form' })

const { formType = 'add', data = null } = defineProps<{
  formType: 'add' | 'edit'
  data?: DepartmentVo | null
}>()

const t = useTrans().globalTrans
const departmentForm = ref<MaFormExpose>()
const deptModel = ref<DepartmentVo>({})
const msg = useMessage()

useForm('departmentForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      deptModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, deptModel.value, msg))
  form.setOptions({
    labelWidth: '80px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    create(deptModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    save(deptModel.value.id as number, deptModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  add,
  edit,
  maForm: departmentForm,
})
</script>

<template>
  <ma-form ref="departmentForm" v-model="deptModel" />
</template>

<style scoped lang="scss">

</style>
