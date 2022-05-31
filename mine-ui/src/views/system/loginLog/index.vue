<template>
  <el-container>
    <el-header class="mine-el-header">
      <div class="panel-container">
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
            <el-input v-model="queryParams.username" clearable placeholder="请输入登录用户"></el-input>

            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
              <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
              <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
            </el-tooltip>

            <el-button type="primary" link @click="toggleFilterPanel">
              {{ povpoerShow ? '关闭更多筛选' : '显示更多筛选'}}
              <el-icon><el-icon-arrow-down v-if="povpoerShow" /><el-icon-arrow-up v-else /></el-icon>
            </el-button>
          </div>
        </div>
      </div>

      <el-card class="filter-panel" shadow="never">
        <el-form label-width="80px" :inline="true">
          <el-form-item label="登录IP" prop="ip">
            <el-input v-model="queryParams.ip" clearable placeholder="请输入登录IP"></el-input>
          </el-form-item>

          <el-form-item label="状态" prop="status">
            <el-select v-model="queryParams.status" placeholder="登录状态">
              <el-option label="成功" value="0">成功</el-option>
              <el-option label="失败" value="1">失败</el-option>
            </el-select>
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
      </el-card>
    </el-header>
    <el-main class="nopadding">
      <maTable
        ref="table"
        :api="api"
        @selection-change="selectionChange"
        :column="column"
        :params="{orderBy: 'login_time', orderType: 'desc'}"
        stripe
        remoteSort
        remoteFilter
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <template #status="scope">
          <div class="ma-state ma-state-1 ma-status-processing" v-if="scope.row.status === '0'"></div>
          <div class="ma-state ma-state-2 ma-status-processing" v-else></div>
        </template>

      </maTable>
    </el-main>

  </el-container>

</template>

<script>

  export default {
    name: 'system:loginLog',

    data() {
      return {
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.loginLog.getPageList
        },
        selection: [],
        queryParams: {
          username: undefined,
          status: undefined,
          ip: undefined,
          maxDate: undefined,
          minDate: undefined,
          orderBy: 'login_time',
          orderType: 'desc',
        },
        column: [
					{
						label: "ID",
						prop: "id",
						width: "100",
						sortable: true,
						hide: true,
					},{
						label: "登录用户",
						prop: "username",
						width: "150"
					},{
						label: "状态",
						prop: "status",
						width: "100"
					},{
						label: "登录IP",
						prop: "ip",
						width: "150"
					},{
						label: "登录地点",
						prop: "ip_location",
						width: "150"
					},{
						label: "操作系统",
						prop: "os",
						width: "150"
					},{
						label: "浏览器",
						prop: "browser",
						width: "150"
					},{
						label: "登录信息",
						prop: "message",
						width: "180"
					},{
						label: "登录时间",
						prop: "login_time",
						width: "160",
						sortable: true
					}
				],
      }
    },
    methods: {

      //批量删除
      async batchDel(){
        await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          this.$API.loginLog.deletes(ids.join(',')).then(() => {
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

      toggleFilterPanel() {
        this.povpoerShow = ! this.povpoerShow
        document.querySelector('.filter-panel').style.display = this.povpoerShow ? 'block' : 'none'
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      resetSearch() {
        this.queryParams = {
          username: undefined,
          status: undefined,
          ip: undefined,
          maxDate: undefined,
          minDate: undefined,
          orderBy: 'login_time',
          orderType: 'desc',
        }
        this.$refs.table.upData(this.queryParams)
      }
    }
  }
</script>

<style>
</style>
