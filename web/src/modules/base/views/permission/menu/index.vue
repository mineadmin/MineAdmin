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
import type { ElForm } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import { create, page, save } from '~/base/api/menu.ts'
import type { MenuVo } from '~/base/api/menu.ts'

import MenuTree from './menu-tree.vue'
import MenuForm from './menu-form.vue'
import { ResultCode } from '@/utils/ResultCode.ts'

defineOptions({ name: 'permission:menu' })

const menuList = ref<MenuVo[]>([])
const defaultExpandNodes = ref<number[]>([])
const currentMenu = ref<MenuVo | null>(null)
const menuFormRef = ref()
const menuTreeRef = ref()
const newMenu = ref<MenuVo>({
  id: undefined,
  parent_id: undefined,
  name: '',
  path: '',
  meta: {
    title: '新增顶级菜单',
    type: 'M',
    componentSuffix: '.vue',
    componentPath: 'modules/',
    breadcrumbEnable: true,
    copyright: true,
    hidden: false,
    affix: false,
    cache: true,
    activeName: '',
    auth: [],
    role: [],
    user: [],
  },
  component: '',
  sort: 0,
  status: 1,
  btnPermission: [],
})

const loading = ref<boolean>(false)
const t = useTrans().globalTrans
const msg = useMessage()

async function getMenu() {
  const { data } = await page()
  menuList.value = data
  menuList.value.map((item: MenuVo) => setNodeExpand(item.id as number))
}

// 设置某个节点展开
function setNodeExpand(id: number, state: boolean = true) {
  const elTree = menuTreeRef.value.treeRef.elTree
  if (elTree) {
    const treeNode = elTree.store!._getAllNodes()?.find((node: any) => id === node.data.id)
    if (treeNode) {
      treeNode.expanded = state
    }
  }
}

onMounted(async () => {
  await getMenu()
})

provide('newMenu', newMenu)
provide('menuList', menuList)
provide('defaultExpandNodes', defaultExpandNodes)
provide('setNodeExpand', setNodeExpand)

function resetForm() {
  currentMenu.value = Object.assign(newMenu.value, {})
  currentMenu.value!.meta!.title = ''
  menuFormRef.value?.setData?.(currentMenu.value)
  menuFormRef.value?.menuForm?.getElFormRef().resetFields()
}

function createOrSaveMenu() {
  const { model } = menuFormRef.value
  const { getElFormRef } = menuFormRef.value.menuForm
  const elForm = getElFormRef() as typeof ElForm
  loading.value = true
  elForm.validate().then(() => {
    if (!model.parent_id) {
      model.parent_id = 0
    }
    if (model.dataType && model.dataType === 'add') {
      create(model).then(async (res: any) => {
        res.code === ResultCode.SUCCESS ? msg.success(t('crud.createSuccess')) : msg.error(res.message)
        await getMenu()
        resetForm()
        setNodeExpand(model.parent_id as number)
      })
    }
    else if (currentMenu.value !== null && model.dataType === 'edit' && model.id) {
      save(model.id as number, model).then(async (res: any) => {
        res.code === ResultCode.SUCCESS ? msg.success(t('crud.updateSuccess')) : msg.error(res.message)
        await getMenu()
        setNodeExpand(model.id as number)
      })
    }
    else {
      msg.alertError(t('baseMenuManage.addError'))
    }
    loading.value = false
  }).catch((err: any) => {
    if (Object.keys(err)?.[0]) {
      // 跳转到未填写字段
      elForm.scrollToField(Object.keys(err)?.[0])
    }
    loading.value = false
  })
}
</script>

<template>
  <div
    v-loading="loading"
    class="mine-card menu-container h-full gap-x-4.5 lg:flex"
    :style="{ height: `${getOnlyWorkAreaHeight() + 12}px` }"
  >
    <div class="relative w-full overflow-hidden b-r-1 b-r-gray-2 b-r-solid pr-5 lg:w-4/12 dark-b-r-dark-3">
      <MenuTree
        ref="menuTreeRef"
        :data="menuList"
        @menu-select="(menu: MenuVo) => {
          currentMenu = menu
          menuFormRef?.setData?.(menu)
        }"
      />
    </div>
    <div class="relative mt-3 h-[calc(100%-250px)] w-full overflow-x-hidden overflow-y-auto pr-5 lg:mt-0 lg:h-full">
      <div class="sticky top-0 z-2 flex items-center justify-between b-b-1 b-b-gray-1 b-b-solid bg-white pb-3 text-base dark-b-b-dark-4 dark-bg-dark-8">
        <span>{{ currentMenu ? (currentMenu.meta?.i18n ? t(currentMenu.meta?.i18n) : currentMenu.meta?.title) : t('baseMenuManage.addTopMenu') }}</span>
        <div>
          <el-button type="primary" @click="createOrSaveMenu">
            {{ t('crud.save') }}
          </el-button>
        </div>
      </div>
      <MenuForm ref="menuFormRef" />
    </div>
  </div>
</template>
