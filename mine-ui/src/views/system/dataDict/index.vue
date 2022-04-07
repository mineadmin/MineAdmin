<template>
  <el-container>
    <el-aside width="310px" v-loading="showDicloading">
      <el-container>
        <el-header>
          <el-input placeholder="输入关键字进行过滤" v-model="dicFilterText" clearable></el-input>
        </el-header>
        <el-main class="nopadding">

          <el-tree
            ref="dictType"
            class="menu"
            node-key="id"
            :data="dictTypeList"
            :props="dicProps"
            :highlight-current="true"
            :expand-on-click-node="false"
            :filter-node-method="dicFilterNode"
            @node-click="dicClick"
          >
            <template #default="{node, data}">
              <span class="custom-tree-node">
                <span class="label">{{ node.label }}</span>
                <span class="code">{{ data.code }}</span>
                <span class="do" v-if="showTypeRecycle">

                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="恢复数据"
                    placement="top"
                  >
                    <el-button
                      class="mini-button"
                      icon="el-icon-refresh-left"
                      v-auth="'system:dictType:recovery'"
                      @click.stop="dictTypeRecoverys(data)"
                    />
                  </el-tooltip>

                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="物理删除"
                    placement="top"
                  >
                    <el-button
                      class="mini-button"
                      icon="el-icon-delete"
                      v-auth="'system:dictType:realDelete'"
                      @click.stop="dictTypeDelete(node, data)"
                    />
                  </el-tooltip>

                </span>
                <span class="do" v-else>

                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="编辑字典类型"
                    placement="top"
                  >
                    <el-button
                      class="mini-button"
                      icon="el-icon-edit"
                      v-auth="'system:dictType:update'"
                      @click.stop="dictTypeEdit(data)"
                    />
                  </el-tooltip>

                  <el-tooltip
                    class="item"
                    effect="dark"
                    content="删除字典类型"
                    placement="top"
                  >
                    <el-button
                      class="mini-button"
                      icon="el-icon-delete"
                      v-auth="'system:dictType:delete'"
                      @click.stop="dictTypeDelete(node, data)"
                    />
                  </el-tooltip>

                </span>
              </span>
            </template>
          </el-tree>
        </el-main>
        <el-footer style="height:60px;">
          <el-button
            icon="el-icon-plus"
            v-auth="'system:dictType:save'"
            @click="addDictType()"
            v-if="!showTypeRecycle"
          >新增</el-button>

          <el-button
            icon="el-icon-view"
            v-auth="'system:dictType:recycle'"
            @click="switchTypeData"
          >{{ getSwitchText }}</el-button>

          <el-button
            icon="el-icon-refresh"
            @click="getDictTypeList"
          >刷新</el-button>
        </el-footer>
      </el-container>
    </el-aside>
    <el-container class="is-vertical">
      <el-header>
        <div class="left-panel">

          <el-button
            type="primary"
            v-if="!showTypeRecycle"
            icon="el-icon-plus"
            @click="addDataDict"
            v-auth="['system:dataDict:save']"
          >新增</el-button>

          <el-button
            type="danger"
            plain
            icon="el-icon-delete"
            :disabled="selection.length==0"
            @click="dataDictBatchDelete"
            v-auth="['system:dataDict:delete']"
          >删除</el-button>

          <el-button
            type="success"
            plain
            icon="el-icon-delete"
            @click="clearCache"
            v-auth="['system:dataDict:clearCache']"
          >清除缓存</el-button>

        </div>

        <div class="right-panel">
          <div class="right-panel-search">
            <el-input v-model="dataQueryParams.label" placeholder="字典标签" clearable></el-input>

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

                <el-form-item label="状态" prop="status">
                  <el-select  v-model="dataQueryParams.status" style="width:100%" clearable placeholder="状态">
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
          :api="api"
          row-key="id"
          :params="dataQueryParams"
          :showRecycle="true"
          :autoLoad="false"
          @selection-change="selectionChange"
          @switch-data="switchData"
          stripe :paginationLayout="'prev, pager, next'"
        >
          <el-table-column type="selection" width="50"></el-table-column>
          <el-table-column label="字典标签" prop="label" width="180"></el-table-column>
          <el-table-column label="键值" prop="value" width="180"></el-table-column>
          <el-table-column label="排序" prop="sort" width="100"></el-table-column>
          <el-table-column label="状态" prop="status" width="100">
            <template #default="scope">
              <el-switch
                v-if="scope.row.status"
                v-model="scope.row.status"
                @change="handleStatus($event, scope.row)"
                active-value="0"
                inactive-value="1"
                ></el-switch>
            </template>
          </el-table-column>
          <el-table-column label="创建时间" prop="created_at" width="180"></el-table-column>
          <el-table-column label="操作" fixed="right" align="right" width="140" v-if="isDataRecycle">
            <template #default="scope">
              <el-button
                type="text"
                
                @click="dataDictRecovery(scope.row.id)"
                v-auth="['system:dataDict:recovery']"
              >恢复</el-button>

              <el-button
                type="text"
                
                @click="dataDictDelete(scope.row.id)"
                v-auth="['system:dataDict:realDelete']"
              >删除</el-button>
            </template>
          </el-table-column>

          <el-table-column label="操作" fixed="right" align="right" width="140" v-else>
            <template #default="scope">
              <el-button
                type="text"
                
                @click="dataDictEdit(scope.row, scope.$index)"
                v-auth="['system:dataDict:update']"
              >编辑</el-button>

              <el-button
                type="text"
                
                @click="dataDictDelete(scope.row.id)"
                v-auth="['system:dataDict:delete']"
              >删除</el-button>
                
            </template>
          </el-table-column>
        </maTable>
      </el-main>
    </el-container>
  </el-container>

  <type-dialog v-if="dialog.dictType" ref="typeDialog" @success="handleTypeSuccess" @closed="dialog.dictType=false"></type-dialog>

  <data-dialog v-if="dialog.dictData" ref="dataDialog" @success="handleDataSuccess" @closed="dialog.dictData=false"></data-dialog>

</template>

<script>
  import typeDialog from './type'
  import dataDialog from './data'

  export default {
    name: 'system:dataDict',
    components: {
      typeDialog,
      dataDialog
    },
    data() {
      return {
        dialog: {
          dictType: false,
          dictData: false
        },
        dateRange:'',
        showTypeRecycle: false,
        isDataRecycle: false,
        showDicloading: true,
        dictTypeList: [],
        dicFilterText: '',
        dicProps: { label: 'name' },
        api: {
          list: this.$API.dataDict.getPageList,
          recycleList: this.$API.dataDict.getRecyclePageList,
        },
        dataQueryParams: {},
        currentTypeCode: '',
        selection: []
      }
    },
    
    computed: {
      getSwitchText() {
        return this.showTypeRecycle ? '显示正常数据' : '回收站'
      }
    },

    watch: {
      dicFilterText(val) {
        this.$refs.dictType.filter(val);
      }
    },

    mounted() {
      this.getDictTypeList()
    },

    methods: {
      //加载字典类型数据
      async getDictTypeList(){
        this.showDicloading = true
        if (!this.showTypeRecycle) {
          await this.$API.dictType.getTypeList().then(res => {
            this.dictTypeList = res.data
            this.showDicloading = false
          });
        } else {
          await this.$API.dictType.getRecycleTypeList().then(res => {
            this.dictTypeList = res.data
            this.showDicloading = false
          });
        }
        
        //获取第一个节点,设置选中 & 加载明细列表
        if(this.dictTypeList.length){
          this.$nextTick(() => {
            this.$refs.dictType.setCurrentKey(this.dictTypeList[0].id)
          })
          this.dataQueryParams = {
            code: this.dictTypeList[0].code
          }
          await this.$refs.table.upData(this.dataQueryParams)
        }
      },

      //字典类型过滤
      dicFilterNode(value, data){
        if (!value) return true;
        return data.name.indexOf(value) !== -1;
      },

      //字典类型增加
      addDictType(){
        this.dialog.dictType = true
        this.$nextTick(() => {
          this.$refs.typeDialog.open()
        })
      },

      //编辑字典类型
      dictTypeEdit(data){
        this.dialog.dictType = true
        this.$nextTick(() => {
          var editNode = this.$refs.dictType.getNode(data.id);
          var editNodeParentId =  editNode.level==1?undefined:editNode.parent.data.id
          data.parentId = editNodeParentId
          this.$refs.typeDialog.open('edit').setData(data)
        })
      },

      // 恢复字典类型
      dictTypeRecoverys (data) {
        this.$API.dictType.recoverys(data.id).then(res => {
          if (res.success) {
            this.$message.success('数据恢复成功')
            this.getDictTypeList()
          }
        })
      },

      //字典类型点击事件
      dicClick(data){
        this.$refs.table.upData({ code: data.code })
        this.currentTypeCode = data.code
      },

      //删除字典类型
      dictTypeDelete(node, data){
        this.$confirm(`确定删除 ${data.name} 项吗？`, '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.showDicloading = true;

          if (this.showTypeRecycle) {
            this.$API.dictType.realDelete(data.id).then(() => {
              this.getDictTypeList()
            })
          } else {
            this.$API.dictType.deletes(data.id).then(() => {
              this.getDictTypeList()
            })
          }

          this.showDicloading = false;
          this.$message.success("操作成功")
        }).catch(() => {

        })
      },

      //添加字典
      addDataDict(){
        this.dialog.dictData = true
        this.$nextTick(() => {
          let id = this.$refs.dictType.getCurrentKey();
          let data = this.dictTypeList.filter(item => id == item.id)
          this.$refs.dataDialog.open('add', data[0])
        })
      },

      //编辑字典
      dataDictEdit(row){
        this.dialog.dictData = true
        this.$nextTick(() => {
          this.$refs.dataDialog.open('edit').setData(row)
        })
      },

      //删除字典
      async dataDictDelete(id){

        await this.$confirm(`确定删除选中的该字典项吗？`, '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          if (this.isDataRecycle) {
            this.$API.dataDict.realDeletesDictData(id).then(() => {
              this.$refs.table.upData(this.dataQueryParams)
            })
          } else {
            this.$API.dataDict.deletesDictData(id).then(() => {
              this.$refs.table.upData(this.dataQueryParams)
            })
          }
          loading.close();
          this.$message.success("操作成功")
        })
        
      },

      // 恢复字典项数据
      async dataDictRecovery(id){
        let res = await this.$API.dataDict.recoverysDictData(id)
        if(res.success){
          this.$refs.table.upData(this.dataQueryParams)
          this.$message.success('恢复成功')
        }else{
          this.$alert(res.message, "提示", {type: 'error'})
        }
      },

      //批量删除字典
      async dataDictBatchDelete(){
        await this.$confirm(`确定删除选中的 ${this.selection.length} 项吗？`, '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          const loading = this.$loading();
          let ids = []
          this.selection.map(item => ids.push(item.id))
          if (this.isDataRecycle) {
            this.$API.dataDict.realDeletesDictData(ids.join(',')).then(() => {
              this.$refs.table.upData(this.dataQueryParams)
            })
          } else {
            this.$API.dataDict.deletesDictData(ids.join(',')).then(() => {
              this.$refs.table.upData(this.dataQueryParams)
            })
          }
          this.$refs.table.upData(this.dataQueryParams)
          loading.close();
          this.$message.success("操作成功")
        })
      },

      // 切换数据类型回调
      switchData(isDataRecycle) {
        this.isDataRecycle = isDataRecycle
      },

      // 选择时间事件
      handleDateChange (values) {
        if (values !== null) {
          this.dataQueryParams.minDate = values[0]
          this.dataQueryParams.maxDate = values[1]
        }
      },

      resetSearch() {
        this.dataQueryParams = {
          label: undefined,
          maxDate: undefined,
          minDate: undefined,
          status: undefined
        }
        this.$refs.table.upData(this.dataQueryParams)
      },

      //提交明细
      saveList(){
        this.$refs.dataDialog.submit(async (formData) => {
          this.isListSaveing = true;
          var res = await this.$API.user.save.post(formData);
          this.isListSaveing = false;
          if(res.code == 200){
            //这里选择刷新整个表格 OR 插入/编辑现有表格数据
            this.$message.success("操作成功")
          }else{
            this.$alert(res.message, "提示", {type: 'error'})
          }
        })
      },

      //表格选择后回调事件
      selectionChange(selection){
        this.selection = selection;
      },

      // 状态更改
      handleStatus (val, row) {
        const status = row.status === '0' ? '0' : '1'
        const text = row.status === '0' ? '启用' : '停用'
        this.$confirm(`确认要${text} ${row.label} 字典吗？`, '提示', {
          type: 'warning',
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(() => {
          this.$API.dataDict.changeStatus({ id: row.id, status }).then(() => {
            this.$message.success(text + '成功')
          })
        }).catch(() => {
          row.status = row.status === '0' ? '1' : '0'
        })
      },

      //搜索
      handlerSearch(){
        this.handleDataSuccess()
      },

      //本地更新数据
      handleTypeSuccess(){
        this.getDictTypeList()
      },

      //本地更新数据
      handleDataSuccess(){
        this.dicClick({code: this.currentTypeCode})
      },

      // 清空缓存
      clearCache() {
        this.$API.dataDict.clearCache().then(res => {
          res.success && this.$message.success('字典缓存已清除')
        })
      },

      switchTypeData () {
        this.showTypeRecycle = !this.showTypeRecycle
        this.getDictTypeList()
        this.$message.success('数据已切换到' + ( this.showTypeRecycle ? '回收站数据' : '正常数据' ))
      }
    }
  }
</script>

<style scoped>
  .custom-tree-node {display: flex;flex: 1;align-items: center;justify-content: space-between;font-size: 14px;padding-right: 24px;height:100%;}
  .custom-tree-node .code {font-size: 12px;color: #999;}
  .custom-tree-node .do {display: none;}
  .custom-tree-node .do i {margin-left:5px;color: #999;padding:5px;}
  .custom-tree-node .do i:hover {color: #333;}
  .custom-tree-node:hover .code {display: none;}
  .custom-tree-node:hover .do {display: inline-block;}

  .mini-button {
    margin-left: 10px; margin-top: -1px; height: 23px;
    padding: 0 5px;
  }
</style>
