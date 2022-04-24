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
        <el-radio-group v-model="form.name">
          <el-radio label="1" value="1" />
          <el-radio label="2" value="2" />
          <el-radio label="3" value="3" />
        
        </el-radio-group>
      </el-form-item>

      <el-form-item label="老板代码" prop="code">
        <editor v-model="form.code" placeholder="请输入老板代码" :height="400" />
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <sc-upload
          v-model="form.sort"
          title="上传排序"
          type="file"
          @success="handlerUploadFileSort"
        />
      </el-form-item>

      <el-form-item label="状态 (0正常 1停用)" prop="status">
        <city-linkage v-model="form.status" valueType="name" />
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-switch v-model="form.remark" active-value="0" inactive-value="1" />
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
  import editor from '@/components/scEditor'
  import cityLinkage from '@/components/maCityLinkage'
  import threeLevelLinkage from '@/components/maCityLinkage/threeLevelLinkage'


  const emits = defineEmits(['success', 'closed'])

  const mode = ref('add')
  const treeList = ref([])
  const visible = ref(false)
  const isSaveing = ref(false)
  const dialogForm = ref(null)

  const titleMap = reactive({ add: '新增老板', edit: '编辑老板' })
  const dictData = reactive({
    
  })
  const form = reactive({
    id: '',
    name: '',
    code: '',
    sort: '',
    status: [],
    remark: '',
    
  })
  const rules = reactive({
    
  })

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

  const numberOperation = (numberName, numberType = 'inc', numberValue = 1) => {
    let data = { id: form.id, numberName, numberType, numberValue }
    systemBoss.numberOperation(data).then( res => {
      res.success && ElMessage.success(res.message)
    }).catch( e => { console.log(e) } )
  }
// {SWITCH_STATUS}


  const handlerUploadFileSort = (res) => {
    if (res.success) {
      form.sort = res.url
    }
  }
  defineExpose({
    open, setData
  })
</script>
