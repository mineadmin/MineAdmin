<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="1000" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="100px">
      
        <el-form-item label="标题" prop="title">
            <el-input v-model="form.title" clearable placeholder="请输入标题" />
        </el-form-item>

        <el-form-item label="公告类型" prop="type">
            <el-select v-model="form.type" style="width:100%" clearable placeholder="请选择公告类型">
                <el-option
                    v-for="(item, index) in backend_notice_type_data"
                    :key="index" :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="接收人员" prop="users" v-if="mode === 'add'">
            <ma-select-user v-model="form.users" />
            <div class="el-form-item-msg">不选择则为所有人发送</div>
        </el-form-item>

        <el-form-item label="公告内容" prop="content">
          <editor v-model="form.content" clearable placeholder="请输入公告内容" />
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
          add: '新增系统公告',
          edit: '编辑系统公告'
        },
        form: {
           id: '',
           title: '',
           type: '',
           users: [],
           content: '',
           remark: '',
        },
        rules: {
          
           title: [{required: true, message: '标题必填', trigger: 'blur' }],
           type: [{required: true, message: '公告类型', trigger: 'blur' }],
           content: [{required: true, message: '公告内容必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false,
        
        backend_notice_type_data: [],
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
              res = await this.$API.notice.save(this.form)
            } else {
              res = await this.$API.notice.update(this.form.id, this.form)
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
          this.form.title = data.title;
          this.form.type = data.type;
          this.form.content = data.content;
          this.form.remark = data.remark;
      },

      // 获取字典数据
      getDictData() {
        
          this.getDict('backend_notice_type').then(res => {
              this.backend_notice_type_data = res.data
          })
      },
    }
  }
</script>
