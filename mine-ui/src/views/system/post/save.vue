<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      <el-form-item label="岗位名称" prop="name">
        <el-input v-model="form.name"  clearable placeholder="请输入岗位名称"></el-input>
      </el-form-item>

      <el-form-item label="代码" prop="code">
        <el-input v-model="form.code"  clearable placeholder="请输入岗位代码"></el-input>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-input-number v-model="form.sort"  clearable :min="0" :max="999" label="排序"></el-input-number>
      </el-form-item>

      <el-form-item label="状态" prop="status">

        <el-radio-group v-model="form.status">

        <el-radio label="0">启用</el-radio>
        <el-radio label="1">停用</el-radio>

        </el-radio-group>

      </el-form-item>

      <el-form-item label="备注" prop="remark">

        <el-input
        type="textarea"
        
        clearable
        :rows="3"
        placeholder="备注信息"
        v-model="form.remark"
        maxlength="255" show-word-limit
        ></el-input>

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
        mode: "add",
        titleMap: {
          add: '新增岗位',
          edit: '编辑岗位'
        },
        form: {
          id: null,
          name: null,
          code: null,
          status: '0',
          sort: 0,
          remark: null
        },
        rules: {
          name: [{ required: true, message: '请输入岗位名称', trigger: 'blur' }],
              code: [{ required: true, message: '请输入岗位代码', trigger: 'blur' }]
        },
        visible: false,
        isSaveing: false
      }
    },
    methods: {
      //显示
      open(mode='add'){
        this.mode = mode;
        this.visible = true;
        return this;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode == 'add') {
              res = await this.$API.post.save(this.form)
            } else {
              res = await this.$API.post.update(this.form.id, this.form)
            }
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
      },
      //表单注入数据
      setData(data){
        this.form.id = data.id
        this.form.name = data.name
        this.form.code = data.code
        this.form.status = data.status
        this.form.sort = data.sort
        this.form.remark = data.remark
      }
    }
  }
</script>
