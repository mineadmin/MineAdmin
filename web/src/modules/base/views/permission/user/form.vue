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
import getFormItems from './data/getFormItems.tsx'
import type { MaFormExpose } from '@mineadmin/form'

defineOptions({ name: 'permission:user:form' })

const t = useTrans()
const isOpen = ref<boolean>(false)
const userForm = ref<MaFormExpose>()
const componentInfo = reactive({
  title: '',
  type: 'add',
})

function open(data: UserVo | null = null) {
  componentInfo.title = data ? t('crud.edit') : t('crud.add')
  componentInfo.type = data ? 'edit' : 'add'
  isOpen.value = true
  nextTick(
    () => userForm.value?.setItems(getFormItems(componentInfo.type as 'add' | 'edit', t)),
  )
}

const userModel = ref<UserVo>({
  avatar: ['http://127.0.0.1:9501/uploads/2024-10-01/594a88c3-35df-4fc5-ac5b-030a2d4274d1.jpg'],
})

defineExpose({ open })
</script>

<template>
  <ma-dialog
    v-model="isOpen"
    :title="componentInfo.title"
    :close-on-click-modal="false"
    append-to-body
  >
    {{ userModel }}
    <ma-form ref="userForm" v-model="userModel" />
  </ma-dialog>
</template>

<style scoped lang="scss">

</style>
