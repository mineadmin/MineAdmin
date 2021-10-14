import scuiicon from '@/assets/font/scicon/iconfont.json'

//图标选择器配置
export default {
	icons: [
		{
			name: 'SCUI',
			namespace: 'sc-icon-',
			icons: scuiicon.glyphs.map(v => v.font_class)
		},
		{
			name: '扩展',
			namespace: 'el-icon-',
			icons: ['platform-eleme']
		}
	]
}
