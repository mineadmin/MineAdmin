import API from "@/api";

//上传配置

export default {
	api: {
		uploadImage:API.upload.uploadImage, 				//上传图片请求API对象
		uploadFile: API.upload.uploadFile					//上传文件请求API对象
	},			
	maxSize: 2,												//最大文件大小 默认2MB
	parseData: function (res) {
		return {
			code: res.code,									//分析状态字段结构
			src:  res.data.url,								//分析图片远程地址结构
			msg:  res.message								//分析描述字段结构
		}
	}
}
