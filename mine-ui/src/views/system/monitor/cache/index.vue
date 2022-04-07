<template>
  <div
    class="diy-grid-layout"
  >
    <el-row :gutter="15">
      <el-col :md="24" :xs="24">
        <el-card shadow="hover">
          <template #header>
            <span>Redis信息</span>
          </template>
          <div class="table">
            <el-row :gutter="15">
              <el-col :span="18">
                <table cellspacing="0" style="width: 100%;">
                  <tbody>
                    <tr>
                      <td><div class="cell">Redis版本</div></td>
                      <td><div class="cell">{{ server.version }}</div></td>
                      <td><div class="cell">客户端连接数</div></td>
                      <td><div class="cell">{{ server.clients }}</div></td>
                    </tr>
                    <tr>
                      <td><div class="cell">运行模式</div></td>
                      <td><div class="cell">{{ server.redis_mode }}</div></td>
                      <td><div class="cell">运行天数</div></td>
                      <td><div class="cell">{{ server.run_days }}</div></td>
                    </tr>
                    <tr>
                      <td><div class="cell">端口</div></td>
                      <td><div class="cell">{{ server.port }}</div></td>
                      <td><div class="cell">AOF状态</div></td>
                      <td><div class="cell">{{ server.aof_enabled }}</div></td>
                    </tr>
                    <tr>
                      <td><div class="cell">已过期key</div></td>
                      <td><div class="cell">{{ server.expired_keys }}</div></td>
                      <td><div class="cell">系统使用key</div></td>
                      <td><div class="cell">{{ server.sys_total_keys }}</div></td>
                    </tr>
                  </tbody>
                </table>
              </el-col>

              <el-col :span="6" class="cache-col">
                <el-progress type="dashboard" :percentage="100" :stroke-width="8" :width="170" status="success">
                  <template #default>
                    <span class="percentage-value">{{ server.use_memory }}</span>
                    <span class="percentage-label">Memory</span>
                  </template>
                </el-progress>
              </el-col>
            </el-row>
          </div>
        </el-card>

        <el-card shadow="hover">
          <template #header>
            <span>其他信息</span>
            <el-button style="float: right;" type="primary" icon="el-icon-refresh" @click="clear">清空缓存</el-button>
          </template>
          <div class="table">
            <el-row :gutter="25">
              <el-col :span="16">
                <table cellspacing="0" style="width: 100%;">
                  <thead>
                    <tr>
                      <td><div class="cell" width="5%">&nbsp;</div></td>
                      <td><div class="cell">缓存名称</div></td>
                      <td><div class="cell" width="130" style="text-align:center">操作</div></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(key, index) in keys" :key="index">
                      <td><div class="cell" width="5%">{{ index + 1 }}</div></td>
                      <td><div class="cell">{{ key }}</div></td>
                      <td><div class="cell" width="130" style="text-align:center">
                        <el-button @click="deleteKey(key)"><el-icon><el-icon-delete /></el-icon></el-button>
                        <el-button @click="view(key)"><el-icon><el-icon-view /></el-icon></el-button>
                      </div></td>
                    </tr>
                  </tbody>
                </table>
              </el-col>
              <el-col :span="8">
                <div style="line-height: 38px; font-size: 14px;">Key详情</div>
                <el-input type="textarea" :rows="20" v-model="keyContent"  />
              </el-col>
            </el-row>
          </div>
        </el-card>
      </el-col>
    </el-row>
	</div>
</template>
<script>
import useTabs from '@/utils/useTabs'
import scEcharts from '@/components/scEcharts';
export default {
  name: 'system:cache',

  components: {
    scEcharts
  },

  data () {
    return {
      server: {},
      keys: [],
      keyContent: '',
    }
  },

  async mounted () {
    await this.getService()
  },

  methods: {
    async getService () {
      await this.$API.monitor.getCacheInfo().then(res => {
        this.server = res.data.server
        this.keys   = res.data.keys
      })
    },

    // 查看key 
    view (key) {
      this.$API.monitor.view({ key }).then(res => {
        if (res.success) {
          this.keyContent = res.data.content
        }
      })
    },

    // 删除一个缓存
    deleteKey(key) {
      this.$API.monitor.deleteKey({ key }).then(res => {
        if (res.success) {
          this.$message.success(res.message)
          useTabs.refresh()
        }
      })
    },

    // 清空缓存
    clear() {
      this.$API.monitor.clear().then(res => {
        if (res.success) {
          this.$message.success(res.message)
          useTabs.refresh()
        }
      })
    },
  }
}
</script>

<style scoped lang="scss">
.diy-grid-layout {
  padding: 15px;
}
.progress {
  left: 50%;
  margin-left: -120px;
}
:deep(.el-progress__text span) {
  font-size: 32px;
}
.table tr {
  font-size: 14px;
  color: #606266;

}
.table td {
  box-sizing:border-box;
  text-overflow:ellipsis;
  text-align:left;
  vertical-align:middle;
  position:relative;
  border-bottom: 1px solid #ebeef5;
  padding: 10px 0;
}

.percentage-value {
  display: block;
  font-size: 24px;
  font-weight: bold;
}

.percentage-value em {
  font-size: 14px;
  font-style: normal;
  margin-left: 5px;
  font-weight: normal;
}

.percentage-label {
  font-size: 14px !important;
  line-height: 25px !important;
}
.cache-col {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
[data-theme='dark'] { 
  .table tbody tr { color: #ddd; }

  .table tbody td, .table thead td {border-bottom: 1px solid #585858;}
}
</style>
