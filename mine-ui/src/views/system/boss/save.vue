<template>
  <el-dialog
    :title="titleMap[mode]"
    v-model="visible"
    :width="700"
    destroy-on-close
    append-to-body
    @closed="$emit('closed')"
  >
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      <el-form-item label="老板名称" prop="name">
        <el-input v-model="form.name" clearable placeholder="请输入老板名称" />
      </el-form-item>

      <el-form-item label="老板代码" prop="code">
        <el-input v-model="form.code" clearable placeholder="请输入老板代码" />
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-input v-model="form.sort" clearable placeholder="请输入排序" />
      </el-form-item>

      <el-form-item label="状态 (0正常 1停用)" prop="status">
        <el-input
          v-model="form.status"
          clearable
          placeholder="请输入状态 (0正常 1停用)"
        />
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button @click="visible = false">取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()"
        >保 存</el-button
      >
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, reactive, defineEmits, defineExpose, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import editor from '@/components/scEditor'
import systemBoss from '@/api/apis/system/systemBoss'

const emits = defineEmits(['success', 'closed'])

const mode = ref('add')
const treeList = ref([])
const visible = ref(false)
const isSaveing = ref(false)
const dialogForm = ref(null)

const titleMap = reactive({ add: '新增老板', edit: '编辑老板' })
const form = reactive({
  id: '',
  name: '',
  code: '',
  sort: '',
  status: ''
})
const rules = reactive({})

onMounted(async () => {
  await getDictData()
})

const getDictData = () => {

}

const open = (type = 'add') => {
  mode.value = type
  visible.value = true
}

const submit = () => {
  dialogForm.value.validate(async (valid) => {
    if (valid) {
      isSaveing.value = true
      const res = mode.value === 'add' ? await systemBoss.save(form) : await systemBoss.update(form.id, form)
      if (res.success) {
        emits('success', form, mode.value)
        visible.value = false
        ElMessage.success(res.message)
      } else {
        ElMessage.error(res.message)
      }
    }
  })
}

const setData = (data) => {
  for (let k in form) {
    if (data[k] || data[k] === 0) {
      form[k] = data[k]
    }
  }
}

defineExpose({
  open, setData
})
</script>
