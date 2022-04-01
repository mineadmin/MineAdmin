<template>
  <el-dialog
    append-to-body
    :title="titleMap[mode]"
    v-model="visible"
    :width="800"
    destroy-on-close
    @closed="$emit('closed')"
  >
    <el-form :model="form" :rules="rules" ref="dialogForm" label-width="80px">
      <el-tabs v-model="activeName">

        <el-tab-pane label="基础信息" name="base">

          <el-form-item label="接口分组" prop="group_id">
              <el-select v-model="form.group_id" style="width:100%" filterable clearable placeholder="请选择接口分组">
                  <el-option v-for="(item, index) in groupData" :key="index" :value="item.id" :label="item.name" />
              </el-select>
          </el-form-item>

          <el-form-item label="接口名称" prop="name">
              <el-input v-model="form.name" clearable placeholder="请输入接口名称" />
          </el-form-item>

					<el-form-item label="访问名称" prop="access_name">
						<el-input v-model="form.access_name" clearable placeholder="请输入接口访问名称" />
					</el-form-item>

          <el-form-item label="类名称" prop="class_name">
              <el-autocomplete
              v-model="form.class_name"
              :fetch-suggestions="querySearch"
              clearable style="width:100%"
              placeholder="请输入类名称，包括命名空间" />
          </el-form-item>

          <el-form-item label="方法名" prop="method_name">
              <el-input v-model="form.method_name" clearable placeholder="请输入方法名" />
          </el-form-item>

          <el-form-item label="认证模式" prop="auth_mode">
              <el-radio-group v-model="form.auth_mode">
                  <el-radio label="0">简易模式</el-radio>
                  <el-radio label="1">复杂模式</el-radio>
              </el-radio-group>
          </el-form-item>

          <el-form-item label="请求模式" prop="request_mode">
              <el-select v-model="form.request_mode" style="width:100%" clearable placeholder="请选择请求模式">
                  <el-option
                      v-for="(item, index) in request_mode_data"
                      :key="index" :label="item.label"
                      :value="item.value"
                  >{{item.label}}</el-option>
              </el-select>
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

        </el-tab-pane>

        <el-tab-pane label="其他信息" name="other">

          <el-form-item label="说明介绍" prop="description">
              <editor v-model="form.description" placeholder="请输入说明介绍" :height="300"></editor>
          </el-form-item>

          <el-form-item label="返回示例" prop="response">
              <ma-json-editor v-model="form.response" />
          </el-form-item>

          <el-form-item label="备注" prop="remark">
              <el-input v-model="form.remark" type="textarea" :rows="3" clearable placeholder="请输入备注" />
          </el-form-item>

        </el-tab-pane>

      </el-tabs>

    </el-form>
    <template #footer>
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
        activeName: 'base',
        titleMap: {
          add: '新增接口',
          edit: '编辑接口'
        },
        form: {
           id: '',
           group_id: '',
           name: '',
					 access_name: '',
           class_name: '',
           method_name: '',
           auth_mode: '0',
           request_mode: 'A',
           description: '',
           response: `{
  code: 200,
  success: true,
  message: '请求成功',
  data: []
}`,
           status: '0',
           remark: '',
        },
        rules: {
          group_id: [{required: true, message: '接口分组必选', trigger: 'change' }],
          name: [{required: true, message: '接口名称必填', trigger: 'blur' }],
					access_name: [{required: true, message: '访问名称必填', trigger: 'blur' }],
          class_name: [{required: true, message: '类名称必填', trigger: 'blur' }],
          method_name: [{required: true, message: '方法名必填', trigger: 'blur' }],
          auth_mode: [{required: true, message: '认证模式必填', trigger: 'blur' }],
          request_mode: [{required: true, message: '请求模式必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false,

        request_mode_data: [],
        data_status_data: [],

        groupData: [],

        modules: [],
      }
    },
    methods: {
      //显示
      async open(mode='add'){
        await this.getDictData()
        await this.getGroupData()
        await this.getModuleList()
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
              res = await this.$API.api.save(this.form)
            } else {
              res = await this.$API.api.update(this.form.id, this.form)
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
          this.form.group_id = data.group_id;
          this.form.name = data.name;
          this.form.access_name = data.access_name;
          this.form.class_name = data.class_name;
          this.form.method_name = data.method_name;
          this.form.auth_mode = data.auth_mode;
          this.form.request_mode = data.request_mode;
          this.form.description = data.description;
          this.form.response = data.response;
          this.form.status = data.status;
          this.form.remark = data.remark;
      },

      // 获取字典数据
      getDictData() {

          this.getDict('request_mode').then(res => {
              this.request_mode_data = res.data
          })
          this.getDict('data_status').then(res => {
              this.data_status_data = res.data
          })
      },

      getModuleList() {
        this.$API.api.getModuleList().then(res => {
          if (res.success) {
            this.modules = res.data
          }
        })
      },

      // 获取组列表
      getGroupData() {
        this.$API.apiGroup.getSelectList().then(res => {
          if (res.success) {
            this.groupData = res.data
          }
        })
      },

      // 模块命名空间过滤
      querySearch(queryString, cb){
        let modules = []
        Object.keys(this.modules).forEach( item => {
          if (item.indexOf(queryString) !== -1) {
            modules.push({ 'value': `Api\\InterfaceApi\\v1\\${item}` })
          }
        })
        cb(modules)
      }

    }
  }
</script>
