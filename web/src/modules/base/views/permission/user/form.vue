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

defineOptions({ name: 'permission:user:form' })

const isOpen = ref<boolean>(false)
const componentInfo = reactive({
  title: '',
  width: '50%',
})

function open(data: UserVo | null = null) {
  componentInfo.title = data ? '编辑' : '新增'
  isOpen.value = true
}

const userModel = ref<UserVo>({})

defineExpose({ open })
</script>

<template>
  <ma-dialog
    v-model="isOpen"
    :title="componentInfo.title"
    :width="componentInfo.width"
    :close-on-click-modal="false"
    append-to-body
  >
    <ma-form v-model="userModel" />
  </ma-dialog>
</template>

<style scoped lang="scss">

</style>
