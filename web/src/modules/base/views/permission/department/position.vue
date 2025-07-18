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
import type { PositionVo } from '~/base/api/position.ts'
import { create, deleteByIds, page, save, setDataScope } from '~/base/api/position.ts'

import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { MaFormExpose } from '@mineadmin/form'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

import DataScope from '~/base/views/permission/component/dataScope.vue'
import { ElTag } from 'element-plus'

const { data = null } = defineProps<{
  data?: DepartmentVo | null
}>()

const dictStore = useDictStore()

const i18n = useTrans() as TransType
const t = i18n.globalTrans
const proTableRef = ref<MaProTableExpose>()
const scopeRef = ref()
const positionForm = ref<MaFormExpose>()
const postModel = ref<PositionVo>({
  dept_id: data?.id,
  dept_name: data?.name,
  policy_type: '',
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
      const elForm = positionForm.value?.getElFormRef()
      // 验证通过后
      elForm?.validate?.().then(() => {
        switch (formType) {
          // 新增
          case 'add':
            create(postModel.value).then((res: any) => {
              res.code === ResultCode.SUCCESS ? msg.success(t('crud.createSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value?.refresh()
            })
            break
          // 修改
          case 'edit':
            save(postModel.value?.id as number, postModel.value).then((res: any) => {
              res.code === 200 ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value?.refresh()
            })
            break
        }
      }).catch()
    }
    else if (formType === 'setDataScope') {
      if (postModel.value.policy_type === 'CUSTOM_FUNC') {
        postModel.value.value = [postModel.value.func_name]
      }
      if (postModel.value.policy_type === 'CUSTOM_DEPT') {
        postModel.value.value = scopeRef.value.deptRef.elTree?.getCheckedKeys()
      }
      setDataScope(postModel.value?.id as number, postModel.value).then((res: any) => {
        res.code === ResultCode.SUCCESS ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
        maDialog.close()
        proTableRef.value?.refresh()
      })
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
    rowKey: 'id',
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
  searchItems: [{ label: () => t('basePost.name'), prop: 'name', render: 'input' }],
  // 表格列
  tableColumns: [
    // 多选列
    { type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection') },
    {
      label: () => t('basePost.name'),
      prop: 'name',
      align: 'left',
    },
    {
      label: () => t('basePost.dataScope'),
      prop: 'policy',
      align: 'center',
      cellRender: ({ row }) => {
        if (!row.policy) {
          return '-'
        }
        return h(
          ElTag,
          { type: dictStore.t('data-scope', row.policy.policy_type, 'color') },
          t(dictStore.t('data-scope', row.policy.policy_type, 'i18n')),
        )
      },
    },
    {
      label: () => t('basePost.created_at'),
      prop: 'created_at',
      width: 170,
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        actions: [
          {
            name: 'dataScope',
            icon: 'mdi:sort-variant-lock-open',
            show: () => showBtn('permission:position:data_permission'),
            text: () => t('baseUserManage.setDataScope'),
            onClick: ({ row }) => {
              postModel.value.id = row.id
              postModel.value.name = row.name
              maDialog.setTitle(t('baseUserManage.setDataScope'))
              maDialog.open({ formType: 'setDataScope' })
              postModel.value.policy_type = row?.policy?.policy_type ?? undefined
              if (postModel.value.policy_type === 'CUSTOM_FUNC') {
                postModel.value.func_name = row.policy.value
              }
              if (postModel.value.policy_type === 'CUSTOM_DEPT') {
                nextTick(() => {
                  postModel.value.value = row.policy.value
                  scopeRef.value.deptRef.elTree?.setCheckedKeys(row.policy.value, true)
                })
              }
            },
          },
          {
            name: 'edit',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('permission:position:update'),
            text: () => t('crud.edit'),
            onClick: ({ row }) => {
              Object.keys(row).map((key: string) => postModel.value[key] = row[key])
              maDialog.setTitle(t('crud.edit'))
              maDialog.open({ formType: 'edit', data: row })
            },
          },
          {
            name: 'del',
            show: () => showBtn('permission:position:delete'),
            icon: 'mdi:delete',
            text: () => t('crud.delete'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('crud.delDataMessage')).then(async () => {
                const response = await deleteByIds([row.id])
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

onMounted(() => {
})
</script>

<template>
  <ma-pro-table ref="proTableRef" :options="options" :schema="schema">
    <template #actions>
      <el-button
        v-auth="['permission:position:save']"
        type="primary"
        @click="() => {
          postModel.name = ''
          maDialog.setTitle(t('crud.add'))
          maDialog.open({ formType: 'add' })
        }"
      >
        {{ t('crud.add') }}
      </el-button>
    </template>
  </ma-pro-table>

  <component :is="maDialog.Dialog">
    <template #default="{ formType }">
      <ma-form
        v-if="['add', 'edit'].includes(formType)"
        ref="positionForm"
        v-model="postModel"
        :options="{ labelWidth: 95 }"
        :items="[
          {
            label: () => '所属部门',
            prop: 'dept_name',
            render: 'input',
            renderProps: { disabled: true },
          },
          {
            label: () => t('basePost.name'),
            prop: 'name',
            render: 'input',
            renderProps: { placeholder: t('form.pleaseInput', { msg: t('basePost.name') }) },
            itemProps: { rules: [{ required: true, message: t('form.requiredInput', { msg: t('basePost.placeholder.name') }) }] },
          },
        ]"
      />
      <DataScope v-if="formType === 'setDataScope'" ref="scopeRef" v-model="postModel" :label="t('basePost.belongPost')" />
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
