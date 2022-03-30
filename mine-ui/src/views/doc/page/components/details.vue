<template>
  <el-drawer
    v-model="drawer"
    :with-header="false"
    size="50%"
  >
    <el-main>
      <h2 class="lh">{{apiData.name}}</h2>
      <el-tabs v-model="activeName">
        <el-tab-pane label="参数列表" name="params">
          <el-divider content-position="left">接口信息</el-divider>
          <el-descriptions :column="1" border>
            <el-descriptions-item label="接口地址">
              /api/v1/{{ apiData.access_name }}
            </el-descriptions-item>
            <el-descriptions-item label="认证模式">
              <el-tag v-if="apiData.auth_mode === '0'" type="success">简易模式</el-tag>
              <el-tag v-else type="danger">复杂模式</el-tag>
            </el-descriptions-item>
            <el-descriptions-item label="认证说明">
              <div v-if="apiData.auth_mode === '0'">
                简易模式下，只需要在接口后面带上 <el-tag>app_id</el-tag> 和<el-tag>identity</el-tag>
              </div>
              <div v-else>
                复杂模式下，先需要获取 <el-tag>AccessToken</el-tag>，再以此请求接口
              </div>
            </el-descriptions-item>
          </el-descriptions>
          <el-divider content-position="left">请求参数</el-divider>
          <el-table :data="requestData" stripe style="width: 100%">
            <el-table-column type="expand">
              <template #default="props">
                <el-card style="width:99%; margin: 0 auto">
                  <div v-html="props.row.description"></div>
                </el-card>
              </template>
            </el-table-column>
            <el-table-column prop="name" label="参数名" />
            <el-table-column prop="data_type" label="参数名">
              <template #default="props">
                <ma-dict-tag :options="api_data_type" :value="props.row.data_type" />
              </template>
            </el-table-column>
            <el-table-column prop="is_required" label="必填">
              <template #default="props">
                <el-tag type="danger" v-if="props.row.is_required === '0'">是</el-tag>
                <el-tag type="success" v-else>否</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="default_value" label="默认值">
              <template #default="props">
                {{ props.row.default_value ? props.row.default_value : '-' }}
              </template>
            </el-table-column>
          </el-table>

          <el-divider content-position="left">响应参数</el-divider>
          <el-table :data="responseData" stripe style="width: 100%">
            <el-table-column type="expand">
              <template #default="props">
                <ma-highlight :code="props.row.description" lang="js" />
              </template>
            </el-table-column>
            <el-table-column prop="name" label="参数名" />
            <el-table-column prop="data_type" label="参数名">
              <template #default="props">
                <ma-dict-tag :options="api_data_type" :value="props.row.data_type" />
              </template>
            </el-table-column>
            <el-table-column prop="updated_at" label="更新时间" />
          </el-table>

          <el-divider content-position="left">接口介绍</el-divider>
          <el-card>
            <div v-html="apiData.description ? apiData.description : '暂无介绍'"></div>
          </el-card>

          <el-divider content-position="left">返回示例</el-divider>
          <ma-highlight :code="apiData.response" lang="js" />
        </el-tab-pane>
        <el-tab-pane label="模拟请求" name="request">
          <sim-request :url="apiData.access_name" />
        </el-tab-pane>
      </el-tabs>
    </el-main>
  </el-drawer>
</template>

<script>
import simRequest from './simRequest'
import maHighlight from '@/components/maHighlight'
export default {
  components: {
    maHighlight,
    simRequest
  },
  data () {
    return {
      drawer: false,
      apiData: {},
      activeName: 'params',
      requestData: [],
      responseData: [],
      api_data_type: [],
    }
  },

  methods: {
    async open (data) {
      this.apiData = data
      this.responseData = []
      this.requestData = []
      await this.getColumnList()
      await this.getDictData()
      this.drawer = true
    },

    getColumnList() {
      this.$API.apiDoc.getColumnList(this.apiData.id).then(res => {
        res.data.api_column.map(item => {
          item.type === '0' ? this.requestData.push(item) : this.responseData.push(item)
        })
      })
    },

    // 获取字典数据
    getDictData() {
        this.getDict('api_data_type').then(res => {
          this.api_data_type = res.data
        })
    },
  }
}
</script>

<style scoped>
.lh {
  line-height: 48px;
}
</style>