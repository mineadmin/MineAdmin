<template>
    <el-form  label-width="40px">
        <el-row :gutter="15">
            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" class="mb15">
                <el-form-item label="省级">
                    <el-select v-model="defaultValue.province" placeholder="请选择省级" clearable @change="onProvinceChange" class="w100">
                        <el-option v-for="(v, k) in linkage.provinceList" :key="k" :label="v.name" :value="v[valueType]"></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" class="mb15">
                <el-form-item label="市级">
                    <el-select v-model="defaultValue.city" placeholder="请选择市级" clearable @change="onCityChange" class="w100">
                        <el-option v-for="(v, k) in linkage.cityList" :key="k" :label="v.name" :value="v[valueType]"></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
                <el-form-item label="区级">
                    <el-select v-model="defaultValue.area" placeholder="请选择区级" @change="onAreaChange" clearable class="w100">
                        <el-option v-for="(v, k) in linkage.areaList" :key="k" :label="v.name" :value="v[valueType]"></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
    </el-form>
</template>

<script>
import cityLinkageJson from './lib/cityLinkage.json';
export default {
    name: 'threeLevelLinkage',

    props: {
        modelValue: {
            type: Object, 
            default: () => {} 
        },
        valueType: {
            type: String,
            default: 'code',
        }
    },

    watch:{
        modelValue(val){
            this.defaultValue = val
        }
    },
    
    data () {
        return {
            defaultType: 'code',
            linkage: {
                provinceList: cityLinkageJson, // 省
				cityList: [], // 市
				areaList: [], // 区
            },
            defaultValue: {
                province: '',
				city: '',
				area: ''
            }
        }
    },

    methods: {
        onProvinceChange (val) {
            this.linkage.cityList = this.linkage.areaList = []
            this.defaultValue.city = this.defaultValue.area = ''
            this.linkage.provinceList.map(item => {
				if (item[this.valueType] === val) this.linkage.cityList = item.children;
			});
            this.$emit('update:modelValue', this.defaultValue)
        },

        onCityChange (val) {
            this.linkage.areaList = []
            this.defaultValue.area = ''
            this.linkage.cityList.map(item => {
				if (item[this.valueType] === val) this.linkage.areaList = item.children;
			});
            this.$emit('update:modelValue', this.defaultValue)
        },
        onAreaChange () {
            this.$emit('update:modelValue', this.defaultValue)
        }
    }

}
</script>

<style lang="scss" scoped>
.w100 {
    width: 100%;
}
</style>