<!--
 * @Descripttion: 表格选择器组件
 * @version: 1.1
 * @Author: sakuya
 * @Date: 2021年6月10日10:04:07
 * @LastEditors: sakuya
 * @LastEditTime: 2021年11月4日16:09:05
-->

<template>
	<el-select ref="select" v-model="defaultValue" clearable :multiple="multiple" filterable :placeholder="placeholder" :disabled="disabled" :filter-method="filterMethod" @remove-tag="removeTag" @visible-change="visibleChange" @clear="clear">
		<template #empty>
			<div class="sc-table-select__table" :style="{width: tableWidth+'px'}" v-loading="loading">
				<div class="sc-table-select__header">
					<slot name="header" :form="formData" :submit="formSubmit"></slot>
				</div>
				<el-table ref="table" :data="tableData" :height="245" :highlight-current-row="!multiple" @row-click="click" @select="select" @select-all="selectAll">
					<el-table-column v-if="multiple" type="selection" width="45"></el-table-column>
					<el-table-column v-else type="index" width="45">
						<template #default="scope"><span>{{scope.$index+(currentPage - 1) * pageSize + 1}}</span></template>
					</el-table-column>
					<slot></slot>
				</el-table>
				<div class="sc-table-select__page">
					<el-pagination small background layout="prev, pager, next" :total="total" :page-size="pageSize" v-model:currentPage="currentPage" @current-change="reload"></el-pagination>
				</div>
			</div>
		</template>
	</el-select>
</template>

<script>
	import config from "@/config/tableSelect";

	export default {
		props: {
			modelValue: null,
			apiObj: { type: Object, default: () => {} },
			params: { type: Object, default: () => {} },
			placeholder: { type: String, default: "请选择" },
			multiple: { type: Boolean, default: false },
			disabled: { type: Boolean, default: false },
			tableWidth: {type: Number, default: 400},
			mode: { type: String, default: "popover" },
			props: { type: Object, default: () => {} }
		},
		data() {
			return {
				loading: false,
				keyword: null,
				defaultValue: [],
				tableData: [],
				pageSize: config.pageSize,
				total: 0,
				currentPage: 1,
				defaultProps: {
					label: config.props.label,
					value: config.props.value,
					page: config.request.page,
					pageSize: config.request.pageSize,
					keyword: config.request.keyword
				},
				formData: {}
			}
		},
		computed: {

		},
		watch: {
			modelValue:{
				handler(){
					this.defaultValue = this.modelValue
					this.autoCurrentLabel()
				},
				deep: true
			}
		},
		mounted() {
			this.defaultProps = Object.assign(this.defaultProps, this.props);
			this.defaultValue =  this.modelValue
			this.autoCurrentLabel()
		},
		methods: {
			//表格显示隐藏回调
			visibleChange(visible){
				if(visible){
					this.currentPage = 1
					this.keyword = null
					this.formData = {}
					this.getData()
				}else{
					this.autoCurrentLabel()
				}
			},
			//获取表格数据
			async getData(){
				this.loading = true;
				var reqData = {
					[this.defaultProps.page]: this.currentPage,
					[this.defaultProps.pageSize]: this.pageSize,
					[this.defaultProps.keyword]: this.keyword
				}
				Object.assign(reqData, this.params, this.formData)
				var res = await this.apiObj.get(reqData);
				var parseData = config.parseData(res)
				this.tableData = parseData.rows;
				this.total = parseData.total;
				this.loading = false;
				//表格默认赋值
				this.$nextTick(() => {
					if(this.multiple){
						this.defaultValue.forEach(row => {
							var setrow = this.tableData.filter(item => item[this.defaultProps.value]===row[this.defaultProps.value] )
							if(setrow.length > 0){
								this.$refs.table.toggleRowSelection(setrow[0], true);
							}
						})
					}else{
						var setrow = this.tableData.filter(item => item[this.defaultProps.value]===this.defaultValue[this.defaultProps.value] )
						this.$refs.table.setCurrentRow(setrow[0]);
					}
					this.$refs.table.$el.querySelector('.el-table__body-wrapper').scrollTop = 0
				})
			},
			//插糟表单提交
			formSubmit(){
				this.currentPage = 1
				this.keyword = null
				this.getData()
			},
			//分页刷新表格
			reload(){
				this.getData()
			},
			//自动模拟options赋值
			autoCurrentLabel(){
				this.$nextTick(() => {
					if(this.multiple){
						this.$refs.select.selected.forEach(item => {
							item.currentLabel = item.value[this.defaultProps.label]
						})
					}else{
						this.$refs.select.selectedLabel = this.defaultValue[this.defaultProps.label]
					}
				})
			},
			//表格勾选事件
			select(rows, row){
				var isSelect = rows.length && rows.indexOf(row) !== -1
				if(isSelect){
					this.defaultValue.push(row)
				}else{
					this.defaultValue.splice(this.defaultValue.findIndex(item => item[this.defaultProps.value] == row[this.defaultProps.value]), 1)
				}
				this.autoCurrentLabel()
				this.$emit('update:modelValue', this.defaultValue);
			},
			//表格全选事件
			selectAll(rows){
				var isAllSelect = rows.length > 0
				if(isAllSelect){
					rows.forEach(row => {
						var isHas = this.defaultValue.find(item => item[this.defaultProps.value] == row[this.defaultProps.value])
						if(!isHas){
							this.defaultValue.push(row)
						}
					})
				}else{
					this.tableData.forEach(row => {
						var isHas = this.defaultValue.find(item => item[this.defaultProps.value] == row[this.defaultProps.value])
						if(isHas){
							this.defaultValue.splice(this.defaultValue.findIndex(item => item[this.defaultProps.value] == row[this.defaultProps.value]), 1)
						}
					})
				}
				this.autoCurrentLabel()
				this.$emit('update:modelValue', this.defaultValue);
			},
			click(row){
				if(this.multiple){
					//处理多选点击行
				}else{
					this.defaultValue = row
					this.$refs.select.blur()
					this.autoCurrentLabel()
					this.$emit('update:modelValue', this.defaultValue);
				}
			},
			//tags删除后回调
			removeTag(tag){
				var row = this.findRowByKey(tag[this.defaultProps.value])
				this.$refs.table.toggleRowSelection(row, false);
				this.$emit('update:modelValue', this.defaultValue);
			},
			//清空后的回调
			clear(){
				this.$emit('update:modelValue', this.defaultValue);
			},
			// 关键值查询表格数据行
			findRowByKey (value) {
				return this.tableData.find(item => item[this.defaultProps.value] === value)
			},
			filterMethod(keyword){
				if(!keyword){
					this.keyword = null;
					return false;
				}
				this.keyword = keyword;
				this.getData()
			}

		}
	}
</script>

<style scoped>
	.sc-table-select__table {padding:12px;}
	.sc-table-select__page {padding-top: 12px;}
</style>
