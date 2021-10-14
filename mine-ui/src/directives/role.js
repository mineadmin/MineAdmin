import role from '@/utils/role'

export default {
	mounted(el, binding) {
		const { value } = binding
		if(Array.isArray(value)){
			let ishas = false
			value.forEach(item => {
				if(role(item)){
					ishas = true
				}
			})
			if (!ishas){
				el.parentNode.removeChild(el)
			}
		}else{
			if(!role(value)){
				el.parentNode.removeChild(el);
			}
		}
	}
};
