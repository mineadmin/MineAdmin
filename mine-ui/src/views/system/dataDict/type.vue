<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px" label-position="left">
      <el-form-item label="类型名称" prop="name">
        <el-input v-model="form.name" size="small" placeholder="请输入类型名称"></el-input>
      </el-form-item>

      <el-form-item label="类型标识" prop="code">
        <el-input v-model="form.code" size="small" placeholder="请输入类型标识"></el-input>
      </el-form-item>

      <el-form-item label="状态" prop="status" v-if="form.type !== 'B'">
        <el-radio-group v-model="form.status">
          <el-radio label="0">启用</el-radio>
          <el-radio label="1">停用</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-input type="textarea" size="small" :rows="3" placeholder="备注信息" v-model="form.remark">
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
          add: '新增字典类型',
          edit: '编辑字典类型'
        },
        visible: false,
        isSaveing: false,
        form: {
          id: null,
          name: null,
          code: null,
          status: '0',
          remark: null
        },
        rules: {
          name: [{ required: true, message: '请输入类型名称', trigger: 'blur' }],
          code: [{ required: true, message: '请输入类型标识', trigger: 'blur' }]
        }
      }
    },
    mounted() {
      this.getDic()
    },
    methods: {
      //显示
      open(mode='add'){
        this.mode = mode;
        this.visible = true;
        return this;
      },
      //获取字典列表
      async getDic(){
        var res = await this.$API.dic.list.get();
        this.dic = res.data;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res
            if (this.mode == 'add') {
              res = await this.$API.dictType.save(this.form)
            } else {
              res = await this.$API.dictType.update(this.form.id, this.form)
            }
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form, this.mode)
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
        this.form.name = data.name
        this.form.code = data.code
        this.form.status = data.status
        this.form.remark = data.remark

        //可以和上面一样单个注入，也可以像下面一样直接合并进去
        //Object.assign(this.form, data)
      }
    }
  }
</script>

<style>
</style>
