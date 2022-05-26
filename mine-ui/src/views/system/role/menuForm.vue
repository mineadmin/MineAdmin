<template>
  <el-dialog
    title="菜单权限"
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

      <el-form-item
        label="角色权限"
        prop="menu_ids"
        v-loading="loading"
        element-loading-background="rgba(255, 255, 255, 0.01)"
        element-loading-text="数据加载中..."
      >
        <el-checkbox @change="handleTreeExpand($event)">展开/折叠</el-checkbox>
        <el-checkbox @change="handleTreeAll($event)">全选/全不选</el-checkbox>
        <el-tree
          class="ma-tree-border"
          ref="tree"
          :data="menuList"
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
        menu_ids: null,
      },

      // ele 树props
      defaultProps: {
        children: "children",
        label: "label",
      },
      // 菜单列表
      menuList: [],
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
          this.form.menu_ids = this.getTreeSelectNodes();
          let res = await this.$API.role.update(this.form.id, this.form);
          this.isSaveing = false;
          if (res.success) {
            this.visible = false;
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

    // 树（展开/折叠）
    handleTreeExpand(value) {
      this.menuList.forEach((item) => {
        this.$refs.tree.store.nodesMap[item.id].expanded = value;
      });
    },

    // 树（全选/全不选）
    handleTreeAll(value) {
      this.$refs.tree.setCheckedNodes(value ? this.menuList : []);
    },

    //表单注入数据
    async setData(data) {
      this.form.id = data.id;
      this.form.name = data.name;
      this.form.code = data.code;

      await this.$API.menu.tree().then((res) => {
        this.menuList = res.data;
      });

      await this.$API.role.getMenuByRole(data.id).then((res) => {
        if (res.data[0] && res.data[0].menus) {
          res.data[0].menus.forEach((item) => {
            this.$refs.tree.setChecked(item.id, true, false);
          });
        }
      });

      this.loading = false;
    },
  },
};
</script>
<style scoped lang="scss">
:deep(.el-form-item__content) {
  display: block;
}
</style>