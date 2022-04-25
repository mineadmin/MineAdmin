<template>
  <div
    ref=""
    class="mobile-nav-button"
    @click="showMobileNav($event)"
    v-drag
    draggable="false"
  >
    <el-icon><el-icon-menu /></el-icon>
  </div>

  <el-drawer
    ref="mobileNavBox"
    title="移动端菜单"
    :size="240"
    v-model="nav"
    direction="ltr"
    :with-header="false"
    destroy-on-close
  >
    <el-container class="mobile-nav">
      <el-header>
        <div class="logo-bar">
          <img class="logo" src="img/logo.svg" /><span>{{
            $CONFIG.APP_NAME
          }}</span>
        </div>
      </el-header>
      <el-main>
        <el-scrollbar>
          <el-menu
            :default-active="$route.meta.active || $route.fullPath"
            @select="select"
            router
            background-color="#212d3d"
            text-color="#fff"
            active-text-color="#409EFF"
          >
            <NavMenu :navMenus="menu"></NavMenu>
          </el-menu>
        </el-scrollbar>
      </el-main>
    </el-container>
  </el-drawer>
</template>

<script>
import NavMenu from "./NavMenu.vue";

export default {
  components: {
    NavMenu,
  },
  data() {
    return {
      nav: false,
      menu: [],
    };
  },
  computed: {},
  created() {
    var menu = this.$store.state.user.routers;
    this.menu = this.filterUrl(menu);
  },

  watch: {},
  methods: {
    showMobileNav(e) {
      var isdrag = e.currentTarget.getAttribute("drag-flag");
      if (isdrag == "true") {
        return false;
      } else {
        this.nav = true;
      }
    },
    select() {
      this.$refs.mobileNavBox.handleClose();
    },
    //转换外部链接的路由
    filterUrl(map) {
      map.forEach((item, index) => {
        item.meta = item.meta ? item.meta : {};
        //处理隐藏
        if (item.meta.hidden) {
          map.splice(index, 1);
        }
        //处理http
        if (item.meta.type == "I") {
          item.path = `/i/${item.name}`;
        }
        //递归循环
        if (item.children && item.children.length > 0) {
          item.children = this.filterUrl(item.children);
        }
      });
      return map;
    },
  },
  directives: {
    drag(el) {
      let oDiv = el; //当前元素
      let firstTime = "",
        lastTime = "";
      //禁止选择网页上的文字
      // document.onselectstart = function() {
      // 	return false;
      // };
      oDiv.onmousedown = function (e) {
        //鼠标按下，计算当前元素距离可视区的距离
        let disX = e.clientX - oDiv.offsetLeft;
        let disY = e.clientY - oDiv.offsetTop;
        document.onmousemove = function (e) {
          oDiv.setAttribute("drag-flag", true);
          firstTime = new Date().getTime();
          //通过事件委托，计算移动的距离
          let l = e.clientX - disX;
          let t = e.clientY - disY;

          //移动当前元素

          if (t > 0 && t < document.body.clientHeight - 50) {
            oDiv.style.top = t + "px";
          }
          if (l > 0 && l < document.body.clientWidth - 50) {
            oDiv.style.left = l + "px";
          }
        };
        document.onmouseup = function () {
          lastTime = new Date().getTime();
          if (lastTime - firstTime > 200) {
            oDiv.setAttribute("drag-flag", false);
          }
          document.onmousemove = null;
          document.onmouseup = null;
        };
        //return false不加的话可能导致黏连，就是拖到一个地方时div粘在鼠标上不下来，相当于onmouseup失效
        return false;
      };
    },
  },
};
</script>

<style scoped>
.mobile-nav-button {
  position: fixed;
  bottom: 10px;
  left: 10px;
  z-index: 10;
  width: 50px;
  height: 50px;
  background: #409eff;
  box-shadow: 0 2px 12px 0 rgba(64, 158, 255, 1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mobile-nav-button i {
  color: #fff;
  font-size: 20px;
}

.mobile-nav {
  background: #212d3d;
}
.mobile-nav .el-header {
  background: transparent;
  border: 0;
}
.mobile-nav .el-main {
  padding: 0;
}
.mobile-nav .logo-bar {
  display: flex;
  align-items: center;
  font-weight: bold;
  font-size: 20px;
  color: #fff;
}
.mobile-nav .logo-bar img {
  width: 30px;
  margin-right: 10px;
}
.mobile-nav .el-submenu__title:hover {
  background: #fff !important;
}
</style>
