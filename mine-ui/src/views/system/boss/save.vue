<template>
  <el-dialog
    :title="titleMap[mode]"
    v-model="visible"
    :width="700"
    destroy-on-close
    append-to-body
    @closed="emits('closed')"
  >
    <el-form
      :model="form"
      :rules="rules"
      ref="dialogForm"
      label-width="80px"
      :style="'el-dialog' === 'el-drawer' ? 'padding:0 20px' : ''"
    >
      
      <el-form-item label="老板名称" prop="name">
        <el-select v-model="form.name" style="width:100%" clearable placeholder="请选择老板名称">
        
          <el-option
            v-for="(item, index) in dictData.upload_mode"
            :key="index" :label="item.label"
            :value="item.value"
          >{{ item.label }}</el-option>

        </el-select>
      </el-form-item>

      <el-form-item label="老板代码" prop="code">
        <el-radio-group v-model="form.code">
          <el-radio label="a" value="a" />
          <el-radio label="b" value="b" />
          <el-radio label="c" value="c" />
        
        </el-radio-group>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-checkbox-group v-model="form.sort">
          <el-checkbox label="aa" value="aa" />
          <el-checkbox label="bb" value="bb" />
        
        </el-checkbox-group>
      </el-form-item>

      <el-form-item label="状态" prop="status">
        <el-rate v-model="form.status" />
      </el-form-item>

        <el-form-item label="创建时间" prop="created_at">
            <el-date-picker
                type="date"
                placeholder="请选择创建时间"
                v-model="form.created_at"
                style="width: 100%;"
            ></el-date-picker>
        </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-slider v-model="form.remark" />
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
    upload_mode: [],
    data_status: [],
    
  })
  const form = reactive({
    id: '',
    name: '',
    code: '',
    sort: [],
    status: '',
    created_at: '',
    updated_at: '',
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
    systemDict.getDict('upload_mode').then(res => {
      dictData.upload_mode = res.data
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
