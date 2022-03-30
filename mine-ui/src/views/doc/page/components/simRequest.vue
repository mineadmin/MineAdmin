<template>
  <el-form ref="form" :model="form" label-width="80px" label-position="top">
    <el-form-item label="请求地址" prop="url">
      <el-input v-model="form.url" placeholder="请输入URL地址">
        <template #prepend placeholder="请选择" style="width: 150px;">
          <el-select v-model="form.method">
            <el-option label="GET" value="GET">GET</el-option>
            <el-option label="POST" value="POST">POST</el-option>
            <el-option label="PUT" value="PUT">PUT</el-option>
            <el-option label="DELETE" value="DELETE">DELETE</el-option>
          </el-select>
        </template>
      </el-input>
    </el-form-item>
    <el-row>
      <el-button @click="sendRequest" :loading="loading" icon="el-icon-promotion" type="primary">发送请求</el-button>
      <el-button @click="isHiddenParams = !isHiddenParams"> {{ isHiddenParams ? '查看全局参数' : '隐藏全局参数' }}</el-button>
    </el-row>
  </el-form>

  <el-card v-if="!isHiddenParams" style="margin-top: 15px;">
    <el-divider content-position="left">全局Header</el-divider>
    <ma-highlight :code="JSON.stringify(globalParams.header)" lang="json" />
    <el-divider content-position="left">全局Query</el-divider>
    <ma-highlight :code="JSON.stringify(globalParams.query)" lang="json" />
    <el-divider content-position="left">全局Body</el-divider>
    <ma-highlight :code="JSON.stringify(globalParams.body)" lang="json" />
  </el-card>

  <el-tabs v-model="activeTab" style="margin-top: 15px;">
    <el-tab-pane label="Header" name="header">
      <el-button @click="addParams('header')" icon="el-icon-plus" type="primary">新增</el-button>
      <el-table :data="requestParams.header" stripe style="width: 100%">
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

    <el-tab-pane label="Query" name="query">
      <el-button @click="addParams('query')" icon="el-icon-plus" type="primary">新增</el-button>
      <el-table :data="requestParams.query" stripe style="width: 100%">
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

    <el-tab-pane label="Body" name="body">
      <el-button @click="addParams('body')" icon="el-icon-plus" type="primary">新增</el-button>
      <el-table :data="requestParams.body" stripe style="width: 100%">
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

  <el-card style="margin-top: 15px;">
    <template #header>服务器响应</template>
    <ma-highlight :code="JSON.stringify(response)" lang="json" />
  </el-card>
</template>

<script>
import maHighlight from '@/components/maHighlight'
import { request } from '@/utils/request.js'
export default {
  components: {
    maHighlight
  },
  props: {
    url: {
      type:String
    }
  },
  watch: {
    url(value) {
      this.form.url = '/api/v1/' + value
    }
  },
  created () {
    if (this.$TOOL.session.get('globalParams')) {
      this.globalParams = this.$TOOL.session.get('globalParams')
    }
    this.form.url = '/api/v1/' + this.url
  },

  data () {
    return {
      form: { url: '', method: 'GET'},
      isHiddenParams: true,
      activeTab: 'header',
      loading: false,
      globalParams: {
        header: [],
        query: [],
        body: []
      },
      requestParams: {
        header: [],
        query: [],
        body: []
      },
      response: {}
    }
  },

  methods: {
    sendRequest() {
      if (this.form.url === '') {
        this.$message.error('请输入请求地址')
        return;
      }

      let header = {}
      let query  = {}
      let body   = {}

      this.requestParams.header.map(item => {
        header[item.name] = item.value
      })
      this.requestParams.query.map(item => {
        query[item.name] = item.value
      })
      this.requestParams.body.map(item => {
        body[item.name] = item.value
      })

      const config = {
        header: Object.assign(header, this.globalParams.header),
        params: Object.assign(query, this.globalParams.query),
        data  : Object.assign(body, this.globalParams.body),
        method: this.form.method,
        url: this.form.url
      }

      this.loading = true
      this.response = {}

      request(config).then(res => {
        this.loading = false
        res.success ? this.$message.success(res.message) : this.$message.error(res.message)
        this.response = res.data
      }).catch(e => {
        this.loading = false
        this.$message.error(res.message)
      })
    },

    addParams(type) {
      this.requestParams[type].push({name: '', value: ''})
    },

    deleteParams(index, type) {
      this.requestParams[type].splice(index, 1)
    },
  }
}
</script>

<style scoped>
:deep(.el-input-group__prepend) {
  width: 80px;
}
</style>