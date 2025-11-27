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
import { create, save } from '~/base/api/user'
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'
import useForm from '@/hooks/useForm.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import DataScope from '../component/dataScope.vue'

defineOptions({ name: 'permission:user:form' })
const { formType = 'add', data = null } = defineProps<{
  formType?: 'add' | 'edit'
  data?: UserVo | null
}>()

const t = useTrans().globalTrans
const userForm = ref<MaFormExpose>()
const userModel = ref<UserVo>({})
const scopeRef = ref()
const deptData = inject('deptData')

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '750px',
  ok: () => {
    if (userModel.value.policy.policy_type === 'CUSTOM_FUNC') {
      userModel.value.policy.value = [userModel.value.policy.func_name]
    }
    if (userModel.value.policy.policy_type === 'CUSTOM_DEPT') {
      userModel.value.policy.value = scopeRef.value.deptRef.elTree?.getCheckedKeys()
    }
    maDialog.close()
  },
})

useForm('userForm').then((form: MaFormExpose) => {
  if (formType === 'edit' && data) {
    Object.keys(data).map((key: string) => {
      userModel.value[key] = data[key]
    })
  }
  form.setItems(getFormItems(formType, t, userModel.value, deptData, maDialog, scopeRef))
  form.setOptions({
    labelWidth: '90px',
  })
})

// 创建操作
function add(): Promise<any> {
  return new Promise((resolve, reject) => {
    if (userModel.value.policy?.value?.length === 0) {
      userModel.value.policy = undefined
    }
    create(userModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    })
  })
}

// 更新操作
function edit(): Promise<any> {
  return new Promise((resolve, reject) => {
    if (userModel.value.policy === null) {
      userModel.value.policy = []
    }
    save(userModel.value.id as number, userModel.value).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
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
  <div>
    <ma-form ref="userForm" v-model="userModel" />
    <component :is="maDialog.Dialog">
      <DataScope ref="scopeRef" v-model="userModel.policy" :label="t('baseUserManage.username')" />
    </component>
  </div>
</template>

<style scoped lang="scss">

</style>
