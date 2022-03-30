<template>

  <el-drawer
    title="修改生成配置"
    v-model="drawer"
    size="80%"
  >
    <el-form
      ref="form"
      :model="form"
      :rules="rules"
      label-width="110px"
      class="form"
    >
      <el-tabs v-model="activeName">

          <el-tab-pane label="配置信息" name="config">

            <el-divider content-position="left">基础信息</el-divider>

            <el-row :gutter="24">

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item label="表名称" prop="table_name">
                  <el-input v-model="form.table_name" :disabled="true"></el-input>
                </el-form-item>
              </el-col>

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item label="表描述" prop="table_comment">
                  <el-input v-model="form.table_comment"></el-input>
                </el-form-item>
              </el-col>

            </el-row>

            <el-row :gutter="0">
              <el-col :span="24">
                <el-form-item label="备注信息" prop="remark">
                  <el-input
                    type="textarea"
                    :rows="3"
                    placeholder="备注内容"
                    v-model="form.remark">
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <el-divider content-position="left">生成配置</el-divider>

            <el-row :gutter="24">

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item class="ma-inline-form-item" prop="module_name">

                  <template #label>
                    所属模块
                    <el-tooltip content="所属模块请对应表模块前缀，否则数据迁移文件不会被执行">
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-select
                    style="width: 100%"
                    v-model="form.module_name"
                    clearable
                    placeholder="请选择所属模块"
                    @change="hanldeChangeModule"
                  >
                    <el-option
                      :label="item.name"
                      :value="item.name"
                      v-for="(item, index) in sysinfo.modulesList"
                      :key="index"
                      >
                        <span style="float: left">{{ item.name }}</span>
                        <span style="float: right; color: #8492a6; font-size: 13px">{{ item.label }}</span>
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item prop="belong_menu_id">
                  <template #label>
                    所属菜单
                    <el-tooltip content="分配业务功能在哪个菜单，例如：权限管理">
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-cascader
                    v-model="form.belong_menu_id"
                   clearable
                    style="width:100%"
                    :options="menus"
                    :props="{ checkStrictly: true }"
                  ></el-cascader>

                </el-form-item>
              </el-col>

            </el-row>

            <el-row :gutter="24">

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item prop="type" label="生成类型">
                  <el-select
                    v-model="form.type"
                    placeholder="请选择生成类型"
                    style="width: 100%"
                  >
                    <el-option label="单表（增删改查）" value="single">单表（增删改查）</el-option>
                    <el-option label="树表（增删改查）" value="tree">树表（增删改查）</el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item class="ma-inline-form-item" prop="menu_name">

                  <template #label>
                    菜单名称
                    <el-tooltip content="比如，用户管理">
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>
                  <el-input v-model="form.menu_name"></el-input>

                </el-form-item>
              </el-col>

            </el-row>

            <el-row :gutter="24">

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item prop="package_name">
                  <template #label>
                    包名
                    <el-tooltip content="控制器文件所在目录名，比如：permission">
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-input v-model="form.package_name"></el-input>
                </el-form-item>
              </el-col>

              <el-col :xs="24" :md="12" :xl="12">
                <el-form-item prop="generate_type">
                  <template #label>
                    生成方式
                    <el-tooltip>
                      <template #content>
                        压缩包下载：<br />
                        后端文件、前端vue和菜单SQL文件会打包成压缩文件下载。<br /><br />
                        生成到模块：<br />
                        后端文件会直接部署到模块（覆盖原文件），前端vue文件和菜单SQL会打包下载。
                      </template>
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-radio-group v-model="form.generate_type" @change="handleChangeGenType">
                    <el-radio-button label="0">压缩包下载</el-radio-button>
                    <el-radio-button label="1">生成到模块</el-radio-button>
                  </el-radio-group>
                </el-form-item>
              </el-col>

            </el-row>

            <el-row v-if="form.type === 'tree'">
              <el-divider content-position="left">生成类型配置</el-divider>

                <el-col :xs="24" :md="8" :xl="8">
                  <el-form-item prop="tree_id">
                    <template #label>
                      树主ID
                      <el-tooltip content="一般为主键ID">
                        <el-icon><el-icon-question-filled /></el-icon>
                      </el-tooltip>
                    </template>

                    <el-select
                      v-model="tree_id"
                      placeholder="请选择树主ID字段"
                      style="width: 100%"
                    >
                      <el-option
                        v-for="(item, index) in columns"
                        :key="index"
                        :label="item.column_name + ' - ' + item.column_comment"
                        :value="item.column_name"
                      ></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <el-col :xs="24" :md="8" :xl="8">
                  <el-form-item prop="tree_parent_id">
                    <template #label>
                      树父ID
                      <el-tooltip content="树节点的父ID，比如：parent_id">
                        <el-icon><el-icon-question-filled /></el-icon>
                      </el-tooltip>
                    </template>

                    <el-select
                      v-model="tree_parent_id"
                      placeholder="请选择树父ID字段"
                      style="width: 100%"
                    >
                      <el-option
                        v-for="(item, index) in columns"
                        :key="index"
                        :label="item.column_name + ' - ' + item.column_comment"
                        :value="item.column_name"
                      ></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <el-col :xs="24" :md="8" :xl="8">
                  <el-form-item prop="tree_name">
                    <template #label>
                      树名称
                      <el-tooltip content="树显示的名称字段，比如：name">
                        <el-icon><el-icon-question-filled /></el-icon>
                      </el-tooltip>
                    </template>

                    <el-select
                      v-model="tree_name"
                      placeholder="请选择树名称字段"
                      style="width: 100%"
                    >
                      <el-option
                        v-for="(item, index) in columns"
                        :key="index"
                        :label="item.column_name + ' - ' + item.column_comment"
                        :value="item.column_name"
                      ></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

            </el-row>

          </el-tab-pane>

          <!-- <el-tab-pane label="菜单配置" name="menu">
            选择菜单
          </el-tab-pane> -->

          <el-tab-pane label="字段管理" name="field">

            <el-table :data="columns" empty-text="表中无字段...">

              <el-table-column prop="sort" label="排序">
                <template v-slot="scope">
                  <el-input v-model="scope.row.sort" clearable placeholder="排序"></el-input>
                </template>
              </el-table-column>

              <el-table-column prop="column_name" label="字段名称" />

              <el-table-column prop="column_comment" label="字段描述">
                <template v-slot="scope">
                  <el-input v-model="scope.row.column_comment" clearable placeholder="注释"></el-input>
                </template>
              </el-table-column>

              <el-table-column prop="column_type" label="物理类型" />

              <el-table-column prop="is_required" label="必填" width="60">
                <template v-slot="scope">
                  <el-checkbox true-label="1" false-label="0" v-model="scope.row.is_required"></el-checkbox>
                </template>
              </el-table-column>

              <el-table-column prop="is_insert" label="插入" width="60">
                <template v-slot="scope">
                  <el-checkbox true-label="1" false-label="0" v-model="scope.row.is_insert"></el-checkbox>
                </template>
              </el-table-column>

              <el-table-column prop="is_edit" label="编辑" width="60">
                <template v-slot="scope">
                  <el-checkbox true-label="1" false-label="0" v-model="scope.row.is_edit"></el-checkbox>
                </template>
              </el-table-column>

              <el-table-column prop="is_list" label="列表" width="60">
                <template v-slot="scope">
                  <el-checkbox true-label="1" false-label="0" v-model="scope.row.is_list"></el-checkbox>
                </template>
              </el-table-column>

              <el-table-column prop="is_query" label="查询" width="60">
                <template v-slot="scope">
                  <el-checkbox true-label="1" false-label="0" v-model="scope.row.is_query"></el-checkbox>
                </template>
              </el-table-column>

              <el-table-column prop="query_type" label="查询方式">
                <template v-slot="scope">
                  <el-select
                    v-model="scope.row.query_type"
                    placeholder="请选择查询方式"
                    style="width: 100%"
                  >
                    <el-option
                      v-for="(item, index) in queryType"
                      :key="index"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </template>
              </el-table-column>

              <el-table-column prop="view_type" label="页面控件">
                <template v-slot="scope">
                  <el-select
                    v-model="scope.row.view_type"
                    placeholder="请选择页面控件"
                    style="width: 100%"
                  >
                    <el-option
                      v-for="(item, index) in viewComponent"
                      :key="index"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </template>
              </el-table-column>

              <el-table-column prop="dict_type" label="数据字典">
                <template v-slot="scope">
                  <el-select
                    v-model="scope.row.dict_type"
                    placeholder="请选择数据字典"
                    style="width: 100%"
                    clearable
                  >
                    <el-option
                      v-for="(item, index) in dict"
                      :key="index"
                      :label="item.name"
                      :value="item.code"
                    />
                  </el-select>
                </template>
              </el-table-column>

            </el-table>

          </el-tab-pane>
      </el-tabs>

      <div style="text-align:center; margin-bottom: 20px; margin-top: 20px;">
        <el-button type="primary" @click="handleSubmit" :loading="saveLoading">提交</el-button>
        <el-button @click="handleClose">关闭</el-button>
      </div>
    </el-form>

  </el-drawer>

</template>
<script>
export default {
  emits: ['confirm'],
  data () {
    return {
      // 显示抽屉
      drawer: false,
      // 默认激活
      activeName: '',
      // 表单字段
      form: {
        id: '',
        table_name: '',
        module_name: '',
        table_comment: '',
        menu_name: '',
        package_name: '',
        remark: '',
        type: '',
        belong_menu_id: '',
        namespace: '',
        generate_type: '',
        options: {},
        columns: [],
      },

      // 保存loading
      saveLoading: false,

      // 验证规则
      rules: {
        table_comment: [{ required: true, message: '请填写表描述', trigger: 'blur' }],
        module_name: [{ required: true, message: '请选择所属模块（注意对应表模块前缀）', trigger: 'change' }],
        belong_menu_id: [{ required: true, message: '请选择所属菜单', trigger: 'change' }],
        menu_name: [{ required: true, message: '请选择所属菜单', trigger: 'blur' }],
        // package_name: [{ required: false, pattern: /^[A-Za-z]{3,}$/g, message: '包名必须为3位字母及以上', trigger: 'blur' }]
      },

      tree_id: '',

      tree_parent_id: '',

      tree_name: '',

      // 当前记录
      record: null,

      // 字段列表
      columns: [],
      // 菜单列表
      menus: [],
      // 字典列表
      dict: [],
      // 模块信息
      sysinfo: {},
      // 查询类型
      queryType: [
        { label: '=', value: 'eq' },
        { label: '!=', value: 'neq' },
        { label: '>', value: 'gt' },
        { label: '>=', value: 'gte' },
        { label: '<', value: 'lt' },
        { label: '<=', value: 'lte' },
        { label: 'LIKE', value: 'like' },
        { label: 'BETWEEN', value: 'between' },
      ],
      // 页面控件
      viewComponent: [
        { label: '文本框', value: 'text' },
        { label: '密码框', value: 'password' },
        { label: '文本域', value: 'textarea' },
        { label: '下拉框', value: 'select' },
        { label: '单选框', value: 'radio' },
        { label: '复选框', value: 'checkbox' },
        { label: '日期控件', value: 'date' },
        { label: '资源选择单选', value: 'selectResourceRadio' },
        { label: '资源选择多选', value: 'selectResourceMulti' },
        { label: '图片上传', value: 'image' },
        { label: '文件上传', value: 'file' },
        { label: '富文本控件', value: 'editor' },
        { label: '标签页', value: 'tabs' },
      ]
    }
  },

  methods: {

    handleChangeGenType(value) {
      if (value === '1') {
        this.$confirm('生成到模块会覆盖原文件，确定使用该方式吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then().catch(_=> {
          this.form.generate_type = '0'
        })
      }
    },

    async show (record) {
      this.drawer = true
      this.record = record
      this.activeName = 'config'
      this.setFormValue()

      await this.getTableColumns()
      await this.getSystemInfo()
      await this.getMenu()
      await this.getDictType()
    },

    handleClose () {
      this.drawer = false
    },

    getMenu () {
      this.$API.menu.tree().then(res => {
        this.menus = res.data
      })
    },

    getSystemInfo () {
      this.$API.table.getSystemInfo().then(res => {
        this.sysinfo = res.data
      })
    },

    // 请求字典列表
    getDictType () {
      this.$API.dictType.getTypeList().then(res => {
        this.dict = res.data
      })
    },

    // 请求表字段
    getTableColumns () {
      this.$API.generate.getTableColumns({ table_id: this.record.id }).then(res => {
        this.columns = res.data
      })
    },

    // 提交数据
    handleSubmit () {
      this.$refs.form.validate(async (valid) => {
        if (valid) {
            this.form.columns = this.columns
            this.saveLoading = true
            if ( this.form.type == 'tree') {
              this.form.options = { tree_id: this.tree_id, tree_parent_id: this.tree_parent_id, tree_name: this.tree_name }
            }
            let res = await this.$API.generate.update(this.form)
            this.saveLoading = false
            if (res.success) {
              this.$emit('confirm')
              this.record = null
              this.$message.success(res.message)
              this.drawer = false
            } else {
              this.$alert(res.message, "提示", { type: 'error' })
            }
        }
      })
    },

    // 为form赋值
    setFormValue () {
      this.form.id = this.record.id
      this.form.table_name = this.record.table_name
      this.form.table_comment = this.record.table_comment
      this.form.module_name = this.record.module_name
      this.form.menu_name = this.record.menu_name
      this.form.belong_menu_id = this.record.belong_menu_id
      this.form.package_name = this.record.package_name
      this.form.remark = this.record.remark
      this.form.type = this.record.type
      this.form.generate_type = this.record.generate_type

      if (this.form.type == 'single') {
        this.form.options = {}
      }

      if (this.form.type == 'tree') {
        this.tree_id = this.record.options.tree_id
        this.tree_parent_id = this.record.options.tree_parent_id
        this.tree_name = this.record.options.tree_name
      }
    },

    // 选择模块处理
    hanldeChangeModule (val) {
      this.form.module_name = val
    },
  }
}
</script>
<style scoped>
.form {
  padding: 0 30px;
}
:deep(.el-form-item--small .el-form-item__content) {
  line-height: 22px;
}
</style>
