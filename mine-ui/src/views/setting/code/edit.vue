<template>
	<sc-page-header :title="title" icon="el-icon-lock">
		<el-button @click="$router.push('code')">返回代码生成器</el-button>
	</sc-page-header>
  <el-main class="nopadding">
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
                    <el-tooltip content="分配业务功能在哪个菜单，例如：权限管理。不选择则为顶级菜单">
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

                <el-form-item prop="generate_type">
                  <template #label>
                    菜单选项
                    <el-tooltip>
                      <template #content>
                        不构建菜单<br />
                        生成代码时，系统不执行SQL语句<br />
                        构建菜单<br />
                        生成代码时，系统自动将菜单SQL语句导入菜单
                      </template>
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-radio-group v-model="form.build_menu" @change="handleBuildMenu">
                    <el-radio-button label="0">不构建菜单</el-radio-button>
                    <el-radio-button label="1">构建菜单</el-radio-button>
                  </el-radio-group>
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

          <el-tab-pane label="字段管理" name="field">
            <el-alert title="只有下拉框、复选框、单选框、标签页支持数据字典，Switch开关和计数器在【菜单配置】里请勾选相应菜单" type="info" />
            <el-table :data="columns" empty-text="表中无字段...">

              <el-table-column prop="sort" label="排序" width="80">
                <template v-slot="scope">
                  <el-input v-model="scope.row.sort" clearable placeholder="排序"></el-input>
                </template>
              </el-table-column>

              <el-table-column prop="column_name" label="字段名称"/>

              <el-table-column prop="column_comment" label="字段描述">
                <template v-slot="scope">
                  <el-input v-model="scope.row.column_comment" clearable placeholder="注释"></el-input>
                </template>
              </el-table-column>

              <el-table-column prop="column_type" label="物理类型" width="120" />

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
                    @change="settingComponent(scope.row, scope.$index)"
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

              <el-table-column prop="allow_role" label="允许角色">
                <template v-slot="scope">
                  <el-select
                    v-model="scope.row.allow_role"
                    placeholder="允许查看的角色"
                    style="width: 100%"
                    clearable
                  >
                    <el-option
                      v-for="(item, index) in roles"
                      :key="index"
                      :label="item.name"
                      :value="item.code"
                    />
                  </el-select>
                </template>
              </el-table-column>

            </el-table>

          </el-tab-pane>

          <el-tab-pane label="菜单配置" name="menu">
            <el-alert :title="`未选择的菜单，后端也对应不生成方法。注意：列表按钮菜单是默认的`" type="info" />
            <el-form-item label="选择" v-for="(menu,index) in menuList" :key="index">
              <el-checkbox :value="menu.name" :label="menu.name" v-model="menu.check" true-label="1" false-label="0" />
              <div class="el-form-item-msg" style="margin-left: 10px">{{menu.comment}}</div>
            </el-form-item>
          </el-tab-pane>

          <el-tab-pane label="关联配置" name="relation">
            <el-alert :title="`
            模型关联支持：一对一、一对多、一对多（反向）、多对多。
            `" type="info" />
            <el-button @click="addRelation" icon="el-icon-plus" style="margin-top: 10px;">新增关联</el-button>
            <div v-for="(item, index) in relations" :key="index">
              <el-divider content-position="left">
                {{ item.name ? item.name : '定义新关联' }}
                <el-button type="text" @click="delRelation(index)" icon="el-icon-delete" style="margin-left: 10px;">删除定义</el-button>
              </el-divider>
              <el-row :gutter="24">
                <el-col :xs="24" :md="12" :xl="12">
                  <el-form-item class="ma-inline-form-item" label="关联类型">
                    <el-select v-model="item.type" style="width: 100%">
                      <el-option v-for="type in realtionsType" :key="type.value" :value="type.value" :label="type.name" />
                    </el-select>
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" label="关联模型">
                    <el-input v-model="item.model" />
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" label="关联键名称">
                    <el-input v-model="item.localKey" />
                  </el-form-item>
                </el-col>
                <el-col :xs="24" :md="12" :xl="12">
                  <el-form-item class="ma-inline-form-item" label="关联名称">
                    <el-input v-model="item.name" />
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" v-show="item.type === 'belongsToMany'" label="中间表名称">
                    <el-input v-model="item.table" />
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" label="外键名称">
                    <el-input v-model="item.foreignKey" />
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-tab-pane>

      </el-tabs>

      <div style="text-align:center; margin-bottom: 20px; margin-top: 20px;">
        <el-button type="primary" @click="handleSubmit" :loading="saveLoading">提交</el-button>
      </div>
    </el-form>
  </el-main>

  <el-drawer v-model="drawer" @close="handleClose" :size="'380px'">
    <el-form :model="settingForm" style="padding-left: 20px;">
      <!-- 用户信息 -->
      <el-form-item label="用户信息" v-if="this.selectField.view_type === 'userinfo'" prop="userinfo">
        <el-select v-model="settingForm.userinfo">
          <el-option label="用户ID" value="id" />
          <el-option label="用户账号" value="username" />
          <el-option label="用户昵称" value="nickname" />
          <el-option label="用户部门ID" value="dept_id" />
          <el-option label="用户手机" value="phone" />
          <el-option label="用户邮箱" value="email" />
        </el-select>
        <div class="el-form-item-msg">选择要保存的用户信息</div>
      </el-form-item>

      <!-- 日期选择器 -->
      <el-form-item label="控件类型" v-if="this.selectField.view_type === 'date'" prop="date">
        <el-select v-model="settingForm.date">
          <el-option label="日期选择器" value="default" />
          <el-option label="日期时间选择器" value="datetime" />
          <el-option label="日期范围" value="date_range" />
          <el-option label="日期时间范围" value="datetime_range" />
          <el-option label="周选择器" value="week" />
          <el-option label="月选择器" value="month" />
          <el-option label="年选择器" value="year" />
        </el-select>
        <div class="el-form-item-msg">请选择日期控件类型</div>
      </el-form-item>

      <el-button @click="handleSetting">确定</el-button>
    </el-form>
  </el-drawer>
</template>
<script>
import useTabs from '@/utils/useTabs'
export default {
  name: 'setting:code:update',
  data () {
    return {
      drawer: false,
      title: '',
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
        generate_type: '0',
        generate_menus: [],
        build_menu: '0',
        options: {},
        columns: [],
      },

      settingForm: {
        userinfo: 'id',
        date: 'default',
        tabs: []
      },

      menuList: [
        { name: '新增', value: 'save', comment: '勾选生成新增数据按钮菜单及接口', check: '1' },
        { name: '更新', value: 'update', comment: '勾选生成更新数据按钮菜单及接口', check: '1'  },
        { name: '读取', value: 'read', comment: '勾选生成读取数据按钮菜单及接口', check: '1'  },
        { name: '回收站列表', value: 'delete', comment: '勾选生成移到回收站列表、移到回收站、恢复菜单及接口，确定该表有deleted_at字段，且模型引入了软删除。', check: '1' },
        { name: '真实删除', value: 'realDelete', comment: '勾选生成真实删除按钮菜单及接口', check: '1' },
        { name: '修改状态', value: 'changeStatus', comment: '勾选生成修改状态按钮菜单及接口，该接口用于单个字段状态修改', check: '1' },
        { name: '自增自减', value: 'numberOperation', comment: '勾选生成数据自增自减按钮菜单及接口，该接口用于单个字段增减操作', check: '1' },
        { name: '导入', value: 'import', comment: '勾选生成导入按钮菜单、接口和DTO文件', check: '1' },
        { name: '导出', value: 'export', comment: '勾选生成导出按钮菜单、接口和DTO文件', check: '1' },
      ],

      // 保存loading
      saveLoading: false,

      // 验证规则
      rules: {
        table_comment: [{ required: true, message: '请填写表描述', trigger: 'blur' }],
        module_name: [{ required: true, message: '请选择所属模块（注意对应表模块前缀）', trigger: 'change' }],
        // belong_menu_id: [{ required: true, message: '请选择所属菜单', trigger: 'change' }],
        menu_name: [{ required: true, message: '请选择所属菜单', trigger: 'blur' }],
        // package_name: [{ required: false, pattern: /^[A-Za-z]{3,}$/g, message: '包名必须为3位字母及以上', trigger: 'blur' }]
      },

      tree_id: '',
      tree_parent_id: '',
      tree_name: '',

      // 关联关系
      relations: [],
      realtionsType: [
        { name: '一对一', value: 'hasOne' },
        { name: '一对多', value: 'hasMany' },
        { name: '一对多（反向)', value: 'belongsTo' },
        { name: '多对多', value: 'belongsToMany' },
      ],

      // 当前记录
      record: null,

      // 字段列表
      columns: [],
      // 菜单列表
      menus: [],
      // 角色列表
      roles: [],
      // 字典列表
      dict: [],
      // 模块信息
      sysinfo: {},
      
      selectField: '',

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
        { label: '计数器', value: 'inputNumber' },
        { label: 'Switch开关', value: 'switch' },
        { label: '滑块', value: 'slider' },
        { label: '下拉框', value: 'select' },
        { label: '单选框', value: 'radio' },
        { label: '复选框', value: 'checkbox' },
        { label: '日期选择器', value: 'date' },
        { label: '时间选择器', value: 'time' },
        { label: '评分器', value: 'rate' },
        { label: '颜色选择器', value: 'colorPicker' },
        // { label: '分片上传', value: 'chunkUpload' },
        { label: '用户选择器', value: 'userSelect' },
        { label: '用户信息', value: 'userinfo' },
        { label: '省市区联动', value: 'area' },
        { label: '资源选择单选', value: 'selectResourceRadio' },
        { label: '资源选择多选', value: 'selectResourceMulti' },
        { label: '图片上传', value: 'image' },
        { label: '文件上传', value: 'file' },
        { label: '富文本控件', value: 'editor' },
        { label: '标签页', value: 'tabs' },
      ]
    }
  },

  async created () {
    if (! this.$route.query.id) {
      this.$message.error('请从正确来路访问页面，标签页已关闭')
      useTabs.close()
    }
    const table = await this.$API.generate.readTable({ id: this.$route.query.id })
    this.record = table.data
    this.activeName = 'config'
    this.title = '更新业务表 - ' + this.record.table_comment
    useTabs.setTitle(this.title)
    this.setFormValue()

    await this.getTableColumns()
    await this.getSystemInfo()
    await this.getMenu()
    await this.getRoles()
    await this.getDictType()
  },

  methods: {

    addRelation() {
      let relation = {
        name: '', type: 'hasOne', model: '', foreignKey: '', localKey: '', table: ''
      }
      this.relations.push(relation)
    },

    delRelation(index) {
      this.relations.splice(index, 1)
    },

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

    handleBuildMenu(value) {
      if (value === '1') {
        this.$confirm('确定选择生成代码时执行菜单SQL语句', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then().catch(_=> {
          this.form.build_menu = '0'
        })
      }
    },

    handleClose () {
      this.drawer = false
      this.selectField = ''
      this.componentInfo = {}
    },

    getMenu () {
      this.$API.menu.tree({ onlyMenu: true }).then(res => {
        this.menus = res.data
      })
    },

    getRoles () {
      this.$API.role.getList().then(res => {
        this.roles = res.data
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

    settingComponent(row, index) {
      let showDrawerList = [
        'date', 'time', 'userinfo', 'tabs'
      ]
      row.$index = index
      if (showDrawerList.includes(row.view_type)) {
        this.selectField = row
        this.drawer = true
      }
    },

    handleSetting() {
      let index = this.selectField.$index;
      if (! this.columns[index].options) {
        this.columns[index].options = {}
      }
      if (this.selectField.view_type === 'userinfo') {
        this.columns[index].options.userinfo = this.settingForm.userinfo
      }

      if (this.selectField.view_type === 'date') {
        this.columns[index].options.date = this.settingForm.date
      }

      this.handleClose()
    },

    // 提交数据
    handleSubmit () {
      this.$refs.form.validate(async (valid) => {
        if (valid) {
          this.form.columns = this.columns
          // this.saveLoading = true
          this.menuList.map(item => {
            if (item.check === '1') this.form.generate_menus.push(item.value)
          })
          
          this.form.options = { relations: this.relations, tree_id: this.tree_id, tree_parent_id: this.tree_parent_id, tree_name: this.tree_name }
          console.log(this.form)
          return
          let res = await this.$API.generate.update(this.form)
          this.saveLoading = false
          if (res.success) {
            this.record = null
            this.$message.success(res.message)
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
