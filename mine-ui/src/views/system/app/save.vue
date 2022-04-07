<template>
  <el-dialog :title="titleMap[mode]" v-model="visible" :width="700" destroy-on-close append-to-body @closed="$emit('closed')">
      <el-form :model="form" :rules="rules" ref="dialogForm" label-width="110px">
        <el-tabs v-model="activeName">
          <el-tab-pane label="基础信息" name="base">

            <el-form-item label="应用分组" prop="group_id">
                <el-select v-model="form.group_id" style="width:100%" filterable clearable placeholder="请选择应用分组">
                    <el-option v-for="(item, index) in groupData" :key="index" :value="item.id" :label="item.name" />
                </el-select>
            </el-form-item>

            <el-form-item label="应用名称" prop="app_name">
                <el-input v-model="form.app_name" clearable placeholder="请输入应用名称" />
            </el-form-item>

            <el-form-item label="APP ID" prop="app_id">
                <el-input v-model="form.app_id" clearable :disabled="true" placeholder="请输入APP ID">
                  <template #append v-if="mode === 'add'">
                    <el-button type="primary" icon="el-icon-refresh" @click="setAppid()">刷新APP ID</el-button>
                  </template>
                </el-input>
            </el-form-item>

            <el-form-item label="APP SECRET" prop="app_secret">
                <el-input v-model="form.app_secret" clearable :disabled="true" placeholder="请输入APP SECRET">
                  <template #append>
                    <el-button type="primary" icon="el-icon-refresh" @click="setAppsecret()">刷新APP SECRET</el-button>
                  </template>
                </el-input>
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
            <el-form-item label="应用介绍" prop="description">
                <editor v-model="form.description" placeholder="请输入应用介绍" :height="260"></editor>
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

  export default {
    emits: ['success', 'closed'],
    components: {
      editor
    },
    data() {
      return {
        mode: "add",
        activeName: 'base',
        titleMap: {
          add: '新增应用',
          edit: '编辑应用'
        },
        form: {

           id: '',
           group_id: '',
           app_name: '',
           app_id: '',
           app_secret: '',
           status: '0',
           description: '',
           remark: '',
        },
        rules: {

           group_id: [{required: true, message: '应用分组必填', trigger: 'blur' }],
           app_name: [{required: true, message: '应用名称必填', trigger: 'blur' }],
           app_id: [{required: true, message: 'APP ID必填', trigger: 'blur' }],
           app_secret: [{required: true, message: 'APP SECRET必填', trigger: 'blur' }],
        },
        visible: false,
        isSaveing: false,

        data_status_data: [],
        groupData: [],
      }
    },
    methods: {
      //显示
      async open(mode='add'){
        await this.getDictData()
        await this.getGroupData()
        this.mode = mode;
        this.visible = true;
        if (mode === 'add') {
          this.setAppid()
          this.setAppsecret()
        }
        return this;
      },
      // 设置appid
      async setAppid() {
          let appid = await this.$API.app.getAppId()
          this.form.app_id = appid.data.app_id
      },
      async setAppsecret() {
      	if( this.mode === 'add' ){
					let appsecret = await this.$API.app.getAppSecret()
					this.form.app_secret = appsecret.data.app_secret
				} else {
					this.$prompt('若要继续,请在下方输入"yes"', '警告!该操作会导致已经在运行中的应用失效', {
						confirmButtonText: '确定',
						cancelButtonText: '取消',
						inputPattern:
							/yes/,
						inputErrorMessage: '输入有误',
					}).then( async () => {
						let appsecret = await this.$API.app.getAppSecret()
						this.form.app_secret = appsecret.data.app_secret
						this.$message.success('appSecret重置成功,请点击下方"保存"按钮使其生效')
					})
				}


      },
      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = null
            if (this.mode == 'add') {
              res = await this.$API.app.save(this.form)
            } else {
              res = await this.$API.app.update(this.form.id, this.form)
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
          this.form.app_name = data.app_name;
          this.form.app_id = data.app_id;
          this.form.app_secret = data.app_secret;
          this.form.status = data.status;
          this.form.description = data.description;
          this.form.remark = data.remark;
      },

      // 获取字典数据
      getDictData() {
          this.getDict('data_status').then(res => {
              this.data_status_data = res.data
          })
      },

      // 获取组列表
      getGroupData() {
        this.$API.appGroup.getSelectList().then(res => {
          if (res.success) {
            this.groupData = res.data
          }
        })
      }

    }
  }
</script>
