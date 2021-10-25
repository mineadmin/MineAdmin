<template>
	<div style="text-align: center;width: 100%;">
		<el-empty description=" ">

			<h2 data-text="404">404</h2>
			<h4>啊呀~页面不存在!</h4>
			<p>您可以先检查网址，然后重新输入或给我们反馈问题。</p>
			<el-button type="primary" plain round @click="gohome">返回首页</el-button>
			<el-button type="primary" plain round @click="gologin">重新登录</el-button>
			<el-button type="primary" round @click="goback">返回上一页</el-button>
		</el-empty>

	</div>
</template>

<script>
	export default {
		methods: {
			gohome(){
				location.href="#/"
			},
			goback(){
				this.$router.go(-1);
			},
			gologin(){
				this.$router.push("/login");
			}
		}
	}
</script>

<style scoped>
	h4 {font-size: 20px;color: #333;}
	p {font-size: 12px;color: #999;margin:20px 0;}
	h2 {font-size: 70px;position: relative;letter-spacing: 1px;z-index: 3;}
	h2:after {
		content: attr(data-text);
		position: absolute;
		left: 2px;
		text-shadow: -1px 0 red;
		top: 0;
		color: #000;
		background: #f6f8f9;
		overflow: hidden;
		clip: rect(0, 900px, 0, 0);
		width: 100%;
		animation: noise-anim 2s infinite linear alternate-reverse;
	}
	h2:before {
	    content: attr(data-text);
	    position: absolute;
	    left: -2px;
	    text-shadow: 1px 0 blue;
	    top: 0;
	    color: #000;
	    background: #f6f8f9;
	    overflow: hidden;
	    clip: rect(0, 900px, 0, 0);
		width: 100%;
	    animation: noise-anim-2 3s infinite linear alternate-reverse;
	}
	@keyframes noise-anim {
	    0% {
	        clip: rect(77px, 9999px, 51px, 0);
	    }
	    5% {
	        clip: rect(25px, 9999px, 30px, 0);
	    }
	    10% {
	        clip: rect(70px, 9999px, 75px, 0);
	    }
	    15% {
	        clip: rect(94px, 9999px, 96px, 0);
	    }
	    20% {
	        clip: rect(24px, 9999px, 11px, 0);
	    }
	    25% {
	        clip: rect(12px, 9999px, 53px, 0);
	    }
	    30% {
	        clip: rect(79px, 9999px, 54px, 0);
	    }
	    35% {
	        clip: rect(70px, 9999px, 93px, 0);
	    }
	    40% {
	        clip: rect(73px, 9999px, 86px, 0);
	    }
	    45% {
	        clip: rect(100px, 9999px, 96px, 0);
	    }
	    50% {
	        clip: rect(5px, 9999px, 68px, 0);
	    }
	    55% {
	        clip: rect(6px, 9999px, 16px, 0);
	    }
	    60% {
	        clip: rect(4px, 9999px, 60px, 0);
	    }
	    65% {
	        clip: rect(86px, 9999px, 74px, 0);
	    }
	    70% {
	        clip: rect(6px, 9999px, 53px, 0);
	    }
	    75% {
	        clip: rect(41px, 9999px, 35px, 0);
	    }
	    80% {
	        clip: rect(70px, 9999px, 60px, 0);
	    }
	    85% {
	        clip: rect(52px, 9999px, 66px, 0);
	    }
	    90% {
	        clip: rect(18px, 9999px, 16px, 0);
	    }
	    95% {
	        clip: rect(71px, 9999px, 80px, 0);
	    }
	    100% {
	        clip: rect(69px, 9999px, 51px, 0);
	    }
	}
	@keyframes noise-anim-2 {
	    0% {
	        clip: rect(20px, 9999px, 7px, 0);
	    }
	    5% {
	        clip: rect(51px, 9999px, 73px, 0);
	    }
	    10% {
	        clip: rect(33px, 9999px, 21px, 0);
	    }
	    15% {
	        clip: rect(60px, 9999px, 48px, 0);
	    }
	    20% {
	        clip: rect(86px, 9999px, 48px, 0);
	    }
	    25% {
	        clip: rect(32px, 9999px, 50px, 0);
	    }
	    30% {
	        clip: rect(59px, 9999px, 50px, 0);
	    }
	    35% {
	        clip: rect(80px, 9999px, 96px, 0);
	    }
	    40% {
	        clip: rect(7px, 9999px, 39px, 0);
	    }
	    45% {
	        clip: rect(62px, 9999px, 90px, 0);
	    }
	    50% {
	        clip: rect(85px, 9999px, 84px, 0);
	    }
	    55% {
	        clip: rect(89px, 9999px, 51px, 0);
	    }
	    60% {
	        clip: rect(88px, 9999px, 50px, 0);
	    }
	    65% {
	        clip: rect(8px, 9999px, 82px, 0);
	    }
	    70% {
	        clip: rect(80px, 9999px, 23px, 0);
	    }
	    75% {
	        clip: rect(22px, 9999px, 52px, 0);
	    }
	    80% {
	        clip: rect(96px, 9999px, 44px, 0);
	    }
	    85% {
	        clip: rect(84px, 9999px, 27px, 0);
	    }
	    90% {
	        clip: rect(32px, 9999px, 71px, 0);
	    }
	    95% {
	        clip: rect(85px, 9999px, 35px, 0);
	    }
	    100% {
	        clip: rect(24px, 9999px, 23px, 0);
	    }
	}
</style>
