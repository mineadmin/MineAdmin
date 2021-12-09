<template>
	<sc-page-header
			:title="title"
			icon="el-icon-connection"
	>
		<el-button @click="$router.push('api')">返回接口管理</el-button>
	</sc-page-header>

  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:apiColumn:save']"
          type="primary"
          @click="add"
        >新增</el-button>

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:apiColumn:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

				<el-button
					icon="el-icon-download"
					v-auth="['system:apiColumn:export']"
					@click="exportExcel"
				>导出</el-button>

				<ma-import
					:auth="['system:apiColumn:import']"
					:upload-api="$API.apiColumn.importExcel"
					:download-tpl-api="$API.apiColumn.downloadTemplate"
					@success="handleSuccess()"
				/>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">

          <el-input v-model="queryParams.name" placeholder="字段名称" clearable></el-input>

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

            <el-form-item label="是否必填" prop="is_required">
              <el-input v-model="queryParams.is_required" placeholder="是否必填" clearable></el-input>
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
				:autoLoad="false"
        stripe
        remoteSort
      >
        <el-table-column type="selection" width="50"></el-table-column>


        <el-table-column
           label="字段名称"
           prop="name"
        />

        <el-table-column
           label="数据类型"
           prop="data_type"
        >
          <template #default="scope">
            <ma-dict-tag :options="api_data_type" :value="scope.row.data_type" />
          </template>
        </el-table-column>

        <el-table-column
           label="是否必填"
           prop="is_required"
           v-if="type === 'request'"
        >
          <template #default="scope">
            <el-tag v-if="scope.row.is_required === '0'" type="danger">必填</el-tag>
            <el-tag v-else type="success">非必填</el-tag>
          </template>
        </el-table-column>

        <el-table-column
           label="默认值"
           prop="default_value"
           v-if="type === 'request'"
        />
        <el-table-column
           label="状态"
           prop="status"
        >
          <template #default="scope">
            <status-indicator :type="scope.row.status === '0' ? 'primary' : 'danger'" />
          </template>
        </el-table-column>

        <el-table-column
           label="创建时间"
           prop="created_at"
        />

        <!-- 正常数据操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              @click="tableShow(scope.row)"
              v-auth="['system:apiColumn:read']"
            >查看</el-button>

            <el-button
              type="text"
              size="small"
              @click="tableEdit(scope.row)"
              v-auth="['system:apiColumn:update']"
            >编辑</el-button>

            <el-button
              type="text"
              size="small"
              @click="deletes(scope.row.id)"
              v-auth="['system:apiColumn:delete']"
            >删除</el-button>

          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['system:apiColumn:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['system:apiColumn:realDelete']"
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
  import useTabs from '@/utils/useTabs'
  import statusIndicator from  '@/components/scMini/scStatusIndicator'

  export default {
    name: 'system:apiColumn',
    components: {
      saveDialog,
      statusIndicator
    },

    async created() {
      await this.getDictData();
      let query = this.$route.query
      if (query.title && query.apiId) {
        this.title = query.title + ' - ' + ((query.type === 'request') ? '接口请求数据' : '接口响应数据')
        this.apiId = query.apiId
				this.type  = query.type
        useTabs.setTitle(this.title)
				this.queryParams.api_id = this.apiId
				this.queryParams.type = (query.type === 'request') ? '0' : '1'
				this.handleSuccess()
      } else {
        this.$message.error('请从正确来路访问页面，标签页已关闭')
        useTabs.close()
      }
    },

    data() {
      return {
        dialog: {
          save: false
        },
        title: '',
        apiId: '',
				type: '',
        data_status_data: [],
        api_data_type: [],
        column: [],
        povpoerShow: false,
        dateRange:'',
        api: {
          list: this.$API.apiColumn.getList,
          recycleList: this.$API.apiColumn.getRecycleList,
        },
        selection: [],
        queryParams: {
          name: undefined,
          is_required: undefined,
          status: undefined
        },
        isRecycle: false,
      }
    },
    methods: {
      //添加
      add(){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('add', this.type, this.apiId)
        })
      },

      //编辑
      tableEdit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit', this.type, this.apiId).setData(row)
        })
      },

      //查看
      tableShow(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('show', this.type, this.apiId).setData(row)
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
            this.$API.apiColumn.realDeletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          } else {
            this.$API.apiColumn.deletes(ids.join(','))
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        })
      },

      // 单个删除
      async deletes(id) {
        await this.$confirm(`确定删除该数据吗？`, '提示', {
          type: 'warning'
        }).then(async () => {
          const loading = this.$loading();
          if (this.isRecycle) {
            await this.$API.apiColumn.realDeletes(id)
            this.$refs.table.upData(this.queryParams)
          } else {
            await this.$API.apiColumn.deletes(id)
            this.$refs.table.upData(this.queryParams)
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.apiColumn.recoverys(id).then(res => {
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

          name: undefined,
          is_required: undefined,
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

          this.getDict('api_data_type').then(res => {
					  this.api_data_type = res.data
			  	})
      },

			// 导出字段
			exportExcel () {
				this.$API.apiColumn.exportExcel(this.queryParams).then(res => {
					this.$TOOL.download(res)
				})
			},
    }
  }
</script>
