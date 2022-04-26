<template>
  <el-dialog
    title="数据权限"
    v-model="visible"
    :width="500"
    destroy-on-close
    append-to-body
  >
    <el-form :model="form" ref="dialogForm" label-width="80px">
      <el-form-item label="角色名称" prop="name">
        <el-input
          v-model="form.name"
          :disabled="true"
          clearable
          placeholder="请输入角色名称"
        ></el-input>
      </el-form-item>

      <el-form-item label="代码" prop="code">
        <el-input
          v-model="form.code"
          :disabled="true"
          clearable
          placeholder="请输入角色代码"
        ></el-input>
      </el-form-item>

      <el-form-item label="数据边界">
        <el-select
          v-model="form.data_scope"
          placeholder="请选择数据权限边界"
          @change="handleChangeScope"
        >
          <el-option
            v-for="item in scopes"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-form-item>

      <el-form-item
        v-if="form.data_scope === '1'"
        label="角色权限"
        prop="dept_ids"
        v-loading="loading"
        element-loading-background="rgba(255, 255, 255, 0.01)"
        element-loading-text="数据加载中..."
      >
        <el-checkbox @change="handleTreeExpand($event)">展开/折叠</el-checkbox>
        <el-checkbox @change="handleTreeAll($event)">全选/全不选</el-checkbox>
        <el-tree
          class="ma-tree-border"
          ref="tree"
          :data="deptList"
          show-checkbox
          node-key="id"
          empty-text="加载数据中..."
          :props="defaultProps"
        />
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button @click="visible = false">取 消</el-button>
      <el-button type="primary" :loading="isSaveing" @click="submit()"
        >保 存</el-button
      >
    </template>
  </el-dialog>
</template>

<script>
export default {
  emits: ["success"],
  data() {
    return {
      visible: false,
      isSaveing: false,
      loading: false,
      //表单数据
      form: {
        id: null,
        name: null,
        code: null,
        data_scope: "0",
        dept_ids: null,
      },
      // ele 树props
      defaultProps: {
        children: "children",
        label: "label",
      },
      // 菜单列表
      deptList: [],

      scopes: [
        { value: "0", label: "全部数据权限" },
        { value: "1", label: "自定义数据权限" },
        { value: "2", label: "本部门数据权限" },
        { value: "3", label: "本部门及以下数据权限" },
        { value: "4", label: "本人数据权限" },
      ],
    };
  },

  methods: {
    //显示
    open() {
      this.visible = true;
      this.loading = true;
      return this;
    },

    //表单提交方法
    submit() {
      this.$refs.dialogForm.validate(async (valid) => {
        if (valid) {
          this.isSaveing = true;
          if (this.form.data_scope == "1") {
            this.form.dept_ids = this.getTreeSelectNodes();
          }
          let res = await this.$API.role.update(this.form.id, this.form);
          this.isSaveing = false;
          if (res.success) {
            this.visible = false;
            this.$emit("success", this.form);
            this.$message.success(res.message);
          } else {
            this.$alert(res.message, "提示", { type: "error" });
          }
        } else {
          return false;
        }
      });
    },

    // 获取所选节点
    getTreeSelectNodes() {
      // 目前被选中的菜单节点
      const selectKeys = this.$refs.tree.getCheckedKeys();
      // 半选中的菜单节点
      const halfSelectKeys = this.$refs.tree.getHalfCheckedKeys();
      selectKeys.unshift.apply(selectKeys, halfSelectKeys);
      return selectKeys;
    },

    // 更改权限边界
    handleChangeScope(value) {
      if (value !== "1" && this.$refs.tree) {
        this.$refs.tree.setCheckedKeys([]);
      }
    },

    // 树（展开/折叠）
    handleTreeExpand(value) {
      this.deptList.forEach((item) => {
        this.$refs.tree.store.nodesMap[item.id].expanded = value;
      });
    },

    // 树（全选/全不选）
    handleTreeAll(value) {
      this.$refs.tree.setCheckedNodes(value ? this.deptList : []);
    },

    //表单注入数据
    async setData(data) {
      this.form.id = data.id;
      this.form.name = data.name;
      this.form.code = data.code;
      this.form.data_scope = data.data_scope;

      await this.$API.dept.tree(data.id).then((res) => {
        this.deptList = res.data;
      });

      if (data.data_scope === "1") {
        await this.$API.role.getDeptByRole(data.id).then((res) => {
          if (res.data[0] && res.data[0].depts) {
            res.data[0].depts.forEach((item) => {
              this.$refs.tree.setChecked(item.id, true, false);
            });
          }
        });
      }

      this.loading = false;
    },
  },
};
</script>
<style scoped lang="scss">
:deep(.el-form-item__content) {
  display: block;
}
[data-theme="dark"] {
  .ma-tree-border {
    border: 1px solid #383838;
  }
}
</style>