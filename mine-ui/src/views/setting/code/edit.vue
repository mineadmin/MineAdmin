<template>
	<sc-page-header :title="title" icon="el-icon-edit">
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

                <el-form-item prop="component_type">
                  <template #label>
                    组件样式
                    <el-tooltip>
                      <template #content>
                        设置新增和修改组件显示方式，默认：模态框
                      </template>
                      <el-icon><el-icon-question-filled /></el-icon>
                    </el-tooltip>
                  </template>

                  <el-radio-group v-model="form.component_type">
                    <el-radio-button label="0">模态框</el-radio-button>
                    <el-radio-button label="1">抽屉</el-radio-button>
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

                  <el-radio-group v-model="form.generate_type" @change="handleChangeGenType" :disabled="true">
                    <el-radio-button label="0">压缩包下载</el-radio-button>
                    <el-radio-button label="1">生成到模块</el-radio-button>
                  </el-radio-group>
                </el-form-item>

                <el-form-item prop="build_menu">
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

                  <el-radio-group v-model="form.build_menu" @change="handleBuildMenu" :disabled="true">
                    <el-radio-button label="0">不构建菜单</el-radio-button>
                    <el-radio-button label="1">构建菜单</el-radio-button>
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
            <el-alert title="只有下拉框、复选框、单选框支持数据字典，Switch开关和计数器在【菜单配置】里请勾选相应菜单。" type="info" />
            <el-alert title="使用复选框组件请在模型文件的 casts 里设置相应字段为 array 类型。" type="success" style="margin-top: 10px" />
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
                    clearable
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
                    clearable
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
                    clearable
                    v-model="scope.row.dict_type"
                    placeholder="请选择数据字典"
                    style="width: 100%"
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

              <el-table-column prop="allow_roles" label="允许角色">
                <template v-slot="scope">
                  <el-select
                    clearable
                    v-model="scope.row.allow_roles"
                    placeholder="允许查看的角色"
                    style="width: 100%"
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

          <el-tab-pane label="菜单配置" name="menu" class="menu-config">
            <el-alert :title="`未选择的菜单，后端也对应不生成方法。注意：列表按钮菜单是默认的`" type="info" />
            <el-form-item label="选择" v-for="(menu,index) in menuList" :key="index">
              <el-checkbox :value="menu.name" :label="menu.name" v-model="menu.check" true-label="1" false-label="0" />
              <div class="el-form-item-msg">{{menu.comment}}</div>
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
                <el-button type="primary" link @click="delRelation(index)" icon="el-icon-delete" style="margin-left: 10px;">删除定义</el-button>
              </el-divider>
              <el-row :gutter="24">
                <el-col :xs="24" :md="12" :xl="12">
                  <el-form-item class="ma-inline-form-item" label="关联类型">
                    <el-select v-model="item.type" style="width: 100%" clearable placeholder="请选择关联类型">
                      <el-option v-for="type in realtionsType" :key="type.value" :value="type.value" :label="type.name" />
                    </el-select>
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" label="关联模型">
                    <el-select v-model="item.model" style="width: 100%" filterable clearable placeholder="请选择关联模型，可输入关键字过滤">
                      <el-option v-for="model in models" :key="model" :value="model" :label="model" />
                    </el-select>
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" :label="item.type === 'belongsToMany' ? '中间表外键' : '外键'">
                    <el-input
                      v-model="item.foreignKey"
                      clearable
                      :placeholder="item.type === 'belongsToMany' ? '中间表外键名称' : '关联表外键名称'"
                    />
                  </el-form-item>
                </el-col>
                <el-col :xs="24" :md="12" :xl="12">
                  <el-form-item class="ma-inline-form-item" label="关联名称">
                    <el-input v-model="item.name" clearable placeholder="设置关联名称" />
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" v-show="item.type === 'belongsToMany'" label="中间表名称">
                    <el-select v-model="item.table" style="width: 100%" filterable clearable placeholder="请选择中间表，可输入关键字过滤">
                      <el-option
                        v-for="table in tables"
                        :key="table.name"
                        :label="table.name + ' - ' + table.comment"
                        :value="table.name"
                      />
                    </el-select>
                  </el-form-item>
                  <el-form-item class="ma-inline-form-item" label="关联键">
                    <el-select v-model="item.localKey" style="width: 100%" filterable clearable placeholder="请选择关联键，可输入关键字过滤">
                      <el-option
                        v-for="column in columns"
                        :key="column.column_name"
                        :label="column.column_name + ' - ' + column.column_comment"
                        :value="column.column_name"
                       />
                    </el-select>
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
      <el-form-item label="用户信息" v-if="selectField.view_type === 'userinfo'" prop="userinfo">
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
      <el-form-item label="控件类型" v-if="selectField.view_type === 'date'" prop="date">
        <el-select v-model="settingForm.date">
          <el-option label="日期选择器" value="date" />
          <el-option label="多日期选择器" value="dates" />
          <el-option label="日期时间选择器" value="datetime" />
          <el-option label="日期范围" value="daterange" />
          <el-option label="日期时间范围" value="datetimerange" />
          <el-option label="月份范围" value="monthrange" />
          <el-option label="周选择器" value="week" />
          <el-option label="月选择器" value="month" />
          <el-option label="年选择器" value="year" />
        </el-select>
        <div class="el-form-item-msg">请选择日期控件类型</div>
      </el-form-item>

      <!-- 日期选择器 -->
      <el-form-item label="组件样式" v-if="selectField.view_type === 'area'" prop="type">
        <el-radio-group v-model="settingForm.area.type">
          <el-radio label="cascader">级联选择器样式</el-radio>
          <el-radio label="select">下拉选择器联动</el-radio>
        </el-radio-group>
      </el-form-item>

      <!-- 省市 -->
      <el-form-item label="数据格式" v-if="selectField.view_type === 'area'" prop="value">
        <el-radio-group v-model="settingForm.area.value">
          <el-radio label="code">保存省市代码</el-radio>
          <el-radio label="name">保存省市名称</el-radio>
        </el-radio-group>
      </el-form-item>

      <div style="font-size: 16px" v-if="['radio', 'checkbox', 'select'].includes(selectField.view_type)">
        设置数据
        <div style="margin-top: 10px;"><el-button @click="() => {
           settingForm[selectField.view_type].push({ name: '', value: ''})
        }">添加</el-button></div>
        <el-card
          style="width: 95%; margin-top: 10px"
          shadow="never"
          v-for="(item, index) in settingForm[selectField.view_type]"
          :key="index"
        >
          <el-form-item label="名称" prop="name">
            <el-input v-model="settingForm[selectField.view_type][index].name" />
          </el-form-item>
          <el-form-item label="&nbsp;&nbsp;&nbsp;值" prop="value">
            <el-input v-model="settingForm[selectField.view_type][index].value" />
          </el-form-item>
          <el-button type="danger" @click="(index) => {
            settingForm[selectField.view_type].splice(index, 1)
          }" >删除</el-button>
        </el-card>
      </div>
      <div style="margin-top: 10px">
        <el-button @click="handleSetting" type="primary">确定</el-button>
        <el-button
          @click="drawer = false"
          v-if="['radio', 'checkbox', 'select'].includes(selectField.view_type)"
        >不设置，使用字典数据</el-button>
      </div>
    </el-form>
  </el-drawer>
</template>
<script>
import useTabs from '@/utils/useTabs'
import datas from './js/datas'
import methods from './js/methods'

export default {
  name: 'setting:code:update',
  mixins: [datas, methods],
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
    await this.getModels()
    await this.getTables()
  }
}
</script>
<style scoped lang="scss">
.form {
  padding: 0 30px;
}
:deep(.el-form-item--small .el-form-item__content) {
  line-height: 22px;
}
:deep(.menu-config .el-form-item__content) {
  width: 100%; flex-wrap: inherit;
  .el-form-item-msg {
    margin-left: 20px;
  }
}
</style>
