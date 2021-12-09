<template>
    <template v-for="(item, index) in options" :key="index">
        <el-tag v-if="values.includes(item.value)" :type="tagType">{{item.label}}</el-tag>
    </template>
</template>

<script>
export default {
  name: "maDictTag",
  props: {
    options: {
      type: Array,
      default: null,
    },
    value: [Number, String, Array],
    tagType: {
      type: String,
      default: ''
    }
  },
  computed: {
    values() {
      if (this.value !== null && typeof this.value !== 'undefined') {
        return Array.isArray(this.value) ? this.value : [String(this.value)];
      } else {
        return [];
      }
    },
  },
};
</script>
<style scoped>
.el-tag + .el-tag {
  margin-left: 10px;
}
</style>