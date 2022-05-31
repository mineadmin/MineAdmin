<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-magic-stick"
          :disabled="selection.length==0"
          v-auth="['system:dataMaintain:optimize']"
          @click="handleOptimize"
        >优化表</el-button>

        <el-button
          icon="el-icon-delete"
          :disabled="selection.length==0"
          v-auth="['system:dataMaintain:fragment']"
          @click="handleClear"
          >清理碎片</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.name" clearable placeholder="请输入表名"></el-input>

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
        rowKey="name"
        :column="column"
        @selection-change="selectionChange"
        stripe
        remoteSort
        remoteFilter
      >
        <el-table-column type="selection" width="50"></el-table-column>
        
        <el-table-column
          label="表名称"
          prop="name"
          width="180"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column
          label="表引擎"
          prop="engine"
        ></el-table-column>

        <el-table-column
          label="总行数"
          prop="rows"
        ></el-table-column>

        <el-table-column
          label="碎片大小"
          prop="data_free"
        >
          <template #default="scope">
            {{ $TOOL.formatSize(scope.row.data_free) }}
          </template>
        </el-table-column>

        <el-table-column
          label="数据大小"
          prop="data_length"
        >
          <template #default="scope">
            {{ $TOOL.formatSize(scope.row.data_length) }}
          </template>
        </el-table-column>

        <el-table-column
          label="索引大小"
          prop="index_length"
        >
          <template #default="scope">
            {{ $TOOL.formatSize(scope.row.index_length) }}
          </template>
        </el-table-column>

        <el-table-column
          label="字符集"
          prop="collation"
          width="180"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column
          label="创建时间"
          prop="created_at"
          width="160"
        >
          <template #default="scope">
            {{scope.row.create_time.substr(0, 10)}}
          </template>
        </el-table-column>

        <el-table-column
          prop="comment"
          label="表注释"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column label="操作" fixed="right" align="right">
          <template #default="scope">

            <el-button
            type="primary" link
            v-auth="['system:dataMaintain:detailed']"
            @click="handleDetail(scope.row.name)"
          >查看</el-button>
            
          </template>
        </el-table-column>

      </maTable>
    </el-main>

    <el-dialog
      title="表结构数据"
      v-model="dialogVisible"
      destroy-on-close
      append-to-body
      @closed="dialogVisible = false"
      width="50%"
    >

      <el-table :data="columnList" stripe>

        <el-table-column prop="column_name" label="字段名称">
        </el-table-column>

        <el-table-column prop="column_type" label="字段类型">
        </el-table-column>

        <el-table-column prop="column_comment" label="字段注释" :show-overflow-tooltip="true">
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
    name: 'system:dataMaintain',

    data() {
      return {
        dialog: {
          save: false
        },
        dialogVisible: false,
        column: [],
        api: { list: this.$API.dataMaintain.getPageList },
        selection: [],
        queryParams: {
          name: undefined,
        },
        // 当前记录
        record: { url: '' }
      }
    },
    methods: {

      // 显示表字段
      handleDetail (name) {
        this.$API.dataMaintain.getDetailed(name).then(res => {
          this.columnList = res.data
          this.dialogVisible = true
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      // 优化表
      handleOptimize () {
        this.$API.dataMaintain.optimize({ tables: this.names }).then(res => {
          res.success && this.$message.success(res.message)
        })
      },

      // 清理表碎片
      handleClear () {
        this.$API.dataMaintain.fragment({ tales: this.names }).then(res => {
          res.success && this.$message.success(res.message)
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

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      }
    },
  }
</script>

<style>
</style>
