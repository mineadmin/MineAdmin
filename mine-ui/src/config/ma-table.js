//数据表格配置
import tool from '@/utils/tool'

export default {

	pageSize: 20,						//表格每一页条数

	parseData: function (res) {			//数据分析
		if (res.data.items) {
			return {
				rows: res.data.items,			//分析行数据字段结构
				total: res.data.pageInfo.total,	//分析总数字段结构
				message: res.message,			//分析描述字段结构
				code: res.code					//分析状态字段结构
			}
		} else {
			return {
				rows: res.data,
				message: res.message,
				code: res.code
			}
		}
	},

	request: {							//请求规定字段
		page: 'page',					//规定当前分页字段
		pageSize: 'pageSize',			//规定一页条数字段
		prop: 'orderBy',					//规定排序字段名字段
		order: 'orderType'					//规定排序规格字段
	},

	/**
	 * 自定义列保存处理
	 * @tableName scTable组件的props->tableName
	 * @column 用户配置好的列
	 */
     columnSettingSave: function (tableName, column) {
		return new Promise((resolve) => {
			setTimeout(()=>{
				//这里为了演示使用了session和setTimeout演示，开发时应用数据请求
				tool.session.set(tableName, column)
				resolve(true)
			},1000)
		})
	},
	/**
	 * 获取自定义列
	 * @tableName scTable组件的props->tableName
	 * @column 组件接受到的props->column
	 */
	columnSettingGet: function (tableName, column) {
		return new Promise((resolve) => {
			//这里为了演示使用了session和setTimeout演示，开发时应用数据请求
			const userColumn = tool.session.get(tableName)
			if(userColumn){
				resolve(userColumn)
			}else{
				resolve(column)
			}
		})
	},
	/**
	 * 重置自定义列
	 * @tableName scTable组件的props->tableName
	 * @column 组件接受到的props->column
	 */
	columnSettingReset: function (tableName, column) {
		return new Promise((resolve) => {
			//这里为了演示使用了session和setTimeout演示，开发时应用数据请求
			setTimeout(()=>{
				tool.session.remove(tableName)
				resolve(column)
			},1000)
		})
	}
}