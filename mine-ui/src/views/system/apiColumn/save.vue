<template>
  <el-dialog :title="titleMap[mode] + titleSuffix" v-model="visible" :width="600" destroy-on-close append-to-body @closed="$emit('closed')">
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">

        <el-form-item label="字段名称" prop="name">
            <el-input v-model="form.name" clearable :disabled="mode === 'show'" placeholder="请输入字段名称" />
        </el-form-item>

        <el-form-item label="数据类型" prop="data_type">
					<el-select v-model="form.data_type" style="width:100%" :disabled="mode === 'show'" clearable placeholder="请选择数据类型">
						<el-option
							v-for="(item, index) in api_data_type"
							:key="index" :label="item.label"
							:value="item.value"
						>{{item.label}}</el-option>
					</el-select>
        </el-form-item>

        <el-form-item label="是否必填" prop="is_required" v-if="form.type === '0'">
            <el-radio-group v-model="form.is_required" :disabled="mode === 'show'">
                <el-radio label="0">是</el-radio>
                <el-radio label="1">否</el-radio>
            </el-radio-group>
        </el-form-item>

        <el-form-item label="默认值" prop="default_value" v-if="form.is_required === '1' && form.type === '0'">
            <el-input v-model="form.default_value" :disabled="mode === 'show'" clearable placeholder="请输入默认值" />
        </el-form-item>

        <el-form-item label="字段说明" prop="description" v-if="form.type === '0'">
            <editor v-model="form.description" :disabled="mode === 'show'" clearable placeholder="请输入字段说明"/>
        </el-form-item>

        <el-form-item label="返回示例" prop="description" v-if="form.type === '1'">
          <ma-json-editor v-model="form.description" />
        </el-form-item>

				<el-form-item label="状态" prop="status">
					<el-radio-group v-model="form.status" :disabled="mode === 'show'">
						<el-radio
							v-for="(item, index) in data_status_data"
							:key="index" :label="item.value"
						>{{item.label}}</el-radio>
					</el-radio-group>
				</el-form-item>

        <el-form-item label="备注" prop="remark">
            <el-input v-model="form.remark" type="textarea" :disabled="mode === 'show'" :rows="3" clearable placeholder="请输入备注" />
        </el-form-item>

    </el-form>
    <template #footer v-if="mode !== 'show'">
      <el-button @click="visible=false" >取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()">保 存</el-button>
    </template>
  </el-dialog>
</template>

<script>
  import editor from '@/components/scEditor'
  import maJsonEditor from '@/components/maJsonEditor'

  export default {
    emits: ['success', 'closed'],
    components: {
      editor,
      maJsonEditor
    },
    data() {
      return {
        mode: "add",
				titleSuffix: '',
        titleMap: {
          add: '新增接口',
          edit: '编辑接口',
          show: '查看接口',
        },
        form: {
           id: '',
           api_id: '',
           name: '',
           data_type: '',
           is_required: '0',
           default_value: '',
           status: '0',
           description: '',
           remark: '',
					 type: ''
        },
        rules: {
           name: [{required: true, message: '字段名称必填', trigger: 'blur' }],
           data_type: [{required: true, message: '数据类型必填', trigger: 'blur' }],
           is_required: [{required: true, message: '是否必填必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false,

        data_status_data: [],
				api_data_type: [],
      }
    },
    async created() {
        await this.getDictData();
    },
    methods: {
      //显示
      open(mode='add', type='request', apiId){
        this.mode = mode;
        this.visible = true;
        this.form.type = (type === 'request') ? '0' : '1'
        this.form.api_id = apiId
				this.titleSuffix += (type === 'request') ? '请求字段' : '响应字段'
        return this;
      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode === 'add') {
              res = await this.$API.apiColumn.save(this.form)
            } else {
              res = await this.$API.apiColumn.update(this.form.id, this.form)
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
          this.form.api_id = data.api_id;
          this.form.name = data.name;
          this.form.data_type = data.data_type;
          this.form.is_required = data.is_required;
          this.form.default_value = data.default_value;
          this.form.status = data.status;
          this.form.description = data.description;
          this.form.remark = data.remark;
      },

      // 获取字典数据
      getDictData() {

				this.getDict('data_status').then(res => {
						this.data_status_data = res.data
				})

				this.getDict('api_data_type').then(res => {
					this.api_data_type = res.data
				})
      }
    }
  }
</script>
