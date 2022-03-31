<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
      <el-form-item label="字典标签" prop="label">
        <el-input v-model="form.label"  placeholder="请输入字典标签"></el-input>
      </el-form-item>

      <el-form-item label="字典值" prop="value">
        <el-input v-model="form.value"  placeholder="请输入字典值"></el-input>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-input-number v-model="form.sort"  :min="0" :max="999" label="排序"></el-input-number>
      </el-form-item>

      <el-form-item label="状态" prop="status">
        <el-radio-group v-model="form.status">

          <el-radio label="0">启用</el-radio>
          <el-radio label="1">停用</el-radio>

        </el-radio-group>
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-input type="textarea"  :rows="3" placeholder="备注信息" v-model="form.remark">
        </el-input>
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
          add: '新增字典项',
          edit: '编辑字典项'
        },
        visible: false,
        isSaveing: false,
        form: {
          id: null,
          label: null,
          value: null,
          type_id: null,
          code: null,
          sort: 0,
          status: '0',
          remark: null
        },
        rules: {
          label: [{ required: true, message: '请输入字典标签', trigger: 'blur' }],
          value: [{ required: true, message: '请输入字典值', trigger: 'blur' }]
        }
      }
    },
    methods: {

      //显示
      open(mode='add', data = {}){
        this.mode = mode
        this.form.type_id = data.id
        this.form.code = data.code
        this.visible = true
        return this
      },

      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res
            if (this.mode == 'add') {
              res = await this.$API.dataDict.saveDictData(this.form)
            } else {
              res = await this.$API.dataDict.updateDictData(this.form.id, this.form)
            }
            this.isSaveing = false;
            if(res.success){
              this.$emit('success')
              this.visible = false;
              this.$message.success("操作成功")
            }else{
              this.$alert(res.message, "提示", {type: 'error'})
            }
          }
        })
      },

      //表单注入数据
      setData(data){
        this.form.id = data.id
        this.form.label = data.label
        this.form.value = data.value
        this.form.type_id = data.type_id
        this.form.code = data.code
        this.form.sort = data.sort
        this.form.status = data.status
        this.form.remark = data.remark
      }
    }
  }
</script>

<style>
</style>
