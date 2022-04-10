<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <!-- <el-button
          type="primary"
          icon="el-icon-plus"
          v-auth="['system:queueLog:produceStatus']"
          :disabled="selection.length==0"
          @click="batchDel"
        >重新加入队列</el-button> -->

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:queueLog:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          
          <el-input v-model="queryParams.queue_name" placeholder="队列名称" clearable></el-input>

          <el-tooltip class="item" effect="dark" content="搜索" placement="top">
            <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
            <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
          </el-tooltip>

          <el-popover placement="bottom-end" :width="450" trigger="click" >
            <template #reference>
              <el-button type="text" @click="povpoerShow = ! povpoerShow">
                更多筛选<el-icon><el-icon-arrow-down /></el-icon>
              </el-button>
            </template>
            <el-form label-width="100px">

            <el-form-item label="交换机名称" prop="exchange_name">
                <el-input v-model="queryParams.exchange_name" placeholder="交换机名称" clearable></el-input>
            </el-form-item>

            <el-form-item label="路由名称" prop="routing_key_name">
                <el-input v-model="queryParams.routing_key_name" placeholder="路由名称" clearable></el-input>
            </el-form-item>

            <el-form-item label="生产状态" prop="produce_status">
              <el-select v-model="queryParams.produce_status" style="width:100%" clearable placeholder="生产状态">
                  <el-option
                      v-for="(item, index) in queue_produce_status_data"
                      :key="index"
                      :label="item.label"
                      :value="item.value"
                  >{{item.label}}</el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="消费状态" prop="consume_status">
              <el-select v-model="queryParams.consume_status" style="width:100%" clearable placeholder="消费状态">
                  <el-option
                      v-for="(item, index) in queue_consume_status_data"
                      :key="index"
                      :label="item.label"
                      :value="item.value"
                  >{{item.label}}</el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="延迟时间" prop="delay_time">
                <el-input v-model="queryParams.delay_time" placeholder="延迟时间（秒）" clearable></el-input>
            </el-form-item>

            </el-form>
          </el-popover>
        </div>
      </div>
    </el-header>
    <el-main class="nopadding">
      <maTable
        ref="table"
        :api="api"
        row-key="id"
        :hidePagination="false"
        @row-click="rowClick"
        @selection-change="selectionChange"
        stripe
        remoteSort
        highlightCurrentRow
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column
           label="交换机名称"
           prop="exchange_name"
        />
        <el-table-column
           label="路由名称"
           prop="routing_key_name"
        />
        <el-table-column
           label="队列名称"
           prop="queue_name"
        />

				<el-table-column
					label="生产状态"
					prop="produce_status"
				>
					<template #default="scope">
						<ma-dict-tag :options="queue_produce_status_data" :value="scope.row.produce_status" />
					</template>

				</el-table-column>

        <el-table-column
           label="消费状态"
           prop="consume_status"
        >
					<template #default="scope">
						<ma-dict-tag :options="queue_consume_status_data" :value="scope.row.consume_status" />
					</template>
				</el-table-column>

        <el-table-column
           label="延迟时间（秒）"
           prop="delay_time"
        />

        <el-table-column label="操作" fixed="right" align="right" width="130">
          <template #default="scope">
            <el-button
              type="text"
              
              @click="deletes(scope.row.id)"
              v-auth="['system:queueLog:delete']"
            >删除</el-button>
          </template>
        </el-table-column>

      </maTable>
    </el-main>

    <el-drawer v-model="infoDrawer" title="队列日志详情" :size="600" destroy-on-close>
      <info ref="info"></info>
    </el-drawer>
  </el-container>

</template>

<script>
  import info from './info'
  export default {
    name: 'system:queueLog',

    components: { info },

    async created() {
        await this.getDictData();
    },

    data() {
      return {
        queue_produce_status_data: [],
        queue_consume_status_data: [],
        povpoerShow: false,
        infoDrawer: false,
        dateRange:'',
        api: { list: this.$API.queueLog.getPageList },
        selection: [],
        queryParams: {
          exchange_name: undefined,
          routing_key_name: undefined,
          queue_name: undefined,
          queue_content: undefined,
          log_content: undefined,
          produce_status: undefined,
          consume_status: undefined,
          delay_time: undefined,
        },
      }
    },
    methods: {
      rowClick(row){
        this.infoDrawer = true
        this.$nextTick(() => {
          this.$refs.info.setData(row)
        })
      },

      async deletes(id) {
        await this.$confirm(`确定删除该条日志吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          this.$API.queueLog.deletes(id).then(res => {
            if (res.success) {
              this.$refs.table.upData(this.queryParams)
              loading.close()
              this.$message.success("操作成功")
            }
          })
        })
      },

      //批量删除
      async batchDel(){
        await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          this.$API.queueLog.deletes(ids.join(',')).then(res=> {
            if (res.success) {
              this.$refs.table.upData(this.queryParams)
              loading.close()
              this.$message.success("操作成功")
            }
          })
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      // 选择时间事件
      handleDateChange (values) {
        if (values !== null) {
          this.queryParams.minDate = values[0]
          this.queryParams.maxDate = values[1]
        }
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      resetSearch() {
        this.queryParams = {
          exchange_name: undefined,
          routing_key_name: undefined,
          queue_name: undefined,
          queue_content: undefined,
          log_content: undefined,
          produce_status: undefined,
          consume_status: undefined,
          delay_time: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
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
