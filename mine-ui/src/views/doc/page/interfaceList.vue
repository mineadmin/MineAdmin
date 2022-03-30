<template>
  <el-card shadow="never" class="decs">
    <h2>{{ appInfo.app_name }}</h2>
    <div class="decs-list">
      <el-divider content-position="left">最后更新时间</el-divider>
      <p>{{ appInfo.updated_at }}</p>
      <el-divider content-position="left">应用介绍</el-divider>
      <div class="description" v-html="appInfo.description"></div>
    </div>
  </el-card>
  <el-card shadow="never" class="card" style="margin-top: 10px;">
    <el-button type="primary" @click="openGlobalParams">设置全局参数</el-button>
    <el-button type="danger" @click="clearGlobalParams">清除全局参数</el-button>
    <el-button type="warning" @click="getIdentity">生成简易模式Identity</el-button>
    <el-button type="warning" @click="getAccessToken">生成复杂模式AccessToken</el-button>
    <el-collapse v-model="activeName" accordion style="margin-top: 15px;">
      <el-collapse-item
        :title="item.name"
        :name="index.toString()" v-for="(item, index) in appInfo.apis"
        :key="index"
      >
        <div class="miaoshu">
          <el-tooltip content="点击复制">
            <el-tag
              size="large"
              class="interface-url"
              @click="async () => {
                try{
                  await this.clipboard(`/api/v1/${item.access_name}`)
                  $message.success(this.$t('sys.copy_success'))
                } catch(e) {
                  $message.error(this.$t('sys.copy_fail'))
                }
              }">
              /api/v1/{{ item.access_name }}
            </el-tag>
          </el-tooltip>
          <el-tag type="success" size="large" v-if="item.request_mode === 'A' ">ALL</el-tag>
          <el-tag type="warning" size="large" v-if="item.request_mode === 'P' ">POST</el-tag>
          <el-tag type="danger" size="large" v-if="item.request_mode === 'P' ">PUT</el-tag>
          <el-tag type="danger" size="large" v-if="item.request_mode === 'D' ">DELETE</el-tag>
          <el-button type="primary" class="details" @click="details(item)">查看详情</el-button>
        </div>
        <div>
          <el-divider content-position="left">更新时间</el-divider>
          <p>{{ item.updated_at }}</p>
        </div>
      </el-collapse-item>
    </el-collapse>
  </el-card>

  <details-page ref="details" />

  <global-params ref="GlobalParams" />
</template>

<script>
import DetailsPage from './components/details'
import GlobalParams from './components/globalParams'
import { request } from '@/utils/request.js'
export default {
  components: {
    GlobalParams,
    DetailsPage
  },
  async created() {
    await this.getAppInfo()
  },
  data () {
    return {
      activeName: '0',
      appInfo: {},
      dialogVisible: false,
    }
  },
  methods: {
    async getAppInfo() {
      let res = await this.$API.apiDoc.getAppAndInterfaceList(this.$TOOL.data.get('appId'))
      this.appInfo = res.data
    },

    details(row) {
      this.$nextTick(() => {
        this.$refs.details.open(row)
      })
    },

    genSign() {
      let timestamp = Date.now()
      let obj = {
        app_id: this.appInfo.app_id,
        timestamp,
        app_secret: this.appInfo.app_secret,
        sign_ver: "1.0",
      }

      let keysSorted = Object.keys(obj).sort((x, y) => {
        if(x < y) return 1
        else if(x > y) return -1
        else return 0
      })

      let newObj = {}

      for (let i=0; i < keysSorted.length; i++) {
        newObj[keysSorted[i]] = obj[keysSorted[i]];
      }

      let signature = this.$TOOL.crypto.MD5(this.$TOOL.httpBuild(newObj))

      return {
        app_id: this.appInfo.app_id,
        signature,
        timestamp,
      }

    },

    async getIdentity() {
      try{
        await this.clipboard(this.$TOOL.crypto.MD5(this.appInfo.app_id + this.appInfo.app_secret))
        this.$message.success('生成成功，已复制到剪切板')
      } catch(e) {
        this.$message.error(this.$t('sys.copy_fail'))
      }
    },

    getAccessToken() {
      let url = '/api/v1/getAccessToken?' + this.$TOOL.httpBuild(this.genSign())
      
      request({ url, method: 'post' }).then(async res => {
        if (res.success) {
          try{
            await this.clipboard(res.data.access_token)
            this.$message.success('获取成功，已复制到剪切板')
          } catch(e) {
            this.$message.error(this.$t('sys.copy_fail'))
          }
        } else {
          this.$message.error(res.message)
        }
      }).catch(e => {
        this.$message.error(e)
      })
    },

    openGlobalParams() {
      this.$nextTick(() => {
        this.$refs.GlobalParams.open()
      })
    },

    clearGlobalParams() {
      this.$TOOL.session.remove('globalParams')
      this.$message.success('清除成功')
    },
  }

}
</script>

<style scoped lang="scss">
:deep(.el-dialog__body) {
  padding-top: 0 !important;
}
.decs {
  /* background: linear-gradient(160deg, #fff, #effbff, #dcf6ff); */
  font-size: 14px;
}
.decs-list p {
  line-height: 25px;
}
.decs-list .description {
  line-height: 25px;
}
.interface-url {
  cursor: pointer;
}
.miaoshu {
  position: relative;
}
.details {
  position: relative; top: -1px; margin-left: 30px;
}
</style>