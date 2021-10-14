<!--
 * @Descripttion: 二次封装el-select 支持拼音
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年7月31日22:26:56
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<el-select v-bind="$attrs" :filter-method="filterMethod" @visible-change="visibleChange">
		<el-option v-for="field in optionsList" :key="field.value" :label="field.label" :value="field"></el-option>
	</el-select>
</template>

<script>
	import pinyin from './pinyin'

	export default {
		props: {
			options: { type: Array, default: () => [] }
		},
		data() {
			return {
				optionsList: [],
				optionsList_: []
			}
		},
		mounted() {
			this.optionsList = this.options
			this.optionsList_ = [...this.options]
		},
		methods: {
			filterMethod(keyword){
				if(keyword){
					this.optionsList = this.optionsList_
					this.optionsList = this.optionsList.filter((item) =>
						pinyin.match(item.label, keyword)
					);
				}else{
					this.optionsList = this.optionsList_
				}
			},
			visibleChange(isopen){
				if(isopen){
					this.optionsList = this.optionsList_
				}
			}
		}
	}
</script>