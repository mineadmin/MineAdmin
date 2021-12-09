<!--
 * @Descripttion: 图标选择器组件
 * @version: 1.2
 * @Author: sakuya
 * @Date: 2021年7月27日10:02:46
 * @LastEditors: sakuya
 * @LastEditTime: 2021年11月16日09:35:34
-->

<template>
	<div class="sc-icon-select">
		<el-input v-model="defaultValue" :prefix-icon="defaultValue||'none'" :placeholder="placeholder" :clearable="clearable" :disabled="disabled">
			<template #append><el-button icon="el-icon-more-filled" @click="open"></el-button></template>
		</el-input>
		<el-dialog title="图标选择器" v-model="dialogVisible" :width="780" destroy-on-close>
			<el-tabs style="margin-top: -30px;">
				<el-tab-pane  v-for="item in data" :key="item.name" lazy>
					<template #label>
						{{item.name}} <el-tag size="mini" type="info">{{item.icons.length}}</el-tag>
					</template>
					<div class="sc-icon-select__list">
						<el-scrollbar>
							<ul @click="selectIcon">
								<li v-for="icon in item.icons" :key="icon">
									<span :data-icon="icon"></span>
									<el-icon><component :is="icon" /></el-icon>
								</li>
							</ul>
						</el-scrollbar>
					</div>
				</el-tab-pane>
			</el-tabs>
		</el-dialog>
	</div>
</template>

<script>
	import config from "@/config/iconSelect"

	export default {
		props: {
			modelValue: { type: String, default: "" },
			placeholder: { type: String, default: "请输入或者选择图标" },
			clearable: { type: Boolean, default: false },
			disabled: { type: Boolean, default: false },
		},
		data() {
			return {
				defaultValue: '',
				dialogVisible: false,
				data: []
			}
		},
		watch:{
			modelValue(val){
				this.defaultValue = val
			},
			defaultValue(val){
				this.$emit('update:modelValue', val)
			}
		},
		mounted() {
			this.defaultValue = this.modelValue
			this.data.push(...config.icons)
		},
		methods: {
			open(){
				if(this.disabled){
					return false
				}
				this.dialogVisible = true
			},
			selectIcon(e){
				if(e.target.tagName != 'SPAN'){
					return false
				}
				this.defaultValue = e.target.dataset.icon
				this.dialogVisible = false
				this.$emit('update:modelValue', this.defaultValue);
			}
		}
	}
</script>

<style scoped>
	.sc-icon-select {display: inline-block;}
	.sc-icon-select__list {height:360px;overflow: auto;}
	.sc-icon-select__list ul {}
	.sc-icon-select__list li {display: inline-block;width:80px;height:80px;margin:5px;vertical-align: top;box-shadow: 0 0 0 1px #eee;transition: all 0.1s;border-radius: 4px;position: relative;}
	.sc-icon-select__list li span {position: absolute;top:0;left:0;right:0;bottom:0;z-index: 1;cursor: pointer;}
	.sc-icon-select__list li i {display: inline-block;width: 100%;height:100%;font-size: 26px;color: #6d7882;background: #fff;display: flex;justify-content: center;align-items: center;border-radius: 4px;}
	.sc-icon-select__list li:hover {box-shadow: 0 0 1px 4px rgba(64,158,255,1);}
	.sc-icon-select__list li:hover i {color: #409EFF;}
</style>
