<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:dept:save']"
          type="primary"
          @click="add"
        >新增</el-button>

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:dept:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

        <el-button
          type="info"
          plain
          icon="el-icon-sort"
          @click="handleExpand"
        > {{ isExpand ? '折叠' : '展开' }} </el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.name" placeholder="部门名称" clearable></el-input>

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

              <el-form-item label="负责人" prop="code">
                <el-input v-model="queryParams.code" placeholder="负责人" clearable></el-input>
              </el-form-item>

              <el-form-item label="状态" prop="status">
                <el-select  v-model="queryParams.status" style="width:100%" clearable placeholder="状态">
                  <el-option label="启用" value="0">启用</el-option>
                  <el-option label="停用" value="1">停用</el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="创建时间">
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
        v-if="refreshTable"
        :api="api"
        :showRecycle="true"
        row-key="id"
        :hidePagination="true"
        :default-expand-all="isExpand"
        @selection-change="selectionChange"
        @switch-data="switchData"
        stripe
        remoteSort
      >
        <el-table-column type="selection" width="50"></el-table-column>
        
        <el-table-column
          label="部门名称"
          prop="name"
          sortable='custom'
          width="260"
        ></el-table-column>
        
        <el-table-column
          label="负责人"
          prop="leader"
          width="160"
        ></el-table-column>

        <el-table-column
          label="电话"
          prop="phone"
          width="160"
        ></el-table-column>

        <el-table-column
          label="排序"
          prop="sort"
          sortable='custom'
          width="150"
        ></el-table-column>

        <el-table-column
          label="状态"
          prop="status"
          width="150"
        >
          <template #default="scope">
            <el-switch
              v-model="scope.row.status"
              @change="handleStatus($event, scope.row)"
              active-value="0"
              inactive-value="1"
            ></el-switch>
          </template>
        </el-table-column>

        <el-table-column
          label="创建时间"
          prop="created_at"
          width="200"
          sortable='custom'
        ></el-table-column>

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="180" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              
              @click="tableShow(scope.row, scope.$index)"
              v-auth="['system:dept:read']"
            >查看</el-button>

            <el-button
              type="text"
              
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['system:dept:update']"
            >编辑</el-button>

            <el-button
              type="text"
              
              @click="deletes(scope.row.id)"
              v-auth="['system:dept:delete']"
            >删除</el-button>
            
          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              v-auth="['system:dept:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              v-auth="['system:dept:realDelete']"
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
    name: 'system:dept',
    components: {
      saveDialog
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
          list: this.$API.dept.getList,
          recycleList: this.$API.dept.getRecycleList,
        },
        selection: [],
        queryParams: {
          name: undefined,
          code: undefined,
          maxDate: undefined,
          minDate: undefined,
          status: undefined
        },
        isRecycle: false,
        isExpand: true,
        refreshTable: true,
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
        }).then(async () => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          let res
          if (this.isRecycle) {
            res = await this.$API.dept.realDeletes(ids.join(',')).then()
          } else {
            res = await this.$API.dept.deletes(ids.join(',')).then()
          }
          loading.close();
          if (res.success) {
            this.$message.success(res.message)
            this.$refs.table.upData(this.queryParams)
          }
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          let res
          if (this.isRecycle) {
            res = await this.$API.dept.realDeletes(id).then()
          } else {
            res = await this.$API.dept.deletes(id).then()
          }
          this.$refs.table.upData(this.queryParams)
          loading.close();
          if (res.success) {
            this.$message.success(res.message)
            this.$refs.table.upData(this.queryParams)
          }
        }).catch(()=>{})
      },

      handleExpand() {
        this.refreshTable = false
        this.isExpand = !this.isExpand
        this.$nextTick(()=> {
          this.refreshTable = true
        })
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.dept.recoverys(id).then(res => {
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

      //搜索
      handlerSearch(){
        this.$refs.table.upData(this.queryParams)
      },

      // 切换数据类型回调
      switchData(isRecycle) {
        this.isRecycle = isRecycle
      },

      // 状态更改
      handleStatus (val, row) {
        const status = row.status === '0' ? '0' : '1'
        const text = row.status === '0' ? '启用' : '停用'
        this.$confirm(`确认要${text} ${row.name} 部门吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(() => {
          this.$API.dept.changeStatus({ id: row.id, status }).then(() => {
            this.$message.success(text + '成功')
          })
        }).catch(() => {
          row.status = row.status === '0' ? '1' : '0'
        })
      },

      resetSearch() {
        this.queryParams = {
          name: undefined,
          code: undefined,
          maxDate: undefined,
          minDate: undefined,
          status: undefined
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      }
    }
  }
</script>

<style scoped>
</style>
