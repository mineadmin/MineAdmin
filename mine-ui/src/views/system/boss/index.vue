<template>
  <el-container>
    <el-header class="mine-el-header">
      <div class="panel-container">
        <div class="left-panel">
          <el-button
            icon="el-icon-plus"
            v-auth="['system:boss:save']"
            type="primary"
            @click="add"
          >新增</el-button>

          <el-button
            type="danger"
            plain
            icon="el-icon-delete"
            v-auth="['system:boss:delete']"
            :disabled="selection.length==0"
            @click="batchDel"
          >删除</el-button>

        </div>
        <div class="right-panel">
          <div class="right-panel-search">

            <el-input v-model="queryParams.name" placeholder="老板名称" clearable></el-input>

            <el-tooltip class="item" effect="dark" content="搜索" placement="top">
              <el-button type="primary" icon="el-icon-search" @click="handlerSearch"></el-button>
            </el-tooltip>

            <el-tooltip class="item" effect="dark" content="清空条件" placement="top">
              <el-button icon="el-icon-refresh" @click="resetSearch"></el-button>
            </el-tooltip>

            <el-button type="text" @click="toggleFilterPanel">
              {{ povpoerShow ? '关闭更多筛选' : '显示更多筛选'}}
              <el-icon><el-icon-arrow-down v-if="povpoerShow" /><el-icon-arrow-up v-else /></el-icon>
            </el-button>
          </div>
        </div>
      </div>
      <el-card class="filter-panel" shadow="never">
        <el-form label-width="80px" :inline="true">
          
            <el-form-item label="老板代码" prop="code">
                <el-input v-model="queryParams.code" placeholder="老板代码" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="排序" prop="sort">
                <el-input v-model="queryParams.sort" placeholder="排序" clearable></el-input>
            </el-form-item>
        
            <el-form-item label="状态 (0正常 1停用)" prop="status">
                <el-input v-model="queryParams.status" placeholder="状态 (0正常 1停用)" clearable></el-input>
            </el-form-item>
        
        </el-form>
      </el-card>
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
           label="老板名称"
           prop="name"
        />
        <el-table-column
           label="老板代码"
           prop="code"
        />
        <el-table-column
           label="排序"
           prop="sort"
        />
        <el-table-column
           label="状态 (0正常 1停用)"
           prop="status"
        />

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['system:boss:update']"
            >编辑</el-button>

            <el-button
              type="text"
              size="small"
              @click="deletes(scope.row.id)"
              v-auth="['system:boss:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['system:boss:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['system:boss:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>

</template>

<script>
  import saveDialog from './save'

  export default {
    name: 'system:boss',
    components: {
      saveDialog
    },

    async created() {
        await this.getDictData();
    },

    data() {
      return {
        dialog: {
          save: false
        },
        
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.systemBoss.getList,
          recycleList: this.$API.systemBoss.getRecycleList,
        },
        selection: [],
        queryParams: {
            
          name: undefined,
          code: undefined,
          sort: undefined,
          status: undefined,
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
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消',
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          if (this.isRecycle) {
            this.$API.systemBoss.realDeletes(ids.join(',')).then(res => {
              if(res.success) {
                this.$message.success(res.message)
                this.$refs.table.upData(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
          } else {
            this.$API.systemBoss.deletes(ids.join(',')).then(res => {
              if(res.success) {
                this.$message.success(res.message)
                this.$refs.table.upData(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
          }
          loading.close();

        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消',
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.systemBoss.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.systemBoss.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.systemBoss.recoverys(id).then(res => {
          this.$message.success(res.message)
          this.$refs.table.upData(this.queryParams)
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

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      resetSearch() {
        this.queryParams = {
          
          name: undefined,
          code: undefined,
          sort: undefined,
          status: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      },

      // 获取字典数据
      getDictData() {
        
      },

      // 标签页查询
      handleTabsClick(tab, event) {
      	
      }
    }
  }
</script>
<style>
.el-tabs__item {
	padding: 0 20px;
	height: 44px;
	box-sizing: border-box;
	/* line-height: 40px; */
	display: inline-block;
	list-style: none;
	font-size: 14px;
	font-weight: 500;
	color: var(--el-text-color-primary);
	position: relative;
	margin-top: 10px;
}
</style>