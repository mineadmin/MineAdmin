<template>
  <el-form ref="form" :inline="true" :model="form" :rules="rules" label-width="80px" class="page-form">
    <el-alert
      title="提示"
      type="warning"
      :closable="false"
      description="本表设计器不能替代专业工具，仅提供常用字段选项。另外建议自己制作一个迁移文件"
      show-icon>
    </el-alert>
    <el-card class="box-card ma-card" shadow="hover">

      <template #header class="clearfix">
        <span>基础数据</span>
      </template>

      <el-row :gutter="15">

        <el-col :xs="24" :md="7" :xl="7">
          <el-form-item label="表名称" prop="name" style="width:100%">
            <el-input v-model="form.name" placeholder="请输入表名称" clearable>
              <template #prepend>{{sysinfo.tablePrefix === '' ? '无前缀' : sysinfo.tablePrefix}} {{currentModule === '' ? ''  : currentModule + '_'}}</template>
            </el-input>
          </el-form-item>
        </el-col>

        <el-col :xs="24" :md="6" :xl="6">
          <el-form-item label="所属模块" prop="module" style="width:100%">
            <el-select v-model="form.module" clearable placeholder="请选择模块" @change="hanldeChangeModule" style="width:100%">
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

        <el-col :xs="24" :md="6" :xl="3">
          <el-button type="primary" @click="handleSubmit" :loading="loading">创建数据表</el-button>
          <el-button @click="reset">重置</el-button>
        </el-col>
      </el-row>

    </el-card>

    <el-card class="box-card ma-card" shadow="hover">

      <template #header class="clearfix">
        <el-row :gutter="15">

          <el-col :xs="24" :md="20" :sm="20" :xl="22" style="margin-bottom: 15px;">
            <span>字段</span>
          </el-col>

          <el-col :xs="24" :md="4" :sm="4" :xl="2" style="text-align:right">
            <el-button icon="el-icon-plus" style="width:100%" type="primary" @click="handleAddColumn">增加字段</el-button>
          </el-col>

        </el-row>
        
      </template>

      <el-table :data="form.columns" empty-text="请添加字段...">

        <el-table-column label="操作" align="center" fixed="left" width="100">
          <template #default="scope">
            <el-button type="primary" link @click="handleDeleteColumn(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>

        <el-table-column prop="name" label="字段名称">
          <template #default="scope">
            <el-input v-model="scope.row.name" clearable placeholder="字段名称"></el-input>
          </template>
        </el-table-column>

        <el-table-column prop="type" label="字段类型">
          <template #default="scope">
            <el-select v-model="scope.row.type" clearable placeholder="字段类型">
              <el-option-group
                v-for="group in mysqlTypes"
                :key="group.label"
                :label="group.label">
                <el-option
                  v-for="(item, key) in group.options"
                  :key="key"
                  :label="item.value"
                  :value="item.value">
                </el-option>
              </el-option-group>
            </el-select>
          </template>
        </el-table-column>

        <el-table-column prop="unsigned" label="Unsigned" width="100">
          <template #default="scope">
            <el-checkbox v-model="scope.row.unsigned"></el-checkbox>
          </template>
        </el-table-column>

        <el-table-column prop="isNull" label="NULL" width="100">
          <template #default="scope">
            <el-checkbox v-model="scope.row.isNull"></el-checkbox>
          </template>
        </el-table-column>

        <el-table-column prop="len" label="长度">
          <template #default="scope">
            <el-input-number v-model="scope.row.len" clearable controls-position="right" :min="1" :max="9999"></el-input-number>
          </template>
        </el-table-column>

        <el-table-column prop="index" label="索引类型">
          <template #default="scope">
            <el-select v-model="scope.row.index" clearable placeholder="索引类型">
              <el-option v-for="name in indexs" :value="name" :key="name">{{name}}</el-option>
            </el-select>
          </template>
        </el-table-column>

        <el-table-column prop="default" label="默认值">
          <template #default="scope">
            <el-input v-model="scope.row.default" clearable placeholder="默认值"></el-input>
          </template>
        </el-table-column>

        <el-table-column prop="comment" label="注释">
          <template #default="scope">
            <el-input v-model="scope.row.comment" clearable placeholder="注释"></el-input>
          </template>
        </el-table-column>

      </el-table>

      <el-row :gutter="15" style="margin-top: 25px;">

        <el-col :xs="24" :md="6" :xl="6">
          <el-form-item label="表引擎" prop="engine" style="width:100%">
            <el-select v-model="form.engine" placeholder="表引擎" clearable style="width:100%">
                <el-option :value="item.value" :label="item.label" v-for="(item, index) in engines" :key="index">{{item.label}}</el-option>
              </el-select>
          </el-form-item>
        </el-col>

        <el-col :xs="24" :md="6" :xl="6">
          <el-form-item label="表注释" fixed prop="comment" clearable style="width:100%">
            <el-input  v-model="form.comment" placeholder="请输入表注释"></el-input>
          </el-form-item>
        </el-col>

        <el-col :xs="24" :md="6" :xl="6">
          <el-form-item label="ID主键" prop="pk" clearable style="width:100%">
            <el-input  v-model="form.pk" placeholder="请输入ID主键"></el-input>
          </el-form-item>
        </el-col>

      </el-row>

      <el-row class="ma-row">
        <el-checkbox v-model="form.autoTime">创建时间 & 更新时间</el-checkbox>
        <el-checkbox v-model="form.autoUser">创建人 & 更新人</el-checkbox>
        <el-checkbox v-model="form.softDelete">软删除</el-checkbox>
        <el-checkbox v-model="form.snowflakeId">主键雪花ID</el-checkbox>
      </el-row>

    </el-card>
  </el-form>
</template>
<script>
export default {
  name: 'setting:table',
  data () {
    return {
      loading: false,
      form: {
        name: '',
        module: '',
        columns: [],
        autoTime: true,
        autoUser: true,
        softDelete: true,
        snowflakeId: true,
        // migrate: true,
        pk: 'id',
        engine: '',
        comment: ''
      },
      sysinfo: {},
      currentModule: '',
      tablePrefix: '',
      engines: null,
      fields: { id: 0, name: '', type: '', unsigned: false, len: 0, isNull: true, index: '', default: '', comment: '' },
      indexs: ['UNIQUE', 'NORMAL', 'FULLTEXT'],
      mysqlTypes,
      rules: {
        name: [{ required: true, pattern: /^[A-Za-z|_]{2,}$/g, message: '表名称只能是英文和下划线，至少两个字符', trigger: 'blur' }],
        module: [{ required: true, message: '请选择所属模块', trigger: 'change' }],
        engine: [{ required: true, message: '请选择表引擎', trigger: 'change' }],
        comment: [{ required: true, message: '请输入表注释', trigger: 'blur' }],
        pk: [{ required: true, pattern: /^[A-Za-z|_]{2,}$/g, message: '主键为英文和下划线，至少两个字符', trigger: 'blur' }]
      }
    }
  },
  async created () {
    await this.$API.table.getSystemInfo().then(res => {
      this.sysinfo = res.data
    })
    await this.getDict('table_engine').then(res => {
      this.engines = res.data
    })
    this.init()
  },
  methods: {
    // 初始化
    init () {
      this.form.columns.push({
        id: 0, name: 'id', type: 'BIGINT', unsigned: true, len: 20, isNull: false, index: '', default: '', comment: '主键ID'
      })
    },
    // 重置
    reset () {
      this.form = {
        name: '',
        module: '',
        columns: [],
        autoTime: true,
        autoUser: true,
        softDelete: true,
        snowflakeId: true,
        pk: 'id',
        engine: '',
        comment: ''
      }
      this.init()
    },
    // 增加字段
    handleAddColumn () {
      const field = JSON.parse(JSON.stringify(this.fields))
      field.id = this.form.columns.length + 1
      this.form.columns.push(field)
    },
    // 删除字段
    handleDeleteColumn (id) {
      for (let i = 0; i < this.form.columns.length; i++) {
        if (this.form.columns[i].id === id) {
          this.form.columns.splice(i, 1)
          break
        }
      }
    },
    // 选择模块处理
    hanldeChangeModule (val) {
      this.currentModule = val.toLowerCase()
    },
    // 提交数据处理
    handleSubmit () {
      this.loading = true
      this.$refs.form.validate(valid => {
        if (valid) {
          // 提交数据
          if (this.form.columns.length < 1) {
            this.$message.error('表没有字段')
            this.loading = false
            return
          }
          const columns = this.form.columns
          for (let i = 0; i < columns.length; i++) {
            const requiredField = []
            if (columns[i].name === '') {
              requiredField.push('字段名称')
            } else if (!/^[A-Za-z|_]{2,}$/g.test(columns[i].name)) {
              this.$message.error(`第${columns[i].id}行的字段名称必须是英文和下划线组成`)
              this.loading = false
              return
            }
            if (columns[i].type === '') {
              requiredField.push('字段类型')
            }
            if (columns[i].comment === '') {
              requiredField.push('表注释')
            }

            if (requiredField.length > 0) {
              this.$message.error(`第${columns[i].id}行字段列表的 ` + requiredField.join('、') + ' 为空 ')
              this.loading = false
              return
            }
          }
          this.$API.table.save(this.form).then(res => {
            this.$message.success(res.message)
          })
          this.loading = false
        } else {
          this.loading = false
          return false
        }
      })
    }
  }
}

const mysqlTypes = [
  {
    label: '整型及数字类型',
    options: [
      { value: 'BIGINT' },
      { value: 'INT' },
      { value: 'TINYINT' },
      { value: 'SMALLINT' },
      { value: 'MEDIUMINT' },
      { value: 'DECIMAL' }
    ]
  },
  {
    label: '字符串及文本类型',
    options: [
      { value: 'CHAR' },
      { value: 'VARCHAR' },
      { value: 'TINYTEXT' },
      { value: 'TEXT' },
      { value: 'MEDIUMTEXT' },
      { value: 'LONGTEXT' }
    ]
  },
  {
    label: '日期与时间类型',
    options: [
      { value: 'DATE' },
      { value: 'DATETIME' },
      { value: 'TIMESTAMP' },
      { value: 'TIME' }
    ]
  },
  {
    label: 'JSON类型',
    options: [
      { value: 'JSON' }
    ]
  }
]
</script>

<style scoped>
.page-form { padding: 15px; min-height: 100%}
.ma-card, .ma-row {margin-top: 15px;}
</style>
