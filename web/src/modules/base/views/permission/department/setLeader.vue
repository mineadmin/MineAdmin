<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link https://github.com/mineadmin
-->

<script setup lang="tsx">
import type { DepartmentVo } from '~/base/api/department.ts'
import type { LeaderVo } from '~/base/api/leader.ts'
import { create, deleteByDoubleKey, page, save } from '~/base/api/leader.ts'
import { page as userPage } from '~/base/api/user.ts'

import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { MaFormExpose } from '@mineadmin/form'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

import MaSelectTable from '@/components/ma-select-table/index.vue'

const { data = null } = defineProps<{
  data?: DepartmentVo | null
}>()

const i18n = useTrans() as TransType
const t = i18n.globalTrans
const selections = ref<any[]>([])
const proTableRef = ref<MaProTableExpose>()
const leaderForm = ref<MaFormExpose>()
const leaderModel = ref<LeaderVo>({
  user_id: null,
  dept_id: data?.id,
  dept_name: data?.name,
})

const msg = useMessage()

function showBtn(auth: string | string[]) {
  return hasAuth(auth)
}

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '550px',
  ok: ({ formType }, okLoadingState: (state: boolean) => void) => {
    okLoadingState(true)
    if (['add', 'edit'].includes(formType)) {
      const elForm = leaderForm.value?.getElFormRef()
      // 验证通过后
      elForm?.validate?.().then(() => {
        leaderModel.value.user_id = leaderModel.value.users.map((item: any) => item.id)
        delete leaderModel.value.users
        switch (formType) {
          // 新增
          case 'add':
            create(leaderModel.value).then((res: any) => {
              res.code === ResultCode.SUCCESS ? msg.success(t('crud.createSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value?.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
          // 修改
          case 'edit':
            save(leaderModel.value?.id as number, leaderModel.value).then((res: any) => {
              res.code === 200 ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value?.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
        }
      }).catch()
    }
    okLoadingState(false)
  },
})

// 参数配置
const options = ref<MaProTableOptions>({
  header: { show: false },
  // 表格参数
  tableOptions: {
    adaption: false,
    height: 400,
    rowKey: 'user_id',
    on: {
      // 表格选择事件
      onSelectionChange: (selection: any[]) => selections.value = selection,
    },
  },
  // 搜索参数
  searchOptions: {
    fold: true,
    text: {
      searchBtn: () => t('crud.search'),
      resetBtn: () => t('crud.reset'),
    },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '90px' },
  // 请求配置
  requestOptions: {
    // autoRequest: false,
    requestParams: {
      dept_id: data?.id,
    },
    api: page,
  },
})

// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: [{
    label: () => t('baseDeptLeader.user_id'),
    prop: 'users',
    render: () => {
      return h(MaSelectTable, {
        api: userPage,
        showKey: 'username',
        multiple: false,
        selectProps: {
          placeholder: t('form.pleaseSelect', { msg: t('baseUserManage.username') }),
        },
        columns: [
          { label: () => t('baseUserManage.username'), prop: 'username' },
          { label: () => t('baseUserManage.nickname'), prop: 'nickname' },
          { label: () => t('baseUserManage.phone'), prop: 'phone' },
        ],
        searchItems: [{
          label: t('baseUserManage.username'), prop: 'username', render: 'input',
        }],
      })
    },
    span: 2,
  }],
  // 表格列
  tableColumns: [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    {
      label: () => t('baseDeptLeader.username'),
      prop: 'user.username',
    },
    {
      label: () => t('baseUserManage.phone'),
      prop: 'user.phone',
    },
    {
      label: () => t('baseUserManage.email'),
      prop: 'user.email',
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'del',
            show: () => showBtn('permission:leader:delete'),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByDoubleKey(row.dept_id, [row.user_id])
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('crud.delSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ],
})

// 批量删除
function handleDelete() {
  const deptId = selections.value[0].dept_id
  const userIds = selections.value.map((item: any) => item.user_id)
  msg.confirm(t('crud.delMessage')).then(async () => {
    const response = await deleteByDoubleKey(deptId, userIds)
    if (response.code === ResultCode.SUCCESS) {
      msg.success(t('crud.delSuccess'))
      await proTableRef?.value?.refresh?.()
    }
  })
}
</script>

<template>
  <ma-pro-table
    ref="proTableRef"
    :options="options"
    :schema="schema"
    @search-submit="(form: any) => {
      form.user_id = form.users.id
    }"
    @search-reset="(form: any) => {
      form.user_id = undefined
    }"
  >
    <template #toolbarLeft>
      <div>
        <el-button
          v-auth="['permission:leader:save']"
          type="primary"
          @click="() => {
            maDialog.setTitle(t('crud.add'))
            maDialog.open({ formType: 'add' })
          }"
        >
          {{ t('crud.add') }}
        </el-button>
        <el-button
          v-auth="['permission:leader:delete']"
          type="danger"
          plain
          :disabled="selections.length < 1"
          @click="handleDelete"
        >
          {{ t('crud.delete') }}
        </el-button>
      </div>
    </template>
  </ma-pro-table>

  <component :is="maDialog.Dialog">
    <template #default="{ formType }">
      <ma-form
        v-if="['add', 'edit'].includes(formType)"
        ref="leaderForm"
        v-model="leaderModel"
        :options="{ labelWidth: 95 }"
        :items="[
          {
            label: () => t('baseDeptLeader.belongDept'),
            prop: 'dept_name',
            render: 'input',
            renderProps: { disabled: true },
          },
          {
            label: () => t('baseDeptLeader.user_id'),
            prop: 'users',
            render: () => MaSelectTable,
            renderProps: {
              api: userPage,
              showKey: 'username',
              multiple: true,
              selectProps: {
                placeholder: t('form.pleaseSelect', { msg: t('baseDeptLeader.user_id') }),
              },
              columns: [
                { label: () => t('baseUserManage.username'), prop: 'username' },
                { label: () => t('baseUserManage.nickname'), prop: 'nickname' },
                { label: () => t('baseUserManage.phone'), prop: 'phone' },
              ],
              searchItems: [{
                label: t('baseUserManage.username'), prop: 'username', render: 'input',
              }],
            },
            itemProps: { rules: [{ required: true, message: t('form.requiredSelect', { msg: t('baseDeptLeader.placeholder.user_id') }) }] },
          },
        ]"
      />
    </template>
  </component>
</template>

<style scoped lang="scss">
:deep(.mineadmin-pro-table-search) {
  margin: 0; padding: 0;
  @apply pt-3;
}
:deep(.mine-card) {
  margin: 0;
}
</style>
