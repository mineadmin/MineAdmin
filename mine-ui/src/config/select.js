import API from "@/api";

//字典选择器配置

export default {
	dicApiObj: API.dataDict.getDict,		//获取字典接口对象
	parseData: function (res) {
		return {
			data: res.data,				//分析行数据字段结构
			msg:  res.message,			//分析描述字段结构
			code: res.code				//分析状态字段结构
		}
	},
	request: {
		name: 'code'					//规定搜索字段
	},
	props: {
		label: 'label',					//映射label显示字段
		value: 'value',					//映射value值字段
	}
}
