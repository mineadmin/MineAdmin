<template>
  <el-dialog v-model="visible" title="发消息" :width="800" destroy-on-close append-to-body> 
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">

      <el-form-item label="消息标题" prop="title">
        <el-input v-model="form.title"  clearable placeholder="请输入消息标题"></el-input>
      </el-form-item>

      <el-form-item label="接收人员" prop="users">
        <ma-select-user v-model="form.users" />
      </el-form-item>

      <el-form-item label="消息内容" prop="content">
        <editor v-model="form.content" clearable placeholder="请输入消息内容" />
      </el-form-item>
      
    </el-form>
    <template #footer>
      <el-button @click="visible=false" >取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script>
import editor from '@/components/scEditor'
export default {
  components: {
    editor
  },
  data() {
    return {
      visible: false,
      isSaveing: false,
      form: {
        title: '',
        users: [],
        content: ''
      },
      rules: {
        title: [{required: true, message: '消息必填', trigger: 'blur' }],
        users: [{required: true, message: '接收人员必选', trigger: 'blur' }],
        content: [{required: true, message: '消息内容必填', trigger: 'blur' }],
      },
    }
  },

  methods: {
    open() {
      this.visible = true
    },

    submit() {
      this.$refs.dialogForm.validate(async (valid) => {
        if (valid) {
          this.isSaveing = true
          this.$API.queueMessage.sendPrivateMessage(this.form).then(res => {
            res.success && this.$message.success(res.message)
            res.success || this.$message.error(res.message)
            this.isSaveing = false
            this.visible = false
          })
        }
      })
    }
  }
}
</script>