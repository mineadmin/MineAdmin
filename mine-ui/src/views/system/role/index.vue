<template>
  <el-container>
    <el-header>
      <div class="left-panel">

        <el-button
          icon="el-icon-plus"
          v-auth="['system:role:save']"
          type="primary"
          @click="add"
        >新增</el-button>

        <el-button
          type="danger"
          plain
          icon="el-icon-delete"
          v-auth="['system:role:delete']"
          :disabled="selection.length==0"
          @click="batchDel"
        >删除</el-button>

      </div>
      <div class="right-panel">
        <div class="right-panel-search">
          <el-input v-model="queryParams.name" placeholder="搜索角色名" clearable></el-input>

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
              
              <el-form-item label="标识" prop="code">
                <el-input v-model="queryParams.code" placeholder="角色标识" clearable></el-input>
              </el-form-item>

              <el-form-item label="状态" prop="status">
                <el-select size="small" v-model="queryParams.status" clearable placeholder="状态">
                  <el-option label="启用" value="0">启用</el-option>
                  <el-option label="停用" value="1">停用</el-option>
                </el-select>
              </el-form-item>

              <el-form-item label="创建时间">
                <el-date-picker
                  clearable
                  size="small"
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
        :api="api"
        :showRecycle="true"
        @selection-change="selectionChange"
        @switch-data="switchData"
        stripe
        remoteSort
        remoteFilter
      >
        <el-table-column type="selection" width="50"></el-table-column>
        
        <el-table-column
          label="角色名称"
          prop="name"
          sortable='custom'
          width="260"
        ></el-table-column>

        <el-table-column
          label="角色标识代码"
          prop="code"
          width="220"
        ></el-table-column>

        <el-table-column
          label="排序"
          prop="sort"
          width="180"
        ></el-table-column>

        <el-table-column
          label="状态"
          prop="status"
          width="200"
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
        <el-table-column label="操作" fixed="right" align="right" width="130" v-if="!isRecycle">
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              @click="show(scope.row, scope.$index)"
            >查看</el-button>

            <el-dropdown v-if="scope.row.code !== 'superAdmin'">

              <el-button
                type="text" size="small"
              >
                更多<i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>

              <template #dropdown>
                <el-dropdown-menu>

                  <el-dropdown-item
                    @click="edit(scope.row, scope.$index)"
                    v-auth="['system:role:update']"
                  >编辑</el-dropdown-item>

                  <el-dropdown-item 
                    @click="$refs.menuDialog.open().setData(scope.row)"
                    v-auth="['system:role:menuPermission']"
                  >菜单权限</el-dropdown-item>

                  <el-dropdown-item 
                    @click="$refs.dataDialog.open().setData(scope.row)"
                    v-auth="['system:role:dataPermission']"
                  >数据权限</el-dropdown-item>

                  <el-dropdown-item
                    @click="deletes(scope.row.id)"
                    divided
                    v-auth="['system:role:delete']"
                  >删除</el-dropdown-item>

                </el-dropdown-menu>
              </template>

            </el-dropdown>
            
          </template>
        </el-table-column>

        <!-- 回收站操作按钮 -->
        <el-table-column label="操作" fixed="right" align="right" width="130" v-else>
          <template #default="scope">

            <el-button
              type="text"
              size="small"
              v-auth="['system:role:recovery']"
              @click="recovery(scope.row.id)"
            >恢复</el-button>

            <el-button
              type="text"
              size="small"
              v-auth="['system:role:realDelete']"
              @click="deletes(scope.row.id)"
            >删除</el-button>

          </template>
        </el-table-column>

      </maTable>
    </el-main>
  </el-container>

  <save-dialog v-if="dialog.save" ref="saveDialog" @success="handleSuccess" @closed="dialog.save=false" />

  <menu-dialog ref="menuDialog" />
  <data-dialog ref="dataDialog" @success="handleSuccess" />

</template>

<script>
  import saveDialog from './save'
  import menuDialog from './menuForm'
  import dataDialog from './dataForm'

  export default {
    name: 'system:role',
    components: {
      saveDialog,
      menuDialog,
      dataDialog,
    },

    data() {
      return {
        dialog: {
          save: false
        },
        povpoerShow: false,
        dateRange:'',
        showDeptloading: false,
        deptFilterText: '',
        dept: [],
        api: {
          list: this.$API.role.getPageList,
          recycleList: this.$API.role.getRecyclePageList,
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
      edit(row){
        this.dialog.save = true
        this.$nextTick(() => {
          this.$refs.saveDialog.open('edit').setData(row)
        })
      },
      //查看
      show(row){
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
            this.$API.role.realDeletes(ids.join(',')).then()
          } else {
            this.$API.role.deletes(ids.join(',')).then()
          }
          this.$refs.table.upData(this.queryParams)
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
        }).then(() => {
          const loading = this.$loading();
          if (this.isRecycle) {
            this.$API.role.realDeletes(id).then(() => {
              this.$refs.table.upData(this.queryParams)
            })
          } else {
            this.$API.role.deletes(id).then(() => {
              this.$refs.table.upData(this.queryParams)
            })
          }
          loading.close();
          this.$message.success("操作成功")
        }).catch(()=>{})
      },

      // 恢复数据
      async recovery (id) {
        await this.$API.role.recoverys(id).then(res => {
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

      // 角色状态更改
      handleStatus (val, row) {
        const status = row.status === '0' ? '0' : '1'
        const text = row.status === '0' ? '启用' : '停用'
        this.$confirm(`确认要${text} ${row.name} 角色吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(() => {
          this.$API.role.changeStatus({ id: row.id, status }).then(() => {
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

<style>
</style>
