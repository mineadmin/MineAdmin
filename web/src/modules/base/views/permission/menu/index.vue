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
import MenuTree from './menu-tree.vue'
import MenuForm from './menu-form.vue'
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'
import { type MenuVo, page } from '~/base/api/menu.ts'

defineOptions({ name: 'permission:menu' })

const menuList = ref<MenuVo[]>([])
const defaultExpandNodes = ref<number[]>([])

onMounted(async () => {
  const { data } = await page()
  menuList.value = data
  menuList.value.map(item => defaultExpandNodes.value.push(item.id as number))
})

provide('menuList', menuList)
provide('defaultExpandNodes', defaultExpandNodes)
</script>

<template>
  <div
    class="mine-card menu-container gap-x-4.5 lg:flex"
    :style="{ height: `${getOnlyWorkAreaHeight()}px` }"
  >
    <div class="relative w-full overflow-hidden b-r-1 b-r-gray-2 b-r-solid pr-5 lg:w-4/12 dark-b-r-dark-3">
      <MenuTree :data="menuList" :default-expanded-keys="defaultExpandNodes" />
    </div>
    <div class="mt-3 h-[calc(100%-250px)] w-full overflow-x-hidden overflow-y-auto pr-5 lg:mt-0 lg:h-full">
      <div class="text-base">
        添加顶级菜单
      </div>

      <MenuForm />
    </div>
  </div>
</template>
