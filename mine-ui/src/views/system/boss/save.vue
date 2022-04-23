<template>
  <el-dialog
    :title="titleMap[mode]"
    v-model="visible"
    :width="700"
    destroy-on-close
    append-to-body
    @closed="emits('closed')"
  >
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
      <el-form-item label="老板名称" prop="name">
        <el-input v-model="form.name" clearable placeholder="请输入老板名称" />
      </el-form-item>

      <el-form-item label="老板代码" prop="code">
        <el-radio-group v-model="form.code">
        
          <el-radio
            v-for="(item, index) in dictData.api_data_type"
            :key="index" :label="item.label"
            :value="item.value"
          >{{ item.label }}</el-radio>

        </el-radio-group>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-checkbox-group v-model="form.sort">
        
          <el-checkbox
            v-for="(item, index) in dictData.backend_notice_type"
            :key="index" :label="item.label"
            :value="item.value"
          >{{ item.label }}</el-checkbox>

        </el-checkbox-group>
      </el-form-item>

      <el-form-item label="状态" prop="status">
        <el-select v-model="form.status" style="width:100%" clearable placeholder="请选择状态">
        
          <el-option
            v-for="(item, index) in dictData.data_status"
            :key="index" :label="item.label"
            :value="item.value"
          >{{ item.label }}</el-option>

        </el-select>
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-input v-model="form.remark" type="textarea" rows="3" clearable placeholder="请输入备注" />
      </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="visible = false">取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
  import { ref, reactive, defineEmits, defineExpose, onMounted } from 'vue'
  import { ElMessage } from 'element-plus'
  import editor from '@/components/scEditor'
  import systemBoss from '@/api/apis/system/systemBoss'
  import systemDict from '@/api/apis/system/dataDict'

  const emits = defineEmits(['success', 'closed'])

  const mode = ref('add')
  const treeList = ref([])
  const visible = ref(false)
  const isSaveing = ref(false)
  const dialogForm = ref(null)

  const titleMap = reactive({ add: '新增老板信息', edit: '编辑老板信息' })
  const dictData = reactive({
    api_data_type: [],
    backend_notice_type: [],
    data_status: [],
    
  })
  const form = reactive({
    id: '',
    name: '',
    code: '',
    sort: [],
    status: '',
    remark: '',
    
  })
  const rules = reactive({
    name: [{required: true, message: '老板名称必填', trigger: 'blur' }],
    code: [{required: true, message: '老板代码必填', trigger: 'blur' }],
    sort: [{required: true, message: '排序必填', trigger: 'blur' }],
    status: [{required: true, message: '状态必填', trigger: 'blur' }],
    
  })

  onMounted(async () => {
    await getDictData()
  })

  const getDictData = () => {
    systemDict.getDict('api_data_type').then(res => {
      dictData.api_data_type = res.data
    })
    systemDict.getDict('backend_notice_type').then(res => {
      dictData.backend_notice_type = res.data
    })
    systemDict.getDict('data_status').then(res => {
      dictData.data_status = res.data
    })
    
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
        isSaveing.value = false
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
