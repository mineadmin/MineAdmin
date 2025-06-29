<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { deleteByIds, page } from '~/base/api/user'
import { page as departmentList } from '~/base/api/department'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

import UserForm from './form.vue'
import SetRoleForm from './setRoleForm.vue'

defineOptions({ name: 'permission:user' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const setFormRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans
const msg = useMessage()
const deptData = ref<any[]>([])

provide('deptData', deptData)

// 请求部门数据
function getDepartment() {
  departmentList().then((res) => {
    deptData.value = res.data.list as any[]
    deptData.value.unshift({ id: undefined, name: '所有部门' })
  })
}

getDepartment()

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '750px',
  // 保存数据
  ok: ({ formType }, okLoadingState: (state: boolean) => void) => {
    okLoadingState(true)
    if (['add', 'edit'].includes(formType)) {
      const elForm = formRef.value.maForm.getElFormRef()
      // 验证通过后
      elForm.validate().then(() => {
        switch (formType) {
          // 新增
          case 'add':
            formRef.value.add().then((res: any) => {
              res.code === ResultCode.SUCCESS ? msg.success(t('crud.createSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
          // 修改
          case 'edit':
            formRef.value.edit().then((res: any) => {
              res.code === 200 ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
        }
      }).catch()
    }
    else {
      const elForm = setFormRef.value.maForm.getElFormRef()
      // 验证通过后
      elForm.validate().then(() => {
        // 设置角色
        setFormRef.value.saveUserRole().then((res: any) => {
          res.code === ResultCode.SUCCESS ? msg.success(t('baseUserManage.setRoleSuccess')) : msg.error(res.message)
          maDialog.close()
        }).catch((err: any) => {
          msg.alertError(err)
        })
      })
    }
    okLoadingState(false)
  },
})

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  header: {
    mainTitle: () => t('baseUserManage.mainTitle'),
    subTitle: () => t('baseUserManage.subTitle'),
  },
  // 表格参数
  tableOptions: {
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
      isFoldBtn: () => t('crud.searchFold'),
      notFoldBtn: () => t('crud.searchUnFold'),
    },
  },
  // 搜索表单参数
  searchFormOptions: { labelWidth: '90px' },
  // 请求配置
  requestOptions: {
    api: page,
  },
  onSearchReset: () => {
    proTableRef.value.setRequestParams({ department_id: undefined }, false)
  },
})
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t),
  // 表格列
  tableColumns: getTableColumns(maDialog, formRef, t),
})

// 批量删除
function handleDelete() {
  const ids = selections.value.map((item: any) => item.id)
  msg.confirm(t('crud.delMessage')).then(async () => {
    const response = await deleteByIds(ids)
    if (response.code === ResultCode.SUCCESS) {
      msg.success(t('crud.delSuccess'))
      await proTableRef.value.refresh()
    }
  })
}

// 请求某部门的用户列表
function requestUserByDept(node: any) {
  proTableRef.value.setRequestParams({ department_id: node.id }, true)
}
</script>

<template>
  <div class="mine-layout flex justify-between pb-0 pl-3 pt-3">
    <div class="w-full rounded bg-[#fff] p-2 md:w-2/12 dark-bg-dark-8">
      <ma-tree
        :data="deptData"
        tree-key="name"
        node-key="id"
        :props="{ label: 'name' }"
        :expand-on-click-node="false"
        @node-click="requestUserByDept"
      >
        <template #default="{ data }">
          <div class="mine-tree-node">
            <div class="label">
              {{ data.name }}
            </div>
          </div>
        </template>
      </ma-tree>
    </div>
    <div class="w-full md:w-10/12">
      <MaProTable ref="proTableRef" :options="options" :schema="schema">
        <template #actions>
          <el-button
            v-auth="['permission:user:save']"
            type="primary"
            @click="() => {
              maDialog.setTitle(t('crud.add'))
              maDialog.open({ formType: 'add' })
            }"
          >
            {{ t('crud.add') }}
          </el-button>
        </template>

        <template #toolbarLeft>
          <el-button
            v-auth="['permission:user:delete']"
            type="danger"
            plain
            :disabled="selections.length < 1"
            @click="handleDelete"
          >
            {{ t('crud.delete') }}
          </el-button>
        </template>
        <!-- 数据为空时 -->
        <template #empty>
          <el-empty>
            <el-button
              v-auth="['permission:user:save']"
              type="primary"
              @click="() => {
                maDialog.setTitle(t('crud.add'))
                maDialog.open({ formType: 'add' })
              }"
            >
              {{ t('crud.add') }}
            </el-button>
          </el-empty>
        </template>
      </MaProTable>
    </div>

    <component :is="maDialog.Dialog">
      <template #default="{ formType, data }">
        <!-- 新增、编辑表单 -->
        <UserForm v-if="formType !== 'setRole'" ref="formRef" :form-type="formType" :data="data" />
        <!-- 赋予角色表单 -->
        <SetRoleForm v-else ref="setFormRef" :data="data" />
      </template>
    </component>
  </div>
</template>

<style scoped lang="scss">

</style>
