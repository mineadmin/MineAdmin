<template>
  <el-container>
    <el-header class="mine-el-header">
      <div class="panel-container">
        <div class="left-panel">

          <el-button
            icon="el-icon-plus"
            v-auth="['system:app:save']"
            type="primary"
            @click="add"
          >新增</el-button>

          <el-button
            type="danger"
            plain
            icon="el-icon-delete"
            v-auth="['system:app:delete']"
            :disabled="selection.length==0"
            @click="batchDel"
          >删除</el-button>

        </div>
        <div class="right-panel">
          <div class="right-panel-search">

            <el-input v-model="queryParams.app_name" placeholder="应用名称" clearable></el-input>

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
          <el-form-item label="应用名称" prop="group_id">
            <el-select v-model="queryParams.group_id" style="width:100%" clearable placeholder="应用分组">
              <el-option v-for="(item, index) in groupData" :key="index" :value="item.id" :label="item.name" />
            </el-select>
          </el-form-item>

          <el-form-item label="APP ID" prop="app_id">
              <el-input v-model="queryParams.app_id" placeholder="APP ID" clearable></el-input>
          </el-form-item>
      
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
           label="应用名称"
           prop="app_name"
           width="130"
        />
        <el-table-column
           label="APP ID"
           prop="app_id"
           width="130"
        >
          <template #default="scope">
            <el-tooltip content="点击复制" placement="top">
              <span style="cursor:pointer" @click="copy(scope.row.app_id)">{{ scope.row.app_id }}</span>
            </el-tooltip>
          </template>
        </el-table-column>

        <el-table-column
           label="APP SECRET"
           prop="app_secret"
           show-overflow-tooltip
        >
          <template #default="scope">
            <el-tooltip content="点击复制" placement="top">
              <span style="cursor:pointer" @click="copy(scope.row.app_secret)">{{ scope.row.app_secret }}</span>
            </el-tooltip>
          </template>
        </el-table-column>

        <el-table-column
           label="状态"
           prop="status"
           width="50"
        >
          <template #default="scope">
            <status-indicator :type="scope.row.status === '0' ? 'primary' : 'danger'" />
          </template>
        </el-table-column>

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="250" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              
              @click="apidoc(scope.row)"
            >查看文档</el-button>

            <el-button
              type="text"
              
              @click="bind(scope.row)"
              v-auth="['system:app:bind']"
            >绑定接口</el-button>

            <el-button
              type="text"
              
              @click="tableEdit(scope.row, scope.$index)"
              v-auth="['system:app:update']"
            >编辑</el-button>

            <el-button
              type="text"
              
              @click="deletes(scope.row.id)"
              v-auth="['system:app:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="210" v-else>
          <template #default="scope">

            <el-button
              type="text"
              
              v-auth="['system:app:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              
              v-auth="['system:app:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false"></save-dialog>
  <bind-form v-if="dialog.bind" ref="bindForm" @success="handleSuccess" />
</template>

<script>
  import bindForm from './bind'
  import saveDialog from './save'
  import statusIndicator from  '@/components/scMini/scStatusIndicator'

  export default {
    name: 'system:app',
    components: {
      bindForm,
      saveDialog,
      statusIndicator
    },

    async created() {
      await this.getGroupData()
      await this.getDictData()
    },

    data() {
      return {
        dialog: {
          save: false,
          bind: false,
        },
        
        data_status_data: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.app.getList,
          recycleList: this.$API.app.getRecycleList,
        },
        selection: [],
        queryParams: {
            
          group_id: undefined,
          app_name: undefined,
          app_id: undefined,
          status: undefined,
        },
        isRecycle: false,
        groupData: [],
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

      // 查看文档
      apidoc(row) {
        this.$TOOL.data.set('apiAuth', true)
        this.$TOOL.data.set('appId', row.app_id)
        this.$router.push({ name: 'interfaceList' })
      },

      // 绑定接口
      bind(row) {
        this.dialog.bind = true
        this.$nextTick(() => {
          this.$refs.bindForm.open(row.id)
        })
      },

      //编辑
      tableEdit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').then( _ => {
            _.setData(row)
          })
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
            this.$API.app.realDeletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          } else {
            this.$API.app.deletes(ids.join(','))
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
            await this.$API.app.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.app.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      async copy(str) {
        try {
          await this.clipboard(str)
          this.$message.success(this.$t('sys.copy_success'))
        } catch(e) {
          this.$message.error(this.$t('sys.copy_fail'))
        }
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.app.recoverys(id).then(res => {
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
          group_id: undefined,
          app_name: undefined,
          app_id: undefined,
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
      },

      // 获取组列表
      getGroupData() {
        this.$API.appGroup.getSelectList().then(res => {
          if (res.success) {
            this.groupData = res.data
          }
        })
      }
    }
  }
</script>
