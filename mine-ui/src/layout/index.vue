<template>
  <!-- 通栏布局 -->
  <template v-if="layout == 'header'">
    <header class="adminui-header">
      <div class="adminui-header-left">
        <div class="logo-bar">
          <img class="logo" src="img/logo.svg" />
          <span>{{ $CONFIG.APP_NAME }}</span>
        </div>
        <ul v-if="!ismobile" class="nav">
          <li
            v-for="item in menu"
            :key="item"
            :class="pmenu.path == item.path ? 'active' : ''"
            @click="showMenu(item)"
          >
            <el-icon
              ><component :is="item.meta.icon || el - icon - menu"
            /></el-icon>
            <span>{{ item.meta.title }}</span>
          </li>
        </ul>
      </div>
      <div class="adminui-header-right">
        <userbar></userbar>
      </div>
    </header>
    <section class="aminui-wrapper">
      <div
        v-if="(!ismobile && nextMenu.length > 0) || !pmenu.component"
        :class="menuIsCollapse ? 'aminui-side isCollapse' : 'aminui-side'"
      >
        <div v-if="!menuIsCollapse" class="adminui-side-top">
          <h2>{{ pmenu.meta.title }}</h2>
        </div>
        <div class="adminui-side-scroll">
          <el-scrollbar>
            <el-menu
              :default-active="active"
              router
              :collapse="menuIsCollapse"
              :unique-opened="$CONFIG.MENU_UNIQUE_OPENED"
            >
              <NavMenu :navMenus="nextMenu"></NavMenu>
            </el-menu>
          </el-scrollbar>
        </div>
      </div>
      <Side-m v-if="ismobile"></Side-m>
      <div class="aminui-body el-container">
        <Topbar v-if="!ismobile"></Topbar>
        <Tags v-if="!ismobile && layoutTags"></Tags>
        <div class="adminui-main" id="adminui-main">
          <router-view v-slot="{ Component }">
            <keep-alive :include="this.$store.state.keepAlive.keepLiveRoute">
              <component
                :is="Component"
                :key="$route.fullPath"
                v-if="$store.state.keepAlive.routeShow"
              />
            </keep-alive>
          </router-view>
          <iframe-view></iframe-view>
        </div>
      </div>
    </section>
  </template>

  <!-- 经典布局 -->
  <template v-else-if="layout == 'menu'">
    <header class="adminui-header">
      <div class="adminui-header-left">
        <div class="logo-bar">
          <img class="logo" src="img/logo.svg" />
          <span>{{ $CONFIG.APP_NAME }}</span>
        </div>
      </div>
      <div class="adminui-header-right">
        <userbar></userbar>
      </div>
    </header>
    <section class="aminui-wrapper">
      <div
        v-if="!ismobile"
        :class="menuIsCollapse ? 'aminui-side isCollapse' : 'aminui-side'"
      >
        <div class="adminui-side-scroll">
          <el-scrollbar>
            <el-menu
              :default-active="active"
              router
              :collapse="menuIsCollapse"
              :unique-opened="$CONFIG.MENU_UNIQUE_OPENED"
            >
              <NavMenu :navMenus="menu"></NavMenu>
            </el-menu>
          </el-scrollbar>
        </div>
      </div>
      <Side-m v-if="ismobile"></Side-m>
      <div class="aminui-body el-container">
        <Topbar v-if="!ismobile"></Topbar>
        <Tags v-if="!ismobile && layoutTags"></Tags>
        <div class="adminui-main" id="adminui-main">
          <router-view v-slot="{ Component }">
            <keep-alive :include="this.$store.state.keepAlive.keepLiveRoute">
              <component
                :is="Component"
                :key="$route.fullPath"
                v-if="$store.state.keepAlive.routeShow"
              />
            </keep-alive>
          </router-view>
          <iframe-view></iframe-view>
        </div>
      </div>
    </section>
  </template>

  <!-- 功能坞布局 -->
  <template v-else-if="layout == 'dock'">
    <header class="adminui-header">
      <div class="adminui-header-left">
        <div class="logo-bar">
          <img class="logo" src="img/logo.svg" />
          <span>{{ $CONFIG.APP_NAME }}</span>
        </div>
      </div>
      <div class="adminui-header-right">
        <div v-if="!ismobile" class="adminui-header-menu">
          <el-menu mode="horizontal" :default-active="active" router background-color="#222b45" text-color="#fff" active-text-color="var(--el-color-primary)">
            <NavMenu :navMenus="menu"></NavMenu>
          </el-menu>
        </div>
        <Side-m v-if="ismobile"></Side-m>
        <userbar></userbar>
      </div>
    </header>
    <section class="aminui-wrapper">
      <div class="aminui-body el-container">
        <div class="adminui-main" id="adminui-main">
          <router-view v-slot="{ Component }">
            <keep-alive :include="this.$store.state.keepAlive.keepLiveRoute">
              <component
                :is="Component"
                :key="$route.fullPath"
                v-if="$store.state.keepAlive.routeShow"
              />
            </keep-alive>
          </router-view>
          <iframe-view></iframe-view>
        </div>
      </div>
    </section>
  </template>

  <!-- 默认布局 -->
  <template v-else>
    <section class="aminui-wrapper">
      <div v-if="!ismobile" class="aminui-side-split">
        <div class="adminui-side-split-scroll">
          <el-scrollbar>
            <ul>
              <li
                v-for="item in menu"
                :key="item"
                :class="pmenu.path == item.path ? 'active' : ''"
                @click="showMenu(item)"
              >
                <el-icon
                  ><component :is="item.meta.icon || el - icon - menu"
                /></el-icon>
                <p>{{ item.meta.title }}</p>
              </li>
            </ul>
          </el-scrollbar>
        </div>
      </div>
      <div
        v-if="(!ismobile && nextMenu.length > 0) || !pmenu.component"
        :class="menuIsCollapse ? 'aminui-side isCollapse' : 'aminui-side'"
      >
        <div v-if="!menuIsCollapse" class="adminui-side-top">
          <h2>{{ pmenu.meta.title }}</h2>
        </div>
        <div class="adminui-side-scroll">
          <el-scrollbar>
            <el-menu
              :default-active="active"
              router
              :collapse="menuIsCollapse"
              :unique-opened="$CONFIG.MENU_UNIQUE_OPENED"
            >
              <NavMenu :navMenus="nextMenu"></NavMenu>
            </el-menu>
          </el-scrollbar>
        </div>
      </div>
      <Side-m v-if="ismobile"></Side-m>
      <div class="aminui-body el-container">
        <Topbar>
          <userbar></userbar>
        </Topbar>
        <Tags v-if="!ismobile && layoutTags"></Tags>
        <div class="adminui-main" id="adminui-main">
          <router-view v-slot="{ Component }">
            <keep-alive :include="this.$store.state.keepAlive.keepLiveRoute">
              <component
                :is="Component"
                :key="$route.fullPath"
                v-if="$store.state.keepAlive.routeShow"
              />
            </keep-alive>
          </router-view>
          <iframe-view></iframe-view>
        </div>
      </div>
    </section>
  </template>

  <div
    class="layout-setting"
    @click="openSetting"
    v-if="$CONFIG.APP_MODE === 'dev'"
  >
    <el-icon><el-icon-brush-filled /></el-icon>
  </div>

  <el-drawer
    title="布局实时演示"
    v-model="settingDialog"
    :size="400"
    append-to-body
    destroy-on-close
  >
    <setting></setting>
  </el-drawer>
</template>

<script>
import SideM from "./components/sideM.vue";
import Topbar from "./components/topbar.vue";
import Tags from "./components/tags.vue";
import NavMenu from "./components/NavMenu.vue";
import userbar from "./components/userbar.vue";
import setting from "./components/setting.vue";
import iframeView from "./components/iframeView.vue";

export default {
  name: "index",
  components: {
    SideM,
    Topbar,
    Tags,
    NavMenu,
    userbar,
    setting,
    iframeView,
  },
  data() {
    return {
      settingDialog: false,
      menu: [],
      nextMenu: [],
      pmenu: {},
      active: "",
    };
  },
  computed: {
    ismobile() {
      return this.$store.state.global.ismobile;
    },
    layout() {
      return this.$store.state.global.layout;
    },
    layoutTags() {
      return this.$store.state.global.layoutTags;
    },
    menuIsCollapse() {
      return this.$store.state.global.menuIsCollapse;
    },
  },
  created() {
    this.onLayoutResize();
    window.addEventListener("resize", this.onLayoutResize);
    this.menu = this.filterUrl(this.$store.state.user.routers);
    this.showThis();
  },
  watch: {
    $route() {
      this.showThis();
    },
    layout: {
      handler(val) {
        document.body.setAttribute("data-layout", val);
      },
      immediate: true,
    },
  },
  methods: {
    openSetting() {
      this.settingDialog = true;
    },
    onLayoutResize() {
      const clientWidth = document.body.clientWidth;
      if (clientWidth < 992) {
        this.$store.commit("SET_ismobile", true);
      } else {
        this.$store.commit("SET_ismobile", false);
      }
    },
    //路由监听高亮
    showThis() {
      this.pmenu = this.$route.meta.breadcrumb
        ? this.$route.meta.breadcrumb[0]
        : {};
      this.nextMenu = this.filterUrl(this.pmenu.children);
      this.$nextTick(() => {
        this.active = this.$route.meta.active || this.$route.fullPath;
      });
    },
    //点击显示
    showMenu(route) {
      if (/[http:|https:]\/\//.test(route.path)) {
        window.open(route.path.substr(1));
        return;
      }
      this.pmenu = route;
      this.nextMenu = this.filterUrl(route.children);
      if( ( !route.children || route.children.length === 0 ) && route.component ){
        this.$router.push({ path: route.path });
      }
    },
    //转换外部链接的路由
    filterUrl(map) {
      var newMap = [];
      map &&
        map.forEach((item) => {
          // 生产模式去掉开发者工具
          if (this.$CONFIG.APP_MODE === "prod") {
            if (
              item.meta.type === "M" &&
              ["/module", "/code", "/table"].includes(item.path)
            ) {
              return false;
            }
          }
          // 去除没有系统配置权限
          if (
            !this.$TOOL.data.get("user").codes.includes("setting:config") &&
            this.$TOOL.data.get("user").codes[0] != "*" &&
            ["/system"].includes(item.path)
          ) {
            return false;
          }
          item.meta = item.meta ? item.meta : {};
          //处理隐藏
          if (item.meta.hidden) {
            return false;
          }
          //处理http
          if (item.meta.type == "I") {
            item.path = `/i/${item.name}`;
          }
          //递归循环
          if (item.children && item.children.length > 0) {
            item.children = this.filterUrl(item.children);
          }
          newMap.push(item);
        });
      return newMap;
    },
  },
};
</script>
