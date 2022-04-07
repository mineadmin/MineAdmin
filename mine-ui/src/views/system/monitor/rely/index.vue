<template>
  <el-container>
    <el-header>
      <div class="left-panel">
      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.name" clearable placeholder="请输入依赖名称"></el-input>

          <el-tooltip class="item" effect="dark" content="搜索" placement="top">
            <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
          </el-tooltip>

          <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
            <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
          </el-tooltip>

        </div>
      </div>
    </el-header>
    <el-main class="nopadding">
      <maTable
        ref="table"
        :api="api"
        stripe
        remoteSort
        remoteFilter
      >
        
        <el-table-column
          label="依赖名称"
          prop="name"
          width="260"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column
          label="版本号"
          prop="version"
          width="100"
        ></el-table-column>

        <el-table-column
          label="简介"
          prop="description"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column label="操作" fixed="right" align="right">
          <template #default="scope">

            <el-button
            type="text"
            v-auth="['system:monitor:relyDetail']"
            @click="handleDetail(scope.row.name)"
          >详细</el-button>
            
          </template>
        </el-table-column>

      </maTable>
    </el-main>

    <el-dialog
      title="依赖包详细"
      v-model="dialogVisible"
      destroy-on-close
      append-to-body
      @closed="dialogVisible = false"
      width="50%"
    >

      <el-table v-loading="detailsLoading" :data="details">
        <el-table-column prop="name" label="名称" width="120" fixed ></el-table-column>
        <el-table-column prop="value" label="值" :show-overflow-tooltip="true">
          <template #deault="scope">
            <div v-html="scope.row.value"></div>
          </template>
        </el-table-column>
      </el-table>

      <template #footer class="dialog-footer">
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
      </template>

    </el-dialog>

  </el-container>

</template>

<script>

  export default {
    name: 'system:monitor:rely',

    data() {
      return {
        dialog: {
          save: false
        },
        dialogVisible: false,
        detailsLoading: false,
        api: { list: this.$API.monitor.getPackagePageList },
        selection: [],
        // 依赖包详细
        details: [],
        queryParams: {
          name: undefined,
        },
        // 当前记录
        record: { url: '' }
      }
    },
    methods: {

      // 显示依赖详细
      handleDetail (name) {
        this.dialogVisible = true
        this.detailsLoading = true
        this.$API.monitor.getPackageDetail(name).then(res => {
          this.details = res.data
          this.detailsLoading = false
        })
      },

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      resetSearch() {
        this.queryParams = {
          name: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },
    },
  }
</script>

<style>
</style>
