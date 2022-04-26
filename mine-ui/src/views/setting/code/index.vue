<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          type="primary"
          plain
          icon="el-icon-download"
          v-auth="['setting:code:generate']"
          :disabled="selection.length < 1"
          @click="handleGenCodes"
        >生成代码</el-button>

        <el-button
          type="success"
          plain
          icon="el-icon-upload"
          v-auth="['setting:code:loadTable']"
          @click="$refs.tableList.show()"
        >装载数据表</el-button>

        <el-button
          type="danger"
          icon="el-icon-delete"
          v-auth="['setting:code:delete']"
          :disabled="selection.length < 1"
          @click="handleDeleteBatch"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.table_name" placeholder="表名称" clearable></el-input>

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
        @selection-change="selectionChange"
        stripe
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column
          label="表名称"
          prop="table_name"
          width="180"
        ></el-table-column>

        <el-table-column
          label="表描述"
          prop="table_comment"
          :show-overflow-tooltip="true"
        ></el-table-column>

        <el-table-column
          label="生成类型"
          prop="type"
          width="150"
        >
          <template #default="scope">
            <el-tag  v-if="scope.row.type === 'single'">单表CRUD</el-tag>
            <el-tag  v-if="scope.row.type === 'tree'">树表CRUD</el-tag>
            <el-tag  v-if="scope.row.type === 'parent_sub'">父子表CRUD</el-tag>
          </template>
        </el-table-column>

        <el-table-column
          label="创建时间"
          prop="created_at"
          width="160"
        />

        <el-table-column
          label="更新时间"
          prop="updated_at"
          width="160"
        />

        <el-table-column label="操作" fixed="right" align="right" width="130" >

          <template #default="scope">
            <el-button type="text"
              v-auth="['setting:code:preview']"
              @click="$refs.preview.show(scope.row.id)"
            >预览</el-button>

            <el-dropdown v-if="scope.row.username !== 'superAdmin'">

              <el-button type="text">更多</el-button>

              <template #dropdown>
                <el-dropdown-menu>

                  <el-dropdown-item
                    @click="edit(scope.row)"
                    v-if="$AUTH('setting:code:edit')"
                  >编辑</el-dropdown-item>

                  <el-dropdown-item
                    @click="handleSync(scope.row.id)"
                    v-if="$AUTH('setting:code:sync')"
                  >同步</el-dropdown-item>

                  <el-dropdown-item
                    @click="generateCode(scope.row.id)"
                    v-if="$AUTH('setting:code:generate')"
                  >生成代码</el-dropdown-item>

                  <el-dropdown-item
                    @click="handleDelete(scope.row.id)"
                    divided
                    v-if="$AUTH('setting:code:delete')"
                  >删除</el-dropdown-item>

                </el-dropdown-menu>
              </template>

            </el-dropdown>
          </template>

        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <table-list ref="tableList" @confirm="confirm" />

  <preview ref="preview" />

</template>

<script>
  import tableList from './table'
  import preview from './preview'

  export default {
    name: 'setting:code',
    components: {
      tableList,
      preview
    },

    data() {
      return {
        api: {
          list: this.$API.generate.getPageList
        },

        queryParams: {
          table_name: undefined,
        },

        selection: []
      }
    },
    methods: {

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      edit(row) {
        this.$router.push({ path: '/codeEdit', query: { id: row.id } })
      },

      confirm() {
        this.handleSuccess()
      },

      // 批量生成
      async handleGenCodes () {
        let ids = this.selection.map(item => item.id)
        this.$message.info('代码生成下载中，请稍后')
        this.generateCode(ids)
      },

      // 生成代码
      async generateCode (id) {
        this.$message.info('代码生成下载中，请稍后')
        await this.$API.generate.generate(id).then(res => {
          if (res.message && !res.success) {
            this.$message.error(res.message)
          } else {
            this.$TOOL.download(res)
            this.$message.success('代码生成成功')
          }
        })
      },

      // 批量删除
      async handleDeleteBatch () {
        if (this.selection.length > 0) {
          let ids = this.selection.map(item => item.id)
          await this.handleDelete(ids.join(','))
        } else {
          this.$message.error('请选择要删除的项')
        }
      },

      // 删除
      handleDelete (id) {
        this.$confirm('此操作会将数据物理删除？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$API.generate.deletes(id).then(res => {
            this.$message.success(res.message)
            this.handleSuccess()
          })
        })
      },

      // 同步数据表
      handleSync (id) {
        this.$confirm('此操作会导致字段设置信息丢失，确定同步吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$API.generate.sync(id).then(res => {
            res.success && this.$message.success(res.message)
            res.success || this.$message.error(res.message)
          })
        })
      },

      //搜索
      handlerSearch(){
        this.handleSuccess()
      },

      resetSearch() {
        this.queryParams = {
          table_name: undefined,
        }
        this.handleSuccess()
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      }
    }
  }
</script>

<style>
</style>
