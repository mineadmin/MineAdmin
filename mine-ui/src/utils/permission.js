import tool from '@/utils/tool';

let permission = (data) => {
	let codes = tool.data.get('user').codes;
	if(! codes){
		return false
	}

	if (codes[0] === '*') {
		return true
	}

	return codes.includes(data) ? true : false;
}
export default permission;