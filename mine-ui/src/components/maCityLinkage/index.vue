<template>
    <el-main style="padding-left: 0">
        <el-cascader
            v-model="defaultValue"
            :options="cityLinkageList"
            :props="{ expandTrigger: 'hover', value: valueType, label: 'name' }"
            filterable
            size="small"
            :placeholder="placeholder"
            clearable
        ></el-cascader>
    </el-main>
</template>

<script>
import cityLinkageJson from './lib/cityLinkage.json';
export default {
    name: 'cityLinkage',


    props: {
        modelValue: {
            type: Object, 
            default: () => {} 
        },
        placeholder: {
            type: String,
            default: '请选择区域'
        },
        valueType: {
            type: String,
            default: 'code',
        }
    },

    watch:{
        valueType(val) {
            this.defaultType = val !== 'code' ? 'name' : 'code'
        },
        modelValue(val){
            this.defaultValue = val
        },
        defaultValue(val){
            this.$emit('update:modelValue', val)
        }
    },
    
    data () {
        return {
            cityLinkageList: cityLinkageJson,
            defaultValue: '',
            defaultType: '',
        }
    }

}
</script>