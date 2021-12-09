<!--
 * @Descripttion: 异步选择器
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年8月3日15:53:37
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<div class="sc-select">
		<div v-if="initloading" class="sc-select-loading">
			<el-icon class="is-loading"><el-icon-loading /></el-icon>
		</div>
		<el-select v-bind="$attrs" :loading="loading" @visible-change="visibleChange">
			<el-option v-for="item in options" :key="item[props.value]" :label="item[props.label]" :value="item[props.value]">
				<slot name="option" :data="item"></slot>
			</el-option>
		</el-select>
	</div>
</template>

<script>
	import config from "@/config/select";

	export default {
		props: {
			apiObj: { type: Object, default: () => {} },
			dic: { type: String, default: "" },
			params: { type: Object, default: () => ({}) }
		},
		data() {
			return {
				dicParams: this.params,
				loading: false,
				options: [],
				props: config.props,
				initloading: false
			}
		},
		created() {
			//如果有默认值就去请求接口获取options
			if(this.$attrs.modelValue && this.$attrs.modelValue.length > 0){
				this.initloading = true
				this.getRemoteData()
			}
		},
		methods: {
			//选项显示隐藏事件
			visibleChange(ispoen){
				if(ispoen && this.options.length==0 && (this.dic || this.apiObj)){
					this.getRemoteData()
				}
			},
			//获取数据
			async getRemoteData(){
				this.loading = true
				this.dicParams[config.request.name] = this.dic
				var res = {}
				if(this.apiObj){
					res = await this.apiObj.get(this.params)
				}else if(this.dic){
					res = await config.dicApiObj.get(this.params)
				}
				var response = config.parseData(res)
				this.options = response.data
				this.loading = false
				this.initloading = false
			}
		}
	}
</script>

<style scoped>
	.sc-select {display: inline-block;position: relative;}
	.sc-select-loading {position: absolute;top:0;left:0;right:0;bottom:0;background: #fff;z-index: 100;border-radius: 5px;border: 1px solid #EBEEF5;display: flex;align-items: center;padding-left:10px;}
	.sc-select-loading i {font-size: 14px;}

	[data-theme='dark'] .sc-select-loading {background: var(--el-color-white);border-color: var(--el-border-color-base) ;}
</style>
