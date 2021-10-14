<template>
	<div class="node-wrap">
		<div class="node-wrap-box" @click="show">
			<div class="title" style="background: #3296fa;">
				<i class="icon el-icon-s-promotion"></i>
				<span>{{ nodeConfig.nodeName }}</span>
				<i class="close el-icon-close" @click.stop="delNode()"></i>
			</div>
			<div class="content">
				<span v-if="toText(nodeConfig)">{{ toText(nodeConfig) }}</span>
				<span v-else class="placeholder">请选择人员</span>
			</div>
		</div>
		<add-node v-model="nodeConfig.childNode"></add-node>
		<el-drawer title="审批人设置" v-model="drawer" destroy-on-close append-to-body>
			<el-container>
				<el-main style="padding:0 20px 20px 20px">
					<el-form label-position="top">
						<el-form-item label="">
							<el-input v-model="form.nodeName"></el-input>
						</el-form-item>
						<el-divider></el-divider>
						<el-form-item label="">
							<el-checkbox v-model="form.ccSelfSelectFlag" label="允许发起人自选"></el-checkbox>
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
			},
			delNode(){
				this.$emit("update:modelValue", this.nodeConfig.childNode)
			},
			toText(nodeConfig){
				if (nodeConfig.nodeUserList && nodeConfig.nodeUserList.length>0) {
					const users = nodeConfig.nodeUserList.map(item=>item.name).join(" 或 ")
					return users
				}else{
					if(nodeConfig.ccSelfSelectFlag){
						return "发起人自选"
					}else{
						return false
					}

				}
			}
		}
	}
</script>

<style>
</style>
