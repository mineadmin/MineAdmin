<template>
	<div class="node-wrap">
		<div class="node-wrap-box" @click="show">
			<div class="title" style="background: #ff943e;">
				<i class="icon el-icon-user-solid"></i>
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
						<el-form-item label="审批人员类型">
							<el-radio-group v-model="form.settype" class="clear">
								<el-radio :label="1">指定成员</el-radio>
								<el-radio :label="2">主管</el-radio>
								<el-radio :label="4">发起人自选</el-radio>
								<el-radio :label="5">发起人自己</el-radio>
								<el-radio :label="7">连续多级主管</el-radio>
							</el-radio-group>
						</el-form-item>
						<el-divider></el-divider>
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
				if(nodeConfig.settype == 1){
					if (nodeConfig.nodeUserList && nodeConfig.nodeUserList.length>0) {
						const users = nodeConfig.nodeUserList.map(item=>item.name).join(" 或 ")
						return users
					}else{
						return false
					}
				}else if (nodeConfig.settype == 2) {
					return "直接主管"
				}else if (nodeConfig.settype == 4) {
					return "发起人自选"
				}else if (nodeConfig.settype == 5) {
					return "发起人自己"
				}else if (nodeConfig.settype == 7) {
					return "连续多级主管"
				}
			}
		}
	}
</script>

<style>
</style>
