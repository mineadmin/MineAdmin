<template>
  <el-dialog
    append-to-body
    title="装载数据表"
    v-model="dialogVisible"
    width="800px"
    :before-close="handleDialogClose"
  >
    <el-form
      :inline="true"
      ref="queryParams"
      :model="queryParams"
      label-width="40px"
    >
      <el-form-item label="表名" class="ma-inline-form-item" prop="name">
        <el-input  v-model="queryParams.name" placeholder="请输入表名"></el-input>
      </el-form-item>

      <el-form-item class="ma-inline-form-item">
        <el-button  type="primary" @click="handleSearch" icon="el-icon-search">搜索</el-button>
        <el-button  type="default" @click="resetSearch" icon="el-icon-refresh">重置</el-button>
      </el-form-item>

    </el-form>

    <maTable
      ref="table"
      :api="api"
      rowKey="name"
      @selection-change="selectionChange"
      :reserve-selection="true"
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
        prop="comment"
        label="表注释"
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

    </maTable>

    <template #footer class="dialog-footer">
      <el-button @click="handleDialogClose" >关 闭</el-button>
      <el-button type="primary" @click="loadTable" >确 定</el-button>
    </template>
  </el-dialog>
</template>
<script>
export default {
  emits: ['confirm'],

  data () {
    return {

      api: { list: this.$API.dataMaintain.getPageList },
      // 搜索
      queryParams: {
        name: undefined
      },
      // modal
      dialogVisible: false,
      // 选择的表名
      names: []
    }
  },

  methods: {

    // 显示表分页
    show () {
      this.dialogVisible = true
    },

    // 装载数据表
    loadTable () {
      this.$API.generate.loadTable({ names: this.names }).then(res => {
        if (res.success) {
          this.handleDialogClose()
          this.$emit('confirm')
        }
      })
    },

    // 表字段modal关闭
    handleDialogClose () {
      this.dialogVisible = false
    },

    // 多选
    selectionChange (items) {
      if (items.length > 0) {
        this.names = items.map(item => {
          return { name: item.name, comment: item.comment }
        })
      } else {
        this.names = []
      }
    },

    // 搜索
    handleSearch () {
      this.$refs.table.upData(this.queryParams)
    },

    // 重置搜索
    resetSearch () {
      this.$refs.queryParams.resetFields()
      this.handleSearch()
    }
  }
}
</script>
