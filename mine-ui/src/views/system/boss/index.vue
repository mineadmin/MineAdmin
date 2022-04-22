<script> export default { name: 'system:boss' } </script>
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

  <save-dialog v-if="dialog.save" ref="saveRef" @success="handleSuccess" @closed="dialog.save=false" />

</template>
<script setup>
  import { ref, reactive, onMounted, nextTick } from 'vue'
  import { ElMessage, ElMessageBox } from 'element-plus'
  import systemBoss from '@/api/apis/system/systemBoss'
  import saveDialog from './save.vue'

  const table = ref(null)
  const saveRef = ref(null)
  const povpoerShow = ref(false)
  const isRecycle = ref(false)
  const dateRange = ref()
  const selection = ref([])

  const dialog = reactive({ save: false })
  const api = reactive({ list: systemBoss.getList, recycleList: systemBoss.getRecycleList })
  const queryParams = reactive({
    name: undefined,
    code: undefined,
    sort: undefined,
    status: undefined,
  })

  // 获取字典数据
  const getDictData = () => {
  }

  onMounted(() => {
    getDictData()
  })

  //添加
  const add = async () => {
    dialog.save = true
    await nextTick()
    saveRef.value.open()
  }

  //编辑
  const tableEdit = async (row) => {
    dialog.save = true
    await nextTick()
    saveRef.value.open('edit')
    saveRef.value.setData(row)
  }

  //查看
  const tableShow = async (row) => {
    dialog.save = true
    await nextTick()
    saveRef.value.open('show')
    saveRef.value.setData(row)
  }

  //批量删除
  const batchDel = async () =>{
    await ElMessageBox.confirm(`确定删除选中的 ${selection.value.length} 项吗？`, '提示', {
      type: 'warning',
      confirmButtonText: '确定',
      cancelButtonText: '取消',
    }).then(async () => {
      let ids = []
      selection.value.map(item => ids.push(item.id))
      let res = isRecycle.value ? await systemBoss.realDeletes(ids.join(',')) : await systemBoss.deletes(ids.join(','))
      table.value.upData(queryParams)
      ElMessage.success(res.message)
    }).catch(()=>{})
  }

  // 单个删除
  const deletes = async (id) => {
    await ElMessageBox.confirm(`确定删除该数据吗？`, '提示', {
      type: 'warning',
      confirmButtonText: '确定',
      cancelButtonText: '取消',
    }).then(async () => {
      let res = isRecycle.value ? await systemBoss.realDeletes(id) : await systemBoss.deletes(id)
      table.value.upData(queryParams)
      ElMessage.success(res.message)
    }).catch(()=>{})
  }

  // 恢复数据
  const recovery = (id) => {
    systemBoss.recoverys(id).then(res => {
      ElMessage.success(res.message)
      table.value.upData(queryParams)
    })
  }

  //表格选择后回调事件
  const selectionChange = (items) => {
    selection.value = items
  }

  const toggleFilterPanel = () => {
    povpoerShow.value = ! povpoerShow.value
    document.querySelector('.filter-panel').style.display = povpoerShow.value ? 'block' : 'none'
  }

  //搜索
  const handlerSearch = () => {
    table.value.upData(queryParams)
  }

  // 切换数据类型回调
  const switchData = () => {
    isRecycle.value = ! isRecycle.value
  }

  const resetSearch = () => {
    for (let k in queryParams) queryParams[k] = undefined
    table.value.upData(queryParams)
  }

  // 更新
  const handleSuccess = () =>{
    table.value.upData(queryParams)
  }

  // 标签页查询
  const handleTabsClick = (tab, event) => {
    
  }
</script>
<style>
.el-tabs__item {
	padding: 0 20px;
	height: 44px;
	box-sizing: border-box;
	display: inline-block;
	list-style: none;
	font-size: 14px;
	font-weight: 500;
	color: var(--el-text-color-primary);
	position: relative;
	margin-top: 10px;
}
</style>