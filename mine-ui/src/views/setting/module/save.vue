<template>
  <el-dialog title="创建新模块" v-model="visible" :width="500" destroy-on-close @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">

      <el-form-item label="模块名称" prop="name">
        <el-input v-model="form.name" size="small" placeholder="请输入模块名称（英文名称）"></el-input>
      </el-form-item>

      <el-form-item label="模块标签" prop="label">
        <el-input v-model="form.label" size="small" placeholder="请输入模块标签（中文名称）"></el-input>
      </el-form-item>

      <el-form-item label="版本号" prop="version">
        <el-input v-model="form.version" size="small" placeholder="请输入版本号"></el-input>
      </el-form-item>

      <el-form-item label="描述" prop="description">
        <el-input v-model="form.description" type="textarea" :rows="3" size="small" placeholder="请输入模块功能描述"></el-input>
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
        form: {
          name: null,
          label: null,
          version: null,
          description: null
        },
        rules: {
          name: [{ required: true, pattern: /^[A-Za-z]{2,}$/g, message: '名称必须是2位以上的英文', trigger: 'blur' }],
          label: [{ required: true, message: '模块标签必填', trigger: 'blur' }],
          version: [{ required: true, pattern: /^[0-9\.]{3,}$/g, message: '版本号必须包含数字和小数点', trigger: 'blur' }],
          description: [{ required: true, message: '模块功能描述必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false
      }
    },
    methods: {

      //显示
      open (){
        this.visible = true
      },

      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = await this.$API.module.save(this.form)
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form, this.mode)
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
