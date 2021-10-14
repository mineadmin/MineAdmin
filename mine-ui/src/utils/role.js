import tool from '@/utils/tool';

let role = (data) => {
	let roles = tool.data.get('user').roles;

	if(! roles){
		return false
	}

	if (roles[0] === 'superAdmin') {
		return true
	}
	
	return roles.includes(data) ? true : false
}
export default role;