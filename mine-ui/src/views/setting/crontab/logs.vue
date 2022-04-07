<template>
  <el-container>
    <div style="padding: 0 0 10px 20px">
      <el-button
        type="danger"
        plain
        icon="el-icon-delete"
        v-auth="['system:crontab:deleteLog']"
        :disabled="selection.length == 0"
        @click="batchDel"
        >删除</el-button
      >
    </div>
    <el-main style="padding: 0 20px">
      <maTable
        ref="table"
        row-key="id"
        :api="api"
        :autoLoad="false"
        :params="{ orderBy: 'created_at', orderType: 'desc' }"
        @selection-change="selectionChange"
        stripe
      >
        <el-table-column type="selection" width="50"></el-table-column>
        <el-table-column
          label="执行时间"
          prop="created_at"
          width="160"
        ></el-table-column>
        <el-table-column label="执行结果" prop="state" width="80">
          <template #default="scope">
            <span v-if="scope.row.status == '0'" style="color: #67c23a"
              ><i class="el-icon-success"></i> 成功</span
            >
            <span v-else style="color: #f56c6c"
              ><i class="el-icon-error"></i> 异常</span
            >
          </template>
        </el-table-column>
        <el-table-column
          label="执行目标"
          prop="target"
          width="200"
          :show-overflow-tooltip="true"
        ></el-table-column>
        <el-table-column label="异常信息" prop="logs" width="100" fixed="right">
          <template #default="scope">
            <el-button @click="show(scope.row)" type="text">查看</el-button>
          </template>
        </el-table-column>
      </maTable>
    </el-main>

    <el-drawer
      title="异常信息"
      v-model="logsVisible"
      :size="500"
      direction="rtl"
      destroy-on-close
    >
      <el-main style="padding: 0 20px 20px 20px">
        <pre>{{ log == "" ? "无异常信息" : log }}</pre>
      </el-main>
    </el-drawer>
  </el-container>
</template>

<script>
export default {
  data() {
    return {
      selection: [],
      logsVisible: false,
      api: {
        list: this.$API.crontab.getLogPageList,
      },
      queryParams: {
        crontab_id: undefined,
      },
      log: "",
      crontab_id: "",
    };
  },

  methods: {
    show(row) {
      this.logsVisible = true;
      this.log = row.exception_info;
    },

    setData(row) {
      this.crontab_id = row.id;
      this.queryParams.crontab_id = row.id;
      this.$refs.table.upData(this.queryParams);
    },

    //批量删除
    async batchDel() {
      await this.$confirm( `确定删除选中的 ${this.selection.length} 项吗？`, "提示", {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
      }).then(() => {
        const loading = this.$loading();
        let ids = [];
        this.selection.map((item) => ids.push(item.id));
        this.$API.crontab.deleteLog(ids.join(",")).then(() => {
          this.$refs.table.upData({ crontab_id: this.crontab_id });
        });
        loading.close();
        this.$message.success("操作成功");
      });
    },

    //表格选择后回调事件
    selectionChange(selection) {
      this.selection = selection;
    },
  },
};
</script>

<style scoped>
pre {
  font-size: 12px;
  color: #ccc;
  padding: 20px;
  background: #333;
  font-family: consolas;
  line-height: 1.5;
  overflow: auto;
  border-radius: 4px;
}
.el-container {
  flex-direction: column;
}
</style>
