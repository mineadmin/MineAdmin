<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          type="danger"
          plain
          row-key="id"
          icon="el-icon-delete"
          v-auth="['system:loginLog:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.username" clearable placeholder="请输入操作用户"></el-input>

          <el-tooltip class="item" effect="dark" content="搜索" placement="top">
            <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
            <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
          </el-tooltip>

          <el-popover placement="bottom-end" :width="450" trigger="click" >
            <template #reference>
              <el-button type="text" @click="povpoerShow = ! povpoerShow">
                更多筛选<i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
            </template>
            <el-form label-width="80px">

              <el-form-item label="业务名称" prop="service_name">
                <el-input v-model="queryParams.service_name" clearable placeholder="请输入业务名称"></el-input>
              </el-form-item>

              <el-form-item label="操作ID" prop="ip">
                <el-input v-model="queryParams.ip" clearable placeholder="请输入IP地址"></el-input>
              </el-form-item>

              <el-form-item label="登录时间">
                <el-date-picker
                  clearable
                  v-model="dateRange"
                  type="daterange"
                  range-separator="至"
                  @change="handleDateChange"
                  value-format="YYYY-MM-DD"
                  start-placeholder="开始日期"
                  end-placeholder="结束日期"
                ></el-date-picker>
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
        @selection-change="selectionChange"
        @row-click="rowClick"
        :params="{orderBy: 'created_at', orderType: 'desc'}"
        stripe
        remoteSort
        remoteFilter
        highlightCurrentRow
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column prop="username" label="操作用户"></el-table-column>

        <el-table-column prop="service_name" label="业务名称"></el-table-column>

        <el-table-column prop="router" label="路由" :show-overflow-tooltip="true"></el-table-column>

        <el-table-column prop="ip" label="IP"></el-table-column>

        <el-table-column prop="ip_location" label="登录地点"></el-table-column>

        <el-table-column prop="response_code" label="状态码" width="100"></el-table-column>

        <el-table-column prop="created_at" label="操作时间" width="160" ></el-table-column>

      </maTable>
    </el-main>

    <el-drawer v-model="infoDrawer" title="操作日志详情" :size="600" destroy-on-close>
      <info ref="info"></info>
    </el-drawer>

  </el-container>

</template>

<script>
  import info from './info'

  export default {
    name: 'system:operLog',

    components: {
			info
		},

    data() {
      return {
        povpoerShow: false,
        dateRange:'',
        infoDrawer: false,
        api: {
          list: this.$API.operLog.getPageList
        },
        selection: [],
        queryParams: {
          username: undefined,
          service_name: undefined,
          ip: undefined,
          maxDate: undefined,
          minDate: undefined,
          orderBy: 'created_at',
          orderType: 'desc',
        }
      }
    },
    methods: {

      rowClick(row){
				this.infoDrawer = true
				this.$nextTick(() => {
					this.$refs.info.setData(row)
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
          this.$API.operLog.deletes(ids.join(',')).then(() => {
            this.$refs.table.upData(this.queryParams)
          })
          loading.close()
          this.$message.success("操作成功")
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
          username: undefined,
          service_name: undefined,
          ip: undefined,
          maxDate: undefined,
          minDate: undefined,
          orderBy: 'created_at',
          orderType: 'desc',
        }
        this.$refs.table.upData(this.queryParams)
      }
    }
  }
</script>

<style>
</style>
