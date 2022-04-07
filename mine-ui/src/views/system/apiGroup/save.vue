<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="500" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      
        <el-form-item label="分组名称" prop="name">
            <el-input v-model="form.name" clearable placeholder="请输入分组名称" />
        </el-form-item>

        <el-form-item label="状态" prop="status">
            <el-radio-group v-model="form.status">
                <el-radio
                    v-for="(item, index) in data_status_data"
                    :key="index"
                    :label="item.value"
                >{{item.label}}</el-radio>
            </el-radio-group>
        </el-form-item>

        <el-form-item label="备注" prop="remark">
            <el-input v-model="form.remark" type="textarea" :rows="3" clearable placeholder="请输入备注" />
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
          add: '新增接口分组',
          edit: '编辑接口分组'
        },
        form: {
          
           id: '',
           name: '',
           status: '0',
           remark: '',
        },
        rules: {
          name: [{required: true, message: '分组名称必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false,
        
        data_status_data: [],
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
              res = await this.$API.apiGroup.save(this.form)
            } else {
              res = await this.$API.apiGroup.update(this.form.id, this.form)
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
          this.form.status = data.status;
          this.form.remark = data.remark;
      },

      // 获取字典数据
      getDictData() {
        
          this.getDict('data_status').then(res => {
              this.data_status_data = res.data
          })
      },

      

      

      
    }
  }
</script>
