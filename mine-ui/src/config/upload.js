import API from "@/api";

//上传配置

export default {
	api: {
		uploadImage:API.upload.uploadImage, 				//上传图片请求API对象
		uploadFile: API.upload.uploadFile					//上传文件请求API对象
	},
	successCode: 200,
	images: '.jpg,.jpeg,.png,.bmp,.gif,.svg',
	files : '.txt,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.rar,.zip,.7z,.gz,.pdf,.wps,.md',
	maxSize: 4,												//最大文件大小，单位M
	parseData: function (res) {
		return {
			code: res.code,									//分析状态字段结构
			src:  res.data.url,								//分析图片远程地址结构
			msg:  res.message								//分析描述字段结构
		}
	}
}
