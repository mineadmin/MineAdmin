<template>
  <el-cascader
    v-model="defaultValue"
    :options="cityLinkageList"
    :props="{
      expandTrigger: expandType,
      value: valueType,
      label: 'name',
      checkStrictly: true,
    }"
    filterable
    :placeholder="placeholder"
    clearable
  ></el-cascader>
</template>

<script>
import { handleError } from "vue-demi";
import cityLinkageJson from "./lib/cityLinkage.json";
export default {
  name: "cityLinkage",

  props: {
    modelValue: {
      type: Object,
      default: () => {},
    },
    placeholder: {
      type: String,
      default: "请选择区域",
    },
    valueType: {
      type: String,
      default: "code",
    },
    expandType: {
      type: String,
      default: "click",
    },
  },

  watch: {
    valueType(val) {
      this.defaultType = val !== "code" ? "name" : "code";
    },
    modelValue: {
      handle(val) {
        this.defaultValue = val;
      },
      immediate: true,
    },
    defaultValue(val) {
      this.$emit("update:modelValue", val);
    },
  },

  data() {
    return {
      cityLinkageList: cityLinkageJson,
      defaultValue: "",
      defaultType: "",
    };
  },
};
</script>