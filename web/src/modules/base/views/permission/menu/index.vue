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
import { useResizeObserver } from '@vueuse/core'
import { type MenuVo, page } from '~/base/api/menu'
import getOnlyWorkAreaHeight from '@/utils/getOnlyWorkAreaHeight.ts'

defineOptions({ name: 'permission:menu' })

const t = useTrans()

const menuList = ref<MenuVo[]>([])

onMounted(async () => {
  const { data } = await page()
  menuList.value = data

  useResizeObserver(document.body, () => {
    document.querySelector('.menu-container').style.height = `${getOnlyWorkAreaHeight()}px`
  })
})
</script>

<template>
  <div class="mine-card menu-container gap-x-5 lg:flex">
    <div class="w-full ring-1 lg:w-3/12">
      <el-tree-v2
        :data="menuList"
      >
        <template #default="{ data }">
          <span>{{ data.meta?.i18n ? t(data.meta?.i18n) : data.meta.title ?? 'unknown' }}</span>
        </template>
      </el-tree-v2>
    </div>
    <div class="w-full ring-1 lg:w-5/12">
      <div class="text-base">
        菜单管理
      </div>
    </div>
  </div>
</template>
