<template>
	<div class="sc-upload" v-loading="loading" element-loading-background="rgba(0, 0, 0, 0.5)" :style="style">
		<div v-if="tempImg || img" class="sc-upload-file">
			<div class="mask">
				<span class="del" @click.stop="del"><el-icon><el-icon-delete /></el-icon></span>
			</div>
			<el-image v-if="fileIsImg" class="image" :src="tempImg || img" :preview-src-list="[img]" fit="cover" hide-on-click-modal preview-teleported :z-index="9999"></el-image>
			<a v-else :href="img" class="file" target="_blank"><el-icon><el-icon-document /></el-icon></a>
		</div>
		<div v-else class="sc-upload-uploader" @click="fileSelect && showfileSelect()">
			<el-upload ref="upload" class="uploader" :disabled="fileSelect" :auto-upload="!cropper" :on-change="change" :accept="accept" :action="action" :show-file-list="false" :before-upload="before" :on-success="success" :on-error="error" :http-request="request">
				<slot>
					<div class="file-empty">
						<el-icon><component :is="icon" /></el-icon>
						<h4 v-if="title">{{title}}</h4>
					</div>
				</slot>
			</el-upload>
		</div>
		<el-dialog title="剪裁" v-model="cropperDialogVisible" :width="580" destroy-on-close>
			<sc-cropper :src="cropperImg" :compress="compress" :aspectRatio="aspectRatio" ref="cropper"></sc-cropper>
			<template #footer>
				<el-button @click="cropperDialogVisible=false" >取 消</el-button>
				<el-button type="primary" @click="cropperSave">确 定</el-button>
			</template>
		</el-dialog>
		<el-dialog title="打开" v-model="fileSelectDialogVisible" :width="880" destroy-on-close>
			<sc-file-select @submit="fileSelectSubmit">
				<template #do>
					<el-button @click="fileSelectDialogVisible=false" >取 消</el-button>
				</template>
			</sc-file-select>
		</el-dialog>
		<span style="display:none!important"><el-input v-model="img"></el-input></span>

	</div>
</template>

<script>
	import { defineAsyncComponent } from 'vue'
	import config from "@/config/upload"
	const scCropper = defineAsyncComponent(() => import('@/components/scCropper'))
	const scFileSelect = defineAsyncComponent(() => import('@/components/scFileSelect'))

	export default {
		props: {
			height: {type: Number, default: 120},
			width: {type: Number, default: 120},
			modelValue: { type: String, default: "" },
			action: { type: String, default: "" },
			api: { type: Object, default: () => {} },
			type: { type: String, default: 'image'},
			accept: { type: String, default: "image/gif, image/jpeg, image/png, image/bmp, image/jpg" },
			maxSize: { type: Number, default: config.maxSize },
			title: { type: String, default: "" },
			icon: { type: String, default: "el-icon-plus" },
			fileSelect: { type: Boolean, default: false },
			cropper: { type: Boolean, default: false },
			compress: {type: Number, default: 1},
			aspectRatio:  {type: Number, default: NaN},
			onSuccess: { type: Function, default: () => { return true } }
		},
		components: {
			scCropper,
			scFileSelect
		},
		data() {
			return {
				loading: false,
				fileIsImg: true,
				img: "",
				tempImg: "",
				style: {
					width: this.width + "px",
					height: this.height + "px"
				},
				cropperDialogVisible: false,
				cropperImg: "",
				cropperUploadFile: null,
				fileSelectDialogVisible: false,
			}
		},
		watch:{
			modelValue(){
				this.isImg(this.modelValue)
				this.img = this.modelValue;
			},
			img(){
				this.$emit('update:modelValue', this.img);
			}
		},
		mounted() {
			this.isImg(this.modelValue)
			this.img = this.modelValue;
		},
		methods: {
			showfileSelect(){
				this.fileSelectDialogVisible = true
			},
			fileSelectSubmit(val){
				this.img = this.$TOOL.viewImage(val)
				this.fileSelectDialogVisible = false
			},
			cropperSave(){
				var uploadFile = this.$refs.upload.uploadFiles[0].raw
				this.$refs.cropper.getCropFile(file => {
					this.cropperUploadFile = file
					this.$refs.upload.submit()
				}, uploadFile.name, uploadFile.type)
				this.cropperDialogVisible = false
			},
			isImg(fileUrl){
				var strRegex = "(.jpg|.png|.gif|.jpeg)$";
				var re = new RegExp(strRegex);
				if (fileUrl && re.test(fileUrl.toLowerCase())){
					this.fileIsImg=true;
				}else{
					this.fileIsImg=false;
				}
			},
			change(file){
				if(this.cropper && file.status=='ready'){
					this.isImg(file.name)
					if(!this.fileIsImg){
						this.$notify.warning({
							title: '上传文件警告',
							message: '选择的文件非图像类文件'
						})
						return false
					}
					this.cropperDialogVisible = true
					this.cropperImg = URL.createObjectURL(file.raw)
				}
			},
			before(file){
				file = this.cropper ? this.cropperUploadFile : file
				const maxSize = file.size / 1024 / 1024 < this.maxSize;
				if (!maxSize) {
					this.$message.warning(`上传文件大小不能超过 ${this.maxSize}MB!`);
					return false;
				}
				this.isImg(file.name)
				this.tempImg = URL.createObjectURL(file);
				this.loading = true;
			},
			success(res){
				this.loading = false;
				this.tempImg = "";
				var os = this.onSuccess(res);
				if(os!=undefined && os==false){
					return false;
				}
				var response = config.parseData(res);
				if(response.code != config.successCode){
					this.$message.warning(response.msg || "上传文件未知错误")
				}else{
					this.img = response.src;
				}
			},
			error(err){
				this.$notify.error({
					title: '上传文件错误',
					message: err
				})
				this.loading = false;
				this.tempImg = "";
				this.img = ""
			},
			del(){
				this.img = ""
			},
			request(param){
				let api = config.api;
				if(this.api){
					api = this.api;
				}
				const data = new FormData();
				var file = this.cropper ? this.cropperUploadFile : param.file
				data.append(this.type, file);
				if (this.type === 'image') {
					api.uploadImage(data).then(res => {
						this.$emit('success', res.data)
						param.onSuccess(res)
					}).catch(err => {
						param.onError(err)
					})
				} else {
					api.uploadFile(data).then(res => {
						this.$emit('success', res.data)
						param.onSuccess(res)
					}).catch(err => {
						param.onError(err)
					})
				}
			}
		}
	}
</script>

<style>
	.sc-upload+.sc-upload {margin-left: 10px;}
</style>

<style scoped>
	.el-form-item.is-error .sc-upload-uploader {border: 1px dashed #F56C6C;}

	.sc-upload {width: 120px;height: 120px;display: inline-block;vertical-align: top;box-sizing: border-box;}

	.sc-upload-file {position: relative;width: 100%;height: 100%;}
	.sc-upload-file .mask {display: none;position: absolute;top:0px;right:0px;line-height: 1;z-index: 1;}
	.sc-upload-file .mask span {display: inline-block;width: 25px;height:25px;line-height: 23px;text-align: center;cursor: pointer;color: #fff;}
	.sc-upload-file .mask span i {font-size: 12px;}
	.sc-upload-file .mask .del {background: #F56C6C;}
	.sc-upload-file .image {width: 100%;height: 100%;}
	.sc-upload-file .image img {vertical-align: bottom;}
	.sc-upload-file .file {width: 100%;height: 100%;display: flex;flex-direction: column;align-items: center;justify-content: center;border: 1px solid #DCDFE6;}
	.sc-upload-file .file i {font-size: 30px;color: #409EFF;}
	.sc-upload-file:hover .mask {display: inline-block;}

	.sc-upload-uploader {border: 1px dashed #d9d9d9;box-sizing: border-box;width: 100%;height: 100%;}
	.sc-upload-uploader:hover {border: 1px dashed #409eff;}
	.sc-upload-uploader .uploader {width: 100%;height: 100%;}
	.sc-upload-uploader:deep(.el-upload) {width: 100%;height: 100%;}

	.sc-upload-uploader .file-empty {width: 100%;height: 100%;line-height: 1;display: flex;flex-direction: column;align-items: center;justify-content: center;}
	.sc-upload-uploader .file-empty i {font-size: 28px;color: #8c939d;}
	.sc-upload-uploader .file-empty h4 {font-size: 12px;font-weight: normal;color: #8c939d;margin-top: 10px;}
</style>
