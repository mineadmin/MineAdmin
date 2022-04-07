<template>
  <el-dialog
    append-to-body
    v-model="dialogVisible"
    title="设置全局参数"
    width="700px"
  >
    <el-tabs v-model="activeTab">

      <el-tab-pane label="全局Header" name="header">
        <el-button @click="addParams('header')" icon="el-icon-plus" type="primary">新增</el-button>
        <el-table :data="globalParams.header" stripe style="width: 100%">
          <el-table-column label="#" width="80">
            <template #default="scope">
              <el-button @click="deleteParams(scope.$index, 'header')">
                <el-icon><el-icon-delete /></el-icon>
              </el-button>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="键名" width="180">
            <template #default="scope">
              <el-input v-model="scope.row.name" clearable placeholder="请输入键名" />
            </template>
          </el-table-column>
          <el-table-column prop="value" label="值">
            <template #default="scope">
              <el-input v-model="scope.row.value" clearable placeholder="请输入值" />
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="全局Query" name="query">
        <el-button @click="addParams('query')" icon="el-icon-plus" type="primary">新增</el-button>
        <el-table :data="globalParams.query" stripe style="width: 100%">
          <el-table-column label="#" width="80">
            <template #default="scope">
              <el-button @click="deleteParams(scope.$index, 'query')">
                <el-icon><el-icon-delete /></el-icon>
              </el-button>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="键名" width="180">
            <template #default="scope">
              <el-input v-model="scope.row.name" clearable placeholder="请输入键名" />
            </template>
          </el-table-column>
          <el-table-column prop="value" label="值">
            <template #default="scope">
              <el-input v-model="scope.row.value" clearable placeholder="请输入值" />
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="全局Body" name="body">
        <el-button @click="addParams('body')" icon="el-icon-plus" type="primary">新增</el-button>
        <el-table :data="globalParams.body" stripe style="width: 100%">
          <el-table-column label="#" width="80">
            <template #default="scope">
              <el-button @click="deleteParams(scope.$index, 'body')">
                <el-icon><el-icon-delete /></el-icon>
              </el-button>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="键名" width="180">
            <template #default="scope">
              <el-input v-model="scope.row.name" clearable placeholder="请输入键名" />
            </template>
          </el-table-column>
          <el-table-column prop="value" label="值">
            <template #default="scope">
              <el-input v-model="scope.row.value" clearable placeholder="请输入值" />
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

    </el-tabs>

    <template #footer>
      <div class="dialog-footer">
        <el-button @click="dialogVisible = false">关闭</el-button>
        <el-button type="primary" @click="setGlobalParams">保存</el-button>
      </div>
    </template>
  </el-dialog>
</template>

<script>
export default {
  data () {
    return {
      activeTab: 'header',
      dialogVisible: false,
      globalParams: {
        header: [],
        query: [],
        body: []
      }
    }
  },

  methods: {
    open () {
      this.dialogVisible = true
      if (this.$TOOL.session.get('globalParams')) {
        this.globalParams = { header: [], query: [], body: [] }
        let params = this.$TOOL.session.get('globalParams')
        for (let name in params.header) {
          this.globalParams.header.push({ 'name': name, value: params.header[name] })
        }
        for (let name in params.query) {
          this.globalParams.query.push({ 'name': name, value: params.query[name] })
        }
        for (let name in params.body) {
          this.globalParams.body.push({ 'name': name, value: params.body[name] })
        }
      }
    },

    addParams(type) {
      this.globalParams[type].push({name: '', value: ''})
    },

    deleteParams(index, type) {
      this.globalParams[type].splice(index, 1)
    },

    setGlobalParams() {
      let header = {}
      let query  = {}
      let body   = {}

      this.globalParams.header.map(item => {
        header[item.name] = item.value
      })
      this.globalParams.query.map(item => {
        query[item.name] = item.value
      })
      this.globalParams.body.map(item => {
        body[item.name] = item.value
      })
      const params = { header, query, body }
      
      this.$TOOL.session.set('globalParams', params)
      this.dialogVisible = false
      this.$message.success('设置成功')
    },
  }
}
</script>