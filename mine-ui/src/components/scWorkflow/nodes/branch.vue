<template>
	<div class="branch-wrap">
		<div class="branch-box-wrap">
			<div class="branch-box">
				<el-button class="add-branch" type="success" plain round @click="addTerm">添加条件</el-button>
				<div class="col-box" v-for="(item,index) in nodeConfig.conditionNodes" :key="index">
					<div class="condition-node">
						<div class="condition-node-box">
							<div class="auto-judge" @click="show(index)">
								<div class="sort-left" v-if="index!=0" @click.stop="arrTransfer(index,-1)"><i class="el-icon-arrow-left"></i></div>
								<div class="title">
									<span class="node-title">{{ item.nodeName }}</span>
									<span class="priority-title">优先级{{item.priorityLevel}}</span>
									<i class="close el-icon-close" @click.stop="delTerm(index)"></i>
								</div>
								<div class="content">
									<span v-if="toText(nodeConfig, index)">{{ toText(nodeConfig, index) }}</span>
									<span v-else class="placeholder">请设置条件</span>
								</div>
								<div class="sort-right" v-if="index!=nodeConfig.conditionNodes.length-1" @click.stop="arrTransfer(index)"><i class="el-icon-arrow-right"></i></div>
							</div>
							<add-node v-model="item.childNode"></add-node>
						</div>
					</div>
					<slot v-if="item.childNode" :node="item"></slot>
					<div class="top-left-cover-line" v-if="index==0"></div>
					<div class="bottom-left-cover-line" v-if="index==0"></div>
					<div class="top-right-cover-line" v-if="index==nodeConfig.conditionNodes.length-1"></div>
					<div class="bottom-right-cover-line" v-if="index==nodeConfig.conditionNodes.length-1"></div>
				</div>
			</div>
			<add-node v-model="nodeConfig.childNode"></add-node>
		</div>
		<el-drawer title="条件设置" v-model="drawer" destroy-on-close append-to-body>
			<el-container>
				<el-main style="padding:0 20px 20px 20px">
					<el-form label-position="top">
						<el-form-item label="">
							<el-input v-model="form.nodeName"></el-input>
						</el-form-item>
						<el-divider></el-divider>
						<el-form-item label="条件">
							{{ nodeConfig.conditionNodes[index].conditionList }}
						</el-form-item>
						<el-divider></el-divider>
						<p><el-button type="primary">增加条件</el-button></p>
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
				index: 0,
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
			show(index){
				this.index = index
				this.form = {}
				this.form = {...this.nodeConfig.conditionNodes[index]}
				this.drawer = true
			},
			save(){
				this.nodeConfig.conditionNodes[this.index] = this.form
				this.$emit("update:modelValue", this.nodeConfig)
				this.drawer = false
			},
			addTerm(){
				let len = this.nodeConfig.conditionNodes.length + 1
				this.nodeConfig.conditionNodes.push({
					nodeName: "条件" + len,
					type: 3,
					priorityLevel: len
				})
			},
			delTerm(index){
				this.nodeConfig.conditionNodes.splice(index, 1)
				if (this.nodeConfig.conditionNodes.length == 1) {
					if (this.nodeConfig.childNode) {
						if (this.nodeConfig.conditionNodes[0].childNode) {
							this.reData(this.nodeConfig.conditionNodes[0].childNode, this.nodeConfig.childNode)
						}else{
							this.nodeConfig.conditionNodes[0].childNode = this.nodeConfig.childNode
						}
					}
					this.$emit("update:modelValue", this.nodeConfig.conditionNodes[0].childNode);
				}
			},
			reData(data, addData) {
				if (!data.childNode) {
					data.childNode = addData
				} else {
					this.reData(data.childNode, addData)
				}
			},
			arrTransfer(index, type = 1){
				this.nodeConfig.conditionNodes[index] = this.nodeConfig.conditionNodes.splice(index + type, 1, this.nodeConfig.conditionNodes[index])[0]
				this.nodeConfig.conditionNodes.map((item, index) => {
					item.priorityLevel = index + 1
				})
				this.$emit("update:modelValue", this.nodeConfig)
			},
			toText(nodeConfig, index){
				var { conditionList } = nodeConfig.conditionNodes[index]
				if (conditionList && conditionList.length > 0) {
					const text = conditionList.map(item => `${item.label}${item.operator}${item.value}`).join(" 和 ")
					return text
				}else{
					if(index == nodeConfig.conditionNodes.length - 1){
						return "其他条件进入此流程"
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
