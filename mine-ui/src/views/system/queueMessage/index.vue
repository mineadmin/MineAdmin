<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:SystemQueueMessage:save']"
          type="primary"
          @click="add"
        >新增</el-button>
      </div>
      <div class="right-panel">
        <div class="right-panel-search">

          <el-input v-model="queryParams.send_by" placeholder="发送人ID" clearable></el-input>

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
            <el-form label-width="80px">

            <el-form-item label="发送人ID" prop="send_by">
                <el-input v-model="queryParams.send_by" placeholder="发送人ID" clearable></el-input>
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
        :column="column"
        :showRecycle="true"
        row-key="id"
        :hidePagination="false"
        @selection-change="selectionChange"
        @switch-data="switchData"
        stripe
        remoteSort
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column
           label="发送人"
           prop="send_name"
        />

				<el-table-column
					label="发送状态"
					prop="send_status"
				>
					<template #default="scope">
						<ma-dict-tag  :options="message_send_status_data" :value="scope.row.send_status" />
					</template>

				</el-table-column>

				<el-table-column
					label="查看状态"
					prop="read_status"
				>
					<template #default="scope">
						<ma-dict-tag  :options="message_read_status_data" :value="scope.row.read_status" />
					</template>

				</el-table-column>

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
							@click="logs(scope.row)"
            >详情</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['system:SystemQueueMessage:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['system:SystemQueueMessage:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>

	<el-drawer title="消息日志" v-model="dialog.logsVisible" :size="1000" direction="rtl" destroy-on-close>
		<logs ref="messageList" />
	</el-drawer>
</template>

<script>
  import saveDialog from './save'
	import logs from './logs'

  export default {
    name: 'system:SystemQueueMessage',
    components: {
      saveDialog,
			logs
    },

    async created() {
        await this.getDictData();
    },

    data() {
      return {
        dialog: {
          save: false
        },

        message_send_status_data: [],
        message_read_status_data: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.systemQueueMessage.getList,
          recycleList: this.$API.systemQueueMessage.getRecycleList,
        },
        selection: [],
        queryParams: {

          content_type: undefined,
          content: undefined,
          receive_by: undefined,
          send_by: undefined,
          send_status: undefined,
          read_status: undefined,
        },
        isRecycle: false,
      }
    },
    methods: {

      //添加
      add(){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open()
        })
      },

      //编辑
      tableEdit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').setData(row)
        })
      },

      //查看
      tableShow(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('show').setData(row)
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
          if (this.isRecycle) {
            this.$API.systemQueueMessage.realDeletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          } else {
            this.$API.systemQueueMessage.deletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.systemQueueMessage.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.systemQueueMessage.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.systemQueueMessage.recoverys(id).then(res => {
          this.$message.success(res.message)
          this.$refs.table.upData(this.queryParams)
        })
      },

			// 消息日志
			logs(row){
				this.dialog.logsVisible = true
				this.$nextTick(() => {
					this.$refs.messageList.setData(row)
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

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      resetSearch() {
        this.queryParams = {

          content_type: undefined,
          content: undefined,
          receive_by: undefined,
          send_by: undefined,
          send_status: undefined,
          read_status: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      },

      // 获取字典数据
      getDictData() {

          this.getDict('message_send_status').then(res => {
              this.message_send_status_data = res.data
          })
          this.getDict('message_read_status').then(res => {
              this.message_read_status_data = res.data
          })
      }
    }
  }
</script>
