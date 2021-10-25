<template>
  <el-dialog title="新建目录" v-model="visible" :width="500" destroy-on-close @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px" label-position="left">

      <el-form-item label="目录名称" prop="name">
        <el-input v-model="form.name" size="small" placeholder="请输入目录名称"></el-input>
      </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="visible=false" >取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script>
  export default {
    emits: ['success', 'closed'],
    data() {
      return {
        visible: false,
        isSaveing: false,
        form: {
          path: '',
          name: null,
        },
        rules: {
          name: [{ required: true, message: '请输入目录名称', trigger: 'blur' }]
        }
      }
    },
    methods: {
      //显示
      open(node){
        if (node.data && node.data.name) {
          this.form.path = node.data.name
        }
        this.visible = true;
        return this;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = await this.$API.upload.createUploadDir(this.form)
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form)
              this.visible = false;
              this.$message.success(res.message)
            }else{
              this.$alert(res.message, "提示", {type: 'error'})
            }
          }
        })
      }
    }
  }
</script>

<style>
</style>
