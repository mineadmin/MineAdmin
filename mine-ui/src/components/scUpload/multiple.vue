<template>
	<div class="sc-upload-multiple">
		<div class="sc-upload-list">
			<ul>
				<li v-for="(file, index) in fileList" :key="index">
					<div v-if="file.status!='success'" class="sc-upload-item" v-loading="true" element-loading-background="rgba(0, 0, 0, 0.5)">
						<el-image class="image" :src="file.tempImg" fit="cover" :z-index="9999"></el-image>
					</div>
					<div v-else class="sc-upload-item">
						<div class="mask">
							<span class="del" @click.stop="del(index)"><el-icon><el-icon-delete /></el-icon></span>
						</div>
						<el-image class="image" :src="file.url" fit="cover" :preview-src-list="preview" hide-on-click-modal append-to-body>
							<template #placeholder>
								<div class="image-slot">
									<el-icon><el-icon-more-filled /></el-icon>
								</div>
							</template>
						</el-image>
					</div>
				</li>
			</ul>
		</div>
		<div class="sc-upload-uploader" @click="fileSelect && showfileSelect()">
			<el-upload ref="upload" class="uploader" :disabled="fileSelect" :action="action" :accept="accept" multiple  :show-file-list="false" :file-list="defaultFileList" :before-upload="before" :on-progress="progress" :on-success="success" :on-change="change" :on-remove="remove" :on-error="error" :http-request="request">
				<div class="file-empty">
					<el-icon><component :is="icon" /></el-icon>
					<h4 v-if="title">{{title}}</h4>
				</div>
			</el-upload>
		</div>
		<el-dialog title="打开" v-model="fileSelectDialogVisible" :width="880" destroy-on-close>
			<sc-file-select multiple @submit="fileSelectSubmit">
				<template #do>
					<el-button @click="fileSelectDialogVisible=false" >取 消</el-button>
				</template>
			</sc-file-select>
		</el-dialog>
		<span style="display:none!important"><el-input v-model="value"></el-input></span>

	</div>
</template>

<script>
	import { defineAsyncComponent } from 'vue'
	import config from "@/config/upload";
	const scFileSelect = defineAsyncComponent(() => import('@/components/scFileSelect'))

	export default {
		props: {
			modelValue: { type: String, default: "" },
			action: { type: String, default: "" },
			apiObj: { type: Object, default: () => {} },
			accept: { type: String, default: "image/gif, image/jpeg, image/png" },
			maxSize: { type: Number, default: config.maxSize },
			title: { type: String, default: "" },
			icon: { type: String, default: "el-icon-plus" },
			fileSelect: { type: Boolean, default: false }
		},
		components: {
			scFileSelect
		},
		data(){
			return {
				value: "",
				defaultFileList: [],
				fileList: [],
				fileSelectDialogVisible: false,
			}
		},
		watch:{
			modelValue(){
				this.$refs.upload.uploadFiles=this.modelValuetoArr
				this.fileList = this.modelValuetoArr
				this.value = this.modelValue
			},
			fileList: {
				handler(){
					if(this.isAllSuccess){
						this.$emit('update:modelValue', this.fileListtoStr);
					}
				},
				deep: true
			}
		},
		computed: {
			modelValuetoArr(){
				return this.toArr(this.modelValue)
			},
			fileListtoStr(){
				return this.toStr(this.fileList)
			},
			preview(){
				return this.fileList.map(v => v.url)
			},
			isAllSuccess(){
				var all_length = this.fileList.length;
				var success_length = 0
				this.fileList.forEach(item => {
					if(item.status == "success"){
						success_length += 1
					}
				})
				return success_length == all_length
			}
		},
		mounted() {
			this.defaultFileList = this.toArr(this.modelValue);
			this.fileList = this.toArr(this.modelValue)
			this.value = this.modelValue
		},
		methods: {
			showfileSelect(){
				this.fileSelectDialogVisible = true
			},
			fileSelectSubmit(val){
				const newval = [...this.modelValue.split(","),...val].join(",")
				this.$emit('update:modelValue', newval);
				this.fileSelectDialogVisible = false
			},
			//默认值转换为数组
			toArr(str){
				var _arr = [];
				var arr = str.split(",");
				arr.forEach(item => {
					if(item){
						_arr.push({
							name: "F",
							status: "success",
							url: item
						})
					}
				})
				return _arr;
			},
			//数组转换为原始值
			toStr(arr){
				var _arr = [];
				arr.forEach(item => {
					_arr.push(item.url)
				})
				var str = _arr.join(",")
				return str;
			},
			before(file){
				const maxSize = file.size / 1024 / 1024 < this.maxSize;
				if (!maxSize) {
					this.$message.warning(`上传文件大小不能超过 ${this.maxSize}MB!`);
					return false;
				}
			},
			change(file, fileList){
				file.tempImg = URL.createObjectURL(file.raw);
				this.fileList = fileList
			},
			success(res, file){
				var response = config.parseData(res);
				file.url = response.src
			},
			progress(){

			},
			remove(){

			},
			error(err){
				this.$notify.error({
					title: '上传文件错误',
					message: err
				})
			},
			del(index){
				this.fileList.splice(index, 1);
			},
			request(param){
				var apiObj = config.apiObj;
				if(this.apiObj){
					apiObj = this.apiObj;
				}
				const data = new FormData();
				data.append("file", param.file);
				apiObj.post(data).then(res => {
					param.onSuccess(res)
				}).catch(err => {
					param.onError(err)
				})
			}
		}
	}
</script>

<style scoped>

	.el-form-item.is-error .sc-upload-uploader {border: 1px dashed #F56C6C;}

	.sc-upload-multiple {display: inline-block;}


	.sc-upload-list {display: inline-block;}
	.sc-upload-list li {list-style: none;display: inline-block;width: 120px;height: 120px;margin-right: 10px;vertical-align: top;box-sizing: border-box;}

	.sc-upload-item {position: relative;width: 100%;height: 100%;}
	.sc-upload-item .mask {display: none;position: absolute;top:0px;right:0px;line-height: 1;z-index: 1;}
	.sc-upload-item .mask span {display: inline-block;width: 25px;height:25px;line-height: 23px;text-align: center;cursor: pointer;color: #fff;}
	.sc-upload-item .mask span i {font-size: 12px;}
	.sc-upload-item .mask .del {background: #F56C6C;}

	.sc-upload-item:hover .mask {display: inline-block;}


	.sc-upload-list .image {width: 100%;height: 100%;}

	.sc-upload-list .image-slot {display: flex;justify-content: center;align-items: center;width: 100%;height: 100%;background: #f5f7fa;color: #909399;}
	.sc-upload-list .image-slot i {font-size: 20px;}

	.sc-upload-uploader {border: 1px dashed #d9d9d9;width: 120px;height: 120px;display: inline-block;vertical-align: top;box-sizing: border-box;}
	.sc-upload-uploader:hover {border: 1px dashed #409eff;}
	.sc-upload-uploader .uploader {width: 100%;height: 100%;}
	.sc-upload-uploader:deep(.el-upload) {width: 100%;height: 100%;}


	.sc-upload-uploader .file-empty {width: 100%;height: 100%;line-height: 1;display: flex;flex-direction: column;align-items: center;justify-content: center;}
	.sc-upload-uploader .file-empty i {font-size: 28px;color: #8c939d;}
	.sc-upload-uploader .file-empty h4 {font-size: 12px;font-weight: normal;color: #8c939d;margin-top: 10px;}


</style>
