<!--
 * @Descripttion: 动态表单渲染器
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年9月22日09:26:25
 * @LastEditors:
 * @LastEditTime:
-->

<template>
	<el-skeleton v-if="renderLoading || Object.keys(form).length==0" animated />

	<el-form v-else ref="form" :model="form" :label-width="config.labelWidth" :label-position="config.labelPosition" v-loading="loading" element-loading-text="Loading...">
		<el-row :gutter="15">
			<template v-for="(item, index) in config.formItems" :key="index">
				<el-col :span="item.span || 24" v-if="!hideHandle(item)">
					<sc-title  v-if="item.component=='title'"  :title="item.label"></sc-title>
					<el-form-item v-else :prop="item.name" :rules="rulesHandle(item)">
						<template #label>
							{{item.label}}
							<el-tooltip v-if="item.tips" :content="item.tips">
								<el-icon><el-icon-question-filled /></el-icon>
							</el-tooltip>
						</template>
						<!-- input -->
						<template v-if="item.component=='input'" >
							<el-input v-model="form[item.name]" :placeholder="item.options.placeholder" clearable :maxlength="item.options.maxlength" show-word-limit></el-input>
						</template>
						<!-- checkbox -->
						<template v-else-if="item.component=='checkbox'" >
							<template v-if="item.name" >
								<el-checkbox v-model="form[item.name][_item.name]" :label="_item.label"  v-for="(_item, _index) in item.options.items" :key="_index"></el-checkbox>
							</template>
							<template v-else >
								<el-checkbox v-model="form[_item.name]" :label="_item.label"  v-for="(_item, _index) in item.options.items" :key="_index"></el-checkbox>
							</template>
						</template>
						<!-- checkboxGroup -->
						<template v-else-if="item.component=='checkboxGroup'" >
							<el-checkbox-group v-model="form[item.name]">
								<el-checkbox v-for="_item in item.options.items" :key="_item.value" :label="_item.value">{{_item.label}}</el-checkbox>
							</el-checkbox-group>
						</template>
						<!-- upload -->
						<template v-else-if="item.component=='upload'" >
							<el-col v-for="(_item, _index) in item.options.items" :key="_index">
								<el-form-item :prop="_item.name">
									<sc-upload v-model="form[_item.name]" :title="_item.label"></sc-upload>
								</el-form-item>
							</el-col>
						</template>
						<!-- switch -->
						<template v-else-if="item.component=='switch'" >
							<el-switch v-model="form[item.name]" />
						</template>
						<!-- select -->
						<template v-else-if="item.component=='select'" >
							<el-select v-model="form[item.name]" :multiple="item.options.multiple" :placeholder="item.options.placeholder" clearable filterable style="width: 100%;">
								<el-option v-for="option in item.options.items" :key="option.value" :label="option.label" :value="option.value"></el-option>
							</el-select>
						</template>
						<!-- cascader -->
						<template v-else-if="item.component=='cascader'" >
							<el-cascader v-model="form[item.name]" :options="item.options.items" clearable></el-cascader>
						</template>
						<!-- date -->
						<template v-else-if="item.component=='date'" >
							<el-date-picker v-model="form[item.name]" :type="item.options.type" :shortcuts="item.options.shortcuts" :default-time="item.options.defaultTime" :value-format="item.options.valueFormat" :placeholder="item.options.placeholder || '请选择'"></el-date-picker>
						</template>
						<!-- number -->
						<template v-else-if="item.component=='number'" >
							<el-input-number v-model="form[item.name]" controls-position="right"></el-input-number>
						</template>
						<!-- radio -->
						<template v-else-if="item.component=='radio'" >
							<el-radio-group v-model="form[item.name]">
								<el-radio v-for="_item in item.options.items" :key="_item.value" :label="_item.value">{{_item.label}}</el-radio>
							</el-radio-group>
						</template>
						<!-- color -->
						<template v-else-if="item.component=='color'" >
							<el-color-picker v-model="form[item.name]" />
						</template>
						<!-- rate -->
						<template v-else-if="item.component=='rate'" >
							<el-rate style="margin-top: 6px;" v-model="form[item.name]"></el-rate>
						</template>
						<!-- slider -->
						<template v-else-if="item.component=='slider'" >
							<el-slider v-model="form[item.name]" :marks="item.options.marks"></el-slider>
						</template>
						<!-- tableselect -->
						<template v-else-if="item.component=='tableselect'" >
							<tableselect-render v-model="form[item.name]" :item="item"></tableselect-render>
						</template>
						<!-- editor -->
						<template v-else-if="item.component=='editor'" >
							<sc-editor v-model="form[item.name]" placeholder="请输入" :height="400"></sc-editor>
						</template>
						<!-- noComponent -->
						<template v-else>
							<el-tag type="danger">[{{item.component}}] Component not found</el-tag>
						</template>
						<div v-if="item.message" class="el-form-item-msg">{{item.message}}</div>
					</el-form-item>
				</el-col>
			</template>
			<el-col :span="24">
				<el-form-item>
					<slot>
						<el-button type="primary" @click="submit">提交</el-button>
					</slot>
				</el-form-item>
			</el-col>
		</el-row>
	</el-form>
</template>

<script> 
	import { request } from "@/utils/request"

	import { defineAsyncComponent } from 'vue'
	const tableselectRender = defineAsyncComponent(() => import('./items/tableselect'))
	const scEditor = defineAsyncComponent(() => import('@/components/scEditor'))

	export default {
		props: {
			modelValue: { type: Object, default: () => {} },
			config: { type: Object, default: () => {} },
			loading: { type: Boolean, default: false },
		},
		components: {
			tableselectRender,
			scEditor
		},
		data() {
			return {
				form: {},
				renderLoading: false
			}
		},
		watch:{
			modelValue(){
				if(this.hasConfig){
					this.deepMerge(this.form, this.modelValue)
				}
			},
			config(){
				this.render()
			},
			form:{
				handler(val){
					this.$emit("update:modelValue", val)
				},
				deep: true
			}
		},
		computed: {
			hasConfig(){
				return Object.keys(this.config).length>0
			},
			hasValue(){
				return Object.keys(this.modelValue).length>0
			}
		},
		created() {

		},
		mounted() {
			if(this.hasConfig){
				this.render()
			}
		},
		methods: {
			//构建form对象
			render() {
				this.config.formItems.forEach((item) => {
					if(item.component == 'checkbox'){
						if(item.name){
							const value = {}
							item.options.items.forEach((option) => {
								 value[option.name] = option.value
							})
							this.form[item.name] = value
						}else{
							item.options.items.forEach((option) => {
								 this.form[option.name] = option.value
							})
						}
					}else if(item.component == 'upload'){
						if(item.name){
							const value = {}
							item.options.items.forEach((option) => {
								 value[option.name] = option.value
							})
							this.form[item.name] = value
						}else{
							item.options.items.forEach((option) => {
								 this.form[option.name] = option.value
							})
						}
					}else{
						this.form[item.name] = item.value
					}
				})
				if(this.hasValue){
					this.form = this.deepMerge(this.form, this.modelValue)
				}
				this.getData()
			},
			//处理远程选项数据
			getData() {
				this.renderLoading = true
				var remoteData = []
				this.config.formItems.forEach((item) => {
					if(item.options && item.options.remote){
						var req = request({url: item.options.remote.api, params :item.options.remote.data}).then(res=>{
							item.options.items = res.data
						})
						remoteData.push(req)
					}
				})
				Promise.all(remoteData).then(()=>{
					this.renderLoading = false
				})
			},
			//合并深结构对象
			deepMerge(obj1, obj2) {
				let key;
				for (key in obj2) {
					obj1[key] = obj1[key] && obj1[key].toString() === "[object Object]" && (obj2[key] && obj2[key].toString() === "[object Object]") ? this.deepMerge(obj1[key], obj2[key]) : (obj1[key] = obj2[key])
				}
				return obj1
				//return JSON.parse(JSON.stringify(obj1))
			},
			//处理动态隐藏
			hideHandle(item){
				if(item.hideHandle){
					const exp = eval(item.hideHandle.replace(/\$/g,"this.form"))
					return exp
				}
				return false
			},
			//处理动态必填
			rulesHandle(item){
				if(item.requiredHandle){
					const exp = eval(item.requiredHandle.replace(/\$/g,"this.form"))
					var requiredRule = item.rules.find(t => 'required' in t)
					requiredRule.required = exp
				}
				return item.rules
			},
			//数据验证
			validate(valid, obj){
				return this.$refs.form.validate(valid, obj)
			},
			scrollToField(prop){
				return this.$refs.form.scrollToField(prop)
			},
			resetFields(){
				return this.$refs.form.resetFields()
			},
			//提交
			submit(){
				this.$emit("submit", this.form)
			}
		}
	}
</script>

<style>
</style>
