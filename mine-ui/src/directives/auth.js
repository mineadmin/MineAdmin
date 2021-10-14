import permission from '@/utils/permission'

export default {
	mounted(el, binding) {
		const { value } = binding
		if(Array.isArray(value)){
			let ishas = false;
			value.forEach(item => {
				if(permission(item)){
					ishas = true;
				}
			})
			if (!ishas){
				el.parentNode.removeChild(el)
			}
		}else{
			if(!permission(value)){
				el.parentNode.removeChild(el);
			}
		}
	}
};
