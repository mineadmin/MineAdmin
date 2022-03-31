<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:notice:save']"
          type="primary"
          @click="add"
        >新增</el-button>

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:notice:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">

          <el-input v-model="queryParams.title" placeholder="标题" clearable></el-input>

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
              
            <el-form-item label="公告类型" prop="type">
                        
            <el-select v-model="queryParams.type" style="width:100%" clearable placeholder="公告类型">
                <el-option
                    v-for="(item, index) in backend_notice_type_data"
                    :key="index"
                    :label="item.label"
                    :value="item.value"
                >{{item.label}}</el-option>
            </el-select>
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
           label="标题"
           prop="title"
        />
        <el-table-column
           label="公告类型"
           prop="type"
        >
          <template #default="scope">
            <ma-dict-tag :options="backend_notice_type_data" :value="scope.row.type" />
          </template>
        </el-table-column>

        <el-table-column
           label="浏览次数"
           prop="click_num"
        />
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
              v-auth="['system:notice:update']"
            >编辑</el-button>

            <el-button
              type="text"
              
              @click="deletes(scope.row.id)"
              v-auth="['system:notice:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              
              v-auth="['system:notice:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              
              v-auth="['system:notice:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false" />

</template>

<script>
  import saveDialog from './save'

  export default {
    name: 'system:notice',
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
        
        backend_notice_type_data: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.notice.getList,
          recycleList: this.$API.notice.getRecycleList,
        },
        selection: [],
        queryParams: {
            
          title: undefined,
          type: undefined,
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
            this.$API.notice.realDeletes(ids.join(',')).then(res => {
              if(res.success) {
                this.$message.success(res.message)
                this.$refs.table.upData(this.queryParams)
              } else {
                this.$message.error(res.message)
              }
            })
          } else {
            this.$API.notice.deletes(ids.join(',')).then(res => {
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
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.notice.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.notice.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.notice.recoverys(id).then(res => {
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

      resetSearch() {
        this.queryParams = {
          
          title: undefined,
          type: undefined,
        }
        this.$refs.table.upData(this.queryParams)
      },

      //本地更新数据
      handleSuccess(){
        this.$refs.table.upData(this.queryParams)
      },

      // 获取字典数据
      getDictData() {
        
          this.getDict('backend_notice_type').then(res => {
              this.backend_notice_type_data = res.data
          })
      }
    }
  }
</script>
