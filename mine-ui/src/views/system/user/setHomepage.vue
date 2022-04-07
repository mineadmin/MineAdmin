<template>
  <el-dialog
    title="设置用户首页"
    v-model="visible" 
    :width="550"
    append-to-body
    destroy-on-close
    @closed="$emit('closed')"
  >
    <el-form
      :model="form"
      :rules="rules"
      ref="dialogForm"
      label-width="80px"
      v-loading="loading"
      element-loading-background="rgba(255, 255, 255, 0.8)"
      element-loading-text="数据加载中..."
    >

      <el-row :gutter="20">
        <el-col :span="24">
          <el-form-item label="用户首页" prop="dashboard">
            <el-select v-model="form.dashboard" placeholder="请选择用户首页" style="width: 100%" clearable>
              <el-option
                v-for="(item, index) in dashboardList"
                :key="index"
                :label="item.label"
                :value="item.value"
              >
                <span style="float: left">{{ item.label }}</span>
                <span style="float: right; color: #8492a6; font-size: 12px">{{ item.value }}</span>
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>

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
        loading: false,
        visible: false,
        isSaveing: false,
        dashboardList: [],
        //表单数据
        form: {
          id: null,
          dashboard: '',
        },
        //验证规则
        rules: {
          dashboard: [{ required: true, message: '请选择用户首页', trigger: ['blur', 'change'] }],
        }
      }
    },
    methods: {

      //显示
      open(){
        this.visible = true;
        return this
      },

      //表单提交方法
      submit(){
        this.$refs.dialogForm.validate(async (valid) => {
          if (valid) {
            this.isSaveing = true;
            let res = await this.$API.user.setHomePage(this.form)
            this.isSaveing = false;
            if(res.success){
              this.$emit('success', this.form)
              this.visible = false;
              this.$message.success(res.message)
            }else{
              this.$alert(res.message, "提示", { type: 'error' })
            }
          }else{
            return false;
          }
        })

      },

      //表单注入数据
      async setData(data){
        this.loading = true
        this.form.id = data.id
        this.form.dashboard = data.dashboard

        await this.getDict('dashboard').then(res => {
          this.dashboardList = res.data
        })

        this.loading = false

        //可以和上面一样单个注入，也可以像下面一样直接合并进去
        //Object.assign(this.form, data)
      }
    }
  }
</script>

<style>
</style>
