<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form
      :model="form"
      :rules="rules"
      ref="dialogForm"
      label-width="80px"
      :disabled="mode=='show'"
      v-loading="loading"
      element-loading-background="rgba(255, 255, 255, 0.8)"
      element-loading-text="数据加载中..."
    >
      <el-form-item label="上级部门" prop="parent_id">
        <el-cascader
        v-model="form.parent_id"
        clearable
        style="width:100%"
        :options="deptList"
        :props="{ checkStrictly: true }"
        ></el-cascader>
      </el-form-item>

      <el-form-item label="部门名称" prop="name">
        <el-input v-model="form.name"  placeholder="请输入部门名称"></el-input>
      </el-form-item>

      <el-form-item label="负责人" prop="leader">
        <el-input v-model="form.leader"  placeholder="请输入部门负责人"></el-input>
      </el-form-item>

      <el-form-item label="联系电话" prop="phone">
        <el-input v-model="form.phone"  placeholder="请输入负责人电话"></el-input>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-input-number v-model="form.sort"  :min="0" :max="999" label="排序"></el-input-number>
      </el-form-item>

      <el-form-item label="状态" prop="status" v-if="form.type !== 'B'">
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
      <el-button type="primary" v-if="mode!='show'" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script>
  export default {
    emits: ['success', 'closed'],
    data() {
      return {
        mode: "add",
        loading: false,
        deptList: [],
        titleMap: {
          add: '新增部门',
          edit: '编辑部门'
        },
        form: {
          id: null,
          parent_id: null,
          name: null,
          leader: '',
          phone: '',
          status: '0',
          sort: 0
        },
        rules: {
          name: [{ required: true, message: '请输入部门名称', trigger: 'blur' }],
          phone: [{ pattern: /^1[3|4|5|6|7|8|9][0-9]\d{8}$/, message: '请输入正确的手机号码', trigger: ['blur'] }]
        },
        visible: false,
        isSaveing: false
      }
    },
    methods: {
      //显示
      open(mode='add'){
        this.mode = mode;
        this.visible = true
        this.loading = true
        this.$API.dept.tree().then(res => {
          this.deptList = res.data
          this.loading = false
        })
        return this
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode == 'add') {
              res = await this.$API.dept.save(this.form)
            } else {
              res = await this.$API.dept.update(this.form.id, this.form)
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
        this.form.parent_id = data.parent_id
        this.form.status = data.status
        this.form.leader = data.leader
        this.form.phone = data.phone
        this.form.sort = data.sort
        this.form.remark = data.remark
      }
    }
  }
</script>

<style>
</style>
