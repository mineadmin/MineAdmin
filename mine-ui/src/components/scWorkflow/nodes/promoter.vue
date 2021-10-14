<template>
	<div class="node-wrap">
		<div class="node-wrap-box start-node" @click="show">
			<div class="title" style="background: #576a95;">
				<i class="icon el-icon-user-solid"></i>
				<span>{{ nodeConfig.nodeName }}</span>
			</div>
			<div class="content">所有人</div>
		</div>
		<add-node v-model="nodeConfig.childNode"></add-node>
		<el-drawer title="发起人" v-model="drawer" destroy-on-close append-to-body>
			<el-container>
				<el-main style="padding:0 20px 20px 20px">
					<el-form label-position="top">
						<el-form-item label="">
							<el-input v-model="form.nodeName"></el-input>
						</el-form-item>
					</el-form>
				</el-main>
				<el-footer>
					<el-button type="primary" @click="save">保存</el-button>
					<el-button @click="drawer=false">取消</el-button>
				</el-footer>
			</el-container>
		</el-drawer>
	</div>
</template>

<script>
	import addNode from './addNode'

	export default {
		props: {
			modelValue: { type: Object, default: () => {} }
		},
		components: {
			addNode
		},
		data() {
			return {
				nodeConfig: {},
				drawer: false,
				form: {}
			}
		},
		watch:{
			modelValue(){
				this.nodeConfig = this.modelValue
			}
		},
		mounted() {
			this.nodeConfig = this.modelValue
		},
		methods: {
			show(){
				this.form = {}
				this.form = {...this.nodeConfig}
				this.drawer = true
			},
			save(){
				this.$emit("update:modelValue", this.form)
				this.drawer = false
			}
		}
	}
</script>

<style>
</style>
