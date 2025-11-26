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

import { deleteByIds, page } from '~/base/api/department.ts'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

import DepartmentForm from './form.vue'
import Position from './position.vue'
import SetLeader from './setLeader.vue'
import ViewUser from './viewUser.vue'

defineOptions({ name: 'permission:department' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const setLeaderRef = ref()
const positionRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans
const msg = useMessage()
const states = ref<Record<string, boolean>>({
  isExpand: false,
})

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '550px',
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
    else if (formType === 'position' || formType === 'viewUser') {
      proTableRef.value.refresh()
      maDialog.close()
    }
    else {
      proTableRef.value.refresh()
      maDialog.close()
    }
    okLoadingState(false)
  },
})

// 参数配置
const options = ref<MaProTableOptions>({
  // 表格距离底部的像素偏移适配
  adaptionOffsetBottom: 161,
  header: {
    mainTitle: () => t('baseDepartment.page.mainTitle'),
    subTitle: () => t('baseDepartment.page.subTitle'),
  },
  // 表格参数
  tableOptions: {
    rowKey: 'id',
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
})
// 架构配置
const schema = ref<MaProTableSchema>({
  // 搜索项
  searchItems: getSearchItems(t),
  // 表格列
  tableColumns: getTableColumns(maDialog, formRef, t, msg),
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

function expandToggle() {
  states.value.isExpand = !states.value.isExpand
  const tableStates = proTableRef.value?.getElTableStates?.()
  Object.keys(tableStates.treeData.value).map((key: string) => {
    tableStates.treeData.value[key]!.expanded = states.value.isExpand
  })
}
</script>

<template>
  <div class="mine-layout pt-3">
    <MaProTable ref="proTableRef" :options="options" :schema="schema">
      <template #actions>
        <el-button
          v-auth="['permission:department:save']"
          type="primary"
          @click="() => {
            maDialog.setTitle(t('crud.add'))
            maDialog.setAttr({ width: '550px' })
            maDialog.open({ formType: 'add' })
          }"
        >
          {{ t('crud.add') }}
        </el-button>
      </template>

      <template #toolbarLeft>
        <el-button
          v-auth="['permission:department:delete']"
          type="danger"
          plain
          :disabled="selections.length < 1"
          @click="handleDelete"
        >
          {{ t('crud.delete') }}
        </el-button>
        <div>
          <el-button @click="expandToggle">
            {{ t('crud.searchUnFold') }}
          </el-button>
        </div>
      </template>
      <!-- 数据为空时 -->
      <template #empty>
        <el-empty>
          <el-button
            v-auth="['permission:department:save']"
            type="primary"
            @click="() => {
              maDialog.setTitle(t('crud.add'))
              maDialog.setAttr({ width: '550px' })
              maDialog.open({ formType: 'add' })
            }"
          >
            {{ t('crud.add') }}
          </el-button>
        </el-empty>
      </template>
    </MaProTable>

    <component :is="maDialog.Dialog">
      <template #default="{ formType, data }">
        <!-- 新增、编辑表单 -->
        <DepartmentForm v-if="['add', 'edit'].includes(formType)" ref="formRef" :form-type="formType" :data="data" />
        <!-- 设置部门领导 -->
        <SetLeader v-if="formType === 'setLeader'" ref="setLeaderRef" :data="data" />
        <!-- 管理部门岗位 -->
        <Position v-if="formType === 'position'" ref="positionRef" :data="data" />
        <!-- 查看用户 -->
        <ViewUser v-if="formType === 'viewUser'" :data="data" />
      </template>
    </component>
  </div>
</template>

<style scoped lang="scss">

</style>
