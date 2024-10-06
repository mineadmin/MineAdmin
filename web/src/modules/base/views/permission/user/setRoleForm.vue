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
import type { MaFormExpose } from '@mineadmin/form'
import type { RoleVo } from '~/base/api/role.ts'
import { page } from '~/base/api/role.ts'
import type { UserVo } from '~/base/api/user.ts'
import { getUserRole, setUserRole } from '~/base/api/user.ts'

import useForm from '@/hooks/useForm.ts'

import MaRemoteSelect from '@/components/ma-remote-select/index.vue'
import { ResultCode } from '@/utils/ResultCode.ts'

const { data = null } = defineProps<{
  data?: UserVo | null
}>()

const t = useTrans().globalTrans
const userRoleForm = ref<MaFormExpose>()
const userModel = ref<{ id?: number, roleCode?: string[] }>({
  roleCode: [],
})

useForm('userRoleForm').then(async (form: MaFormExpose) => {
  if (data?.id) {
    userModel.value.id = data.id
    const response = await getUserRole(data?.id)
    if (response.code === ResultCode.SUCCESS) {
      userModel.value.roleCode = response.data.map((item: any) => item.code)
    }
  }

  form.setItems([
    {
      label: () => t('baseUserManage.role'),
      prop: 'roleCode',
      render: () => MaRemoteSelect,
      renderProps: {
        multiple: true,
        placeholder: t('form.pleaseSelect', { msg: t('baseUserManage.role') }),
        api: () => new Promise(resolve => resolve(page({ page_size: 999 }))),
        dataHandle: (response: any) => {
          return response.data.list?.map((item: RoleVo) => {
            return { label: `${item.name}-${item.code}`, value: item.code }
          })
        },
      },
      itemProps: {
        rules: [{ required: true, message: t('form.requiredSelect', { msg: t('baseUserManage.role') }) }],
      },
    },
  ])
  form.setOptions({
    labelWidth: '80px',
  })
})

// 保存用户角色
function saveUserRole(): Promise<any> {
  return new Promise((resolve, reject) => {
    setUserRole(userModel.value.id as number, userModel.value.roleCode as string[]).then((res: any) => {
      res.code === ResultCode.SUCCESS ? resolve(res) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}

defineExpose({
  saveUserRole,
  maForm: userRoleForm,
})
</script>

<template>
  <ma-form ref="userRoleForm" v-model="userModel" />
</template>

<style scoped lang="scss">

</style>
