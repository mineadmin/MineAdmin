<template>
  <el-main style="padding:0 20px;">
    <el-descriptions :column="1" border>
      <el-descriptions-item label="交换机名称">{{ data.exchange_name }}</el-descriptions-item>
      <el-descriptions-item label="路由名称">{{ data.routing_key_name }}</el-descriptions-item>
      <el-descriptions-item label="队列名称">{{ data.queue_name }}</el-descriptions-item>
      <el-descriptions-item label="生产状态">
        <ma-dict-tag :options="queue_produce_status_data" :value="data.produce_status" />
      </el-descriptions-item>
      <el-descriptions-item label="消费状态">
        <ma-dict-tag :options="queue_consume_status_data" :value="data.consume_status" />
      </el-descriptions-item>
      <el-descriptions-item label="延迟时间（秒）">{{ data.delay_time }}</el-descriptions-item>
      <el-descriptions-item label="创建时间">{{ data.created_at }}</el-descriptions-item>
    </el-descriptions>
    <el-collapse v-model="activeNames" style="margin-top: 20px;" accordion>
      <el-collapse-item title="队列数据" name="request">
        <ma-highlight :code="data.queue_content" lang="json" />
      </el-collapse-item>
      <el-collapse-item title="队列日志" name="response">
          <pre>{{ data.log_content == null ? "无日志数据" : data.log_content }}</pre>
      </el-collapse-item>
    </el-collapse>
  </el-main>
</template>

<script>
import maHighlight from '@/components/maHighlight'
import MaDictTag from '../../../components/maDictTag/index.vue'
export default {
  components: {
    maHighlight,
    MaDictTag
},
  data() {
    return {
      data: {},
      queue_produce_status_data: [],
      queue_consume_status_data: [],
      activeNames: ['request'],
    }
  },
  methods: {
    async setData(data) {
      await this.getDictData()
      this.data = data
    },
    // 获取字典数据
    getDictData() {
      this.getDict('queue_produce_status').then(res => {
        this.queue_produce_status_data = res.data
      })
      this.getDict('queue_consume_status').then(res => {
        this.queue_consume_status_data = res.data
      })
    }
  }
}
</script>
<style scoped>
pre {
  font-size: 12px;
  color: #ccc;
  padding: 20px;
  background: #333;
  font-family: consolas;
  line-height: 1.5;
  overflow: auto;
  border-radius: 4px;
}
</style>