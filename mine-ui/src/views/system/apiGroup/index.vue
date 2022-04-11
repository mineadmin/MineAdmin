<template>
  <el-container>
    <el-header class="mine-el-header">
      <div class="panel-container">
        <div class="left-panel">
          <el-button
            icon="el-icon-plus"
            v-auth="['system:apiGroup:save']"
            type="primary"
            @click="add"
          >新增</el-button>

          <el-button
            type="danger"
            plain
            icon="el-icon-delete"
            v-auth="['system:apiGroup:delete']"
            :disabled="selection.length==0"
            @click="batchDel"
          >删除</el-button>

        </div>
        <div class="right-panel">
          <div class="right-panel-search">

            <el-input v-model="queryParams.name" placeholder="分组名称" clearable></el-input>

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
          <el-form-item label="状态" prop="status">
              <el-select v-model="queryParams.status" style="width:100%" clearable placeholder="状态">
                <el-option
                    v-for="(item, index) in data_status_data"
                    :key="index"
                    :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
              </el-select>
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
           label="分组名称"
           prop="name"
        />
        <el-table-column
           label="状态"
           prop="status"
        >
          <template #default="scope">
            <ma-dict-tag v-if="scope.row.status === '0'" :options="data_status_data" :value="scope.row.status" />
            <ma-dict-tag v-if="scope.row.status === '1'" type="danger" :options="data_status_data" :value="scope.row.status" />
          </template>
        </el-table-column>

        <el-table-column
           label="创建时间"
           prop="created_at"
        />

        <el-table-column
           label="更新时间"
           prop="updated_at"
        />

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['system:apiGroup:update']"
            >编辑</el-button>

            <el-button
              type="text"
              
              @click="deletes(scope.row.id)"
              v-auth="['system:apiGroup:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              
              v-auth="['system:apiGroup:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              
              v-auth="['system:apiGroup:realDelete']"
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
    name: 'system:apiGroup',
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
        
        data_status_data: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.apiGroup.getList,
          recycleList: this.$API.apiGroup.getRecycleList,
        },
        selection: [],
        queryParams: {
            
          name: undefined,
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
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          if (this.isRecycle) {
            this.$API.apiGroup.realDeletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          } else {
            this.$API.apiGroup.deletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.apiGroup.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.apiGroup.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.apiGroup.recoverys(id).then(res => {
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
        
          this.getDict('data_status').then(res => {
              this.data_status_data = res.data
          })
      }
    }
  }
</script>
