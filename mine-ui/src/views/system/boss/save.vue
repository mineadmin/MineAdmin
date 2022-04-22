<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
        <el-form-item label="老板名称" prop="name">
            <el-input v-model="form.name" clearable placeholder="请输入老板名称" />
        </el-form-item>

        <el-form-item label="老板代码" prop="code">
            <el-input v-model="form.code" clearable placeholder="请输入老板代码" />
        </el-form-item>

        <el-form-item label="排序" prop="sort">
            <el-input v-model="form.sort" clearable placeholder="请输入排序" />
        </el-form-item>

        <el-form-item label="状态 (0正常 1停用)" prop="status">
            <el-input v-model="form.status" clearable placeholder="请输入状态 (0正常 1停用)" />
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
    emits: ['success', 'closed'],
    components: {
      editor
    },
    data() {
      return {
        mode: "add",
        titleMap: {
          add: '新增老板',
          edit: '编辑老板'
        },
        treeList: [],
        form: {
          
           id: '',
           name: '',
           code: '',
           sort: '',
           status: '',
        },
        rules: {
          
        },
        visible: false,
        isSaveing: false,
        
      }
    },
    async created() {
        await this.getDictData();
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
              res = await this.$API.systemBoss.save(this.form)
            } else {
              res = await this.$API.systemBoss.update(this.form.id, this.form)
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
        
          this.form.id = data.id;
          this.form.name = data.name;
          this.form.code = data.code;
          this.form.sort = data.sort;
          this.form.status = data.status;
      },

      // 获取字典数据
      getDictData() {
        
      },

      

      
    }
  }
</script>
