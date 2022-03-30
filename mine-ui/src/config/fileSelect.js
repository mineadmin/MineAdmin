import API from "@/api"

//文件选择器配置

export default {
	apiObj: API.upload.uploadImage,
	menuApiObj: API.dataDict.getDict,
	listApiObj: API.attachment.getPageList,
	successCode: 200,
	max: 99,

	uploadParseData: function (res) {
		return {
			id: res.data.id,
			fileName: res.data.object_name,
			url: res.data.url
		}
	},
	listParseData: function (res) {
		return {
			rows: res.data.items,
			total: res.data.pageInfo.total,
			msg: res.message,
			code: res.code
		}
	},
	request: {
		page: 'page',
		pageSize: 'pageSize',
		keyword: 'origin_name',
		menuKey: 'mime_type',
		upPath: 'path',
	},
	menuProps: {
		key: 'value',
		label: 'label',
		children: 'children'
	},
	fileProps: {
		key: 'object_name',
		fileName: 'origin_name',
		url: 'url'
	},
	files: {
		doc: {
			icon: 'sc-icon-file-word-2-fill',
			color: '#409eff'
		},
		docx: {
			icon: 'sc-icon-file-word-2-fill',
			color: '#409eff'
		},
		xls: {
			icon: 'sc-icon-file-excel-2-fill',
			color: '#67C23A'
		},
		xlsx: {
			icon: 'sc-icon-file-excel-2-fill',
			color: '#67C23A'
		},
		ppt: {
			icon: 'sc-icon-file-ppt-2-fill',
			color: '#F56C6C'
		},
		pptx: {
			icon: 'sc-icon-file-ppt-2-fill',
			color: '#F56C6C'
		}
	}
}
