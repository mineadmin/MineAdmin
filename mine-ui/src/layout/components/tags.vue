<template>
  <div class="adminui-tags">
    <ul ref="tags">
      <li
        v-for="tag in tagList"
        v-bind:key="tag"
        :class="[isActive(tag) ? 'active' : '', tag.meta.affix ? 'affix' : '']"
        @contextmenu.prevent="openContextMenu($event, tag)"
      >
        <router-link :to="tag">
          <span>{{ tag.meta.title }}</span>
          <el-icon
            v-if="!tag.meta.affix"
            @click.prevent.stop="closeSelectedTag(tag)"
            ><el-icon-close
          /></el-icon>
        </router-link>
      </li>
    </ul>
  </div>

  <transition name="el-zoom-in-top">
    <ul
      v-if="contextMenuVisible"
      :style="{ left: left + 'px', top: top + 'px' }"
      class="contextmenu"
      id="contextmenu"
    >
      <li @click="refreshTab()">
        <el-icon><el-icon-refresh /></el-icon>刷新
      </li>
      <hr />
      <li
        @click="closeTabs()"
        :class="contextMenuItem.meta.affix ? 'disabled' : ''"
      >
        <el-icon><el-icon-close /></el-icon>关闭标签
      </li>
      <li @click="closeOtherTabs()">
        <el-icon><el-icon-folder-delete /></el-icon>关闭其他标签
      </li>
      <hr />
      <li @click="screen()">
        <el-icon><el-icon-full-screen /></el-icon>全屏当前标签
      </li>
      <li @click="openWindow()">
        <el-icon><el-icon-copy-document /></el-icon>在新的窗口中打开
      </li>
    </ul>
  </transition>
</template>

<script>
import Sortable from "sortablejs";

export default {
  name: "tags",
  data() {
    return {
      contextMenuVisible: false,
      contextMenuItem: null,
      left: 0,
      top: 0,
      tagList: this.$store.state.viewTags.viewTags,
    };
  },
  props: {},
  watch: {
    $route(e) {
      this.addViewTags(e);
      //判断标签容器是否出现滚动条
      this.$nextTick(() => {
        const tags = this.$refs.tags;
        if (tags && tags.scrollWidth > tags.clientWidth) {
          //确保当前标签在可视范围内
          let targetTag = tags.querySelector(".active");
          targetTag.scrollIntoView();
        }
      });
    },
    contextMenuVisible(value) {
      var _this = this;
      var cm = function (e) {
        let sp = document.getElementById("contextmenu");
        if (sp && !sp.contains(e.target)) {
          _this.closeMenu();
        }
      };
      if (value) {
        document.body.addEventListener("click", (e) => cm(e));
      } else {
        document.body.removeEventListener("click", (e) => cm(e));
      }
    },
  },
  created() {
    let dashboardRoute = this.$store.state.user.routers[0].children[0];
    dashboardRoute.fullPath = dashboardRoute.path;
    this.addViewTags(dashboardRoute);
    this.addViewTags(this.$route);
  },
  mounted() {
    this.tagDrop();
    this.scrollInit();
  },
  methods: {
    //查找树
    treeFind(tree, func) {
      for (const data of tree) {
        if (func(data)) return data;
        if (data.children) {
          const res = this.treeFind(data.children, func);
          if (res) return res;
        }
      }
      return null;
    },
    //标签拖拽排序
    tagDrop() {
      const target = this.$refs.tags;
      Sortable.create(target, {
        draggable: "li",
        animation: 300,
      });
    },
    //增加tag
    addViewTags(route) {
      if (route.name && !route.meta.fullpage) {
        this.$store.commit("pushViewTags", route);
        this.$store.commit("pushKeepLive", route.name);
      }
    },
    //高亮tag
    isActive(route) {
      return route.fullPath === this.$route.fullPath;
    },
    //关闭tag
    closeSelectedTag(tag, autoPushLatestView = true) {
      this.$store.commit("removeViewTags", tag);
      this.$store.commit("removeIframeList", tag);
      this.$store.commit("removeKeepLive", tag.name);
      if (autoPushLatestView && this.isActive(tag)) {
        const latestView = this.tagList.slice(-1)[0];
        if (latestView) {
          this.$router.push(latestView);
        } else {
          this.$router.push("/");
        }
      }
    },
    //tag右键
    openContextMenu(e, tag) {
      this.contextMenuItem = tag;
      this.contextMenuVisible = true;
      this.left = e.clientX + 1;
      this.top = e.clientY + 1;

      //FIX 右键菜单边缘化位置处理
      this.$nextTick(() => {
        let sp = document.getElementById("contextmenu");
        if (document.body.offsetWidth - e.clientX < sp.offsetWidth) {
          this.left = document.body.offsetWidth - sp.offsetWidth + 1;
          this.top = e.clientY + 1;
        }
      });
    },
    //关闭右键菜单
    closeMenu() {
      this.contextMenuItem = null;
      this.contextMenuVisible = false;
    },
    //TAB 刷新
    refreshTab() {
      var nowTag = this.contextMenuItem;
      this.contextMenuVisible = false;
      //判断是否当前路由，否的话跳转
      if (this.$route.fullPath != nowTag.fullPath) {
        this.$router.push({
          path: nowTag.fullPath,
          query: nowTag.query,
        });
      }
      this.$store.commit("refreshIframe", nowTag);
      var _this = this;
      setTimeout(function () {
        _this.$store.commit("removeKeepLive", nowTag.name);
        _this.$store.commit("setRouteShow", false);
        _this.$nextTick(() => {
          _this.$store.commit("pushKeepLive", nowTag.name);
          _this.$store.commit("setRouteShow", true);
        });
      }, 0);
    },
    //TAB 关闭
    closeTabs() {
      var nowTag = this.contextMenuItem;
      if (!nowTag.meta.affix) {
        this.closeSelectedTag(nowTag);
        this.contextMenuVisible = false;
      }
    },
    //TAB 关闭其他
    closeOtherTabs() {
      var nowTag = this.contextMenuItem;
      //判断是否当前路由，否的话跳转
      if (this.$route.fullPath != nowTag.fullPath) {
        this.$router.push({
          path: nowTag.fullPath,
          query: nowTag.query,
        });
      }
      var tags = [...this.tagList];
      tags.forEach((tag) => {
        if ((tag.meta && tag.meta.affix) || nowTag.fullPath == tag.fullPath) {
          return true;
        } else {
          this.closeSelectedTag(tag, false);
        }
      });
      this.contextMenuVisible = false;
    },
    //TAB 全屏标签
    screen() {
      var nowTag = this.contextMenuItem;
      this.contextMenuVisible = false;
      //判断是否当前路由，否的话跳转
      if (this.$route.fullPath != nowTag.fullPath) {
        this.$router.push({
          path: nowTag.fullPath,
          query: nowTag.query,
        });
      }
      var element = document.getElementById("adminui-main");
      this.$TOOL.screen(element);
    },
    //新窗口打开
    openWindow() {
      var nowTag = this.contextMenuItem;
      var url = nowTag.href || "/";
      if (!nowTag.meta.affix) {
        this.closeSelectedTag(nowTag);
      }
      window.open(url);
      this.contextMenuVisible = false;
    },
    //横向滚动
    scrollInit() {
      const scrollDiv = this.$refs.tags;
      scrollDiv.addEventListener("mousewheel", handler, false) ||
        scrollDiv.addEventListener("DOMMouseScroll", handler, false);
      function handler(event) {
        const detail = event.wheelDelta || event.detail;
        //火狐上滚键值-3 下滚键值3，其他内核上滚键值120 下滚键值-120
        const moveForwardStep = 1;
        const moveBackStep = -1;
        let step = 0;
        if (detail == 3 || (detail < 0 && detail != -3)) {
          step = moveForwardStep * 50;
        } else {
          step = moveBackStep * 50;
        }
        scrollDiv.scrollLeft += step;
      }
    },
  },
};
</script>

<style>
.contextmenu {
  position: fixed;
  width: 200px;
  margin: 0;
  border-radius: 0px;
  background: #fff;
  border: 1px solid #e4e7ed;
  box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
  z-index: 3000;
  list-style-type: none;
  padding: 10px 0;
}
.contextmenu hr {
  margin: 5px 0;
  border: none;
  height: 1px;
  font-size: 0px;
  background-color: #ebeef5;
}
.contextmenu li {
  display: flex;
  align-items: center;
  margin: 0;
  cursor: pointer;
  line-height: 30px;
  padding: 0 17px;
  color: #606266;
}
.contextmenu li i {
  font-size: 14px;
  margin-right: 10px;
}
.contextmenu li:hover {
  background-color: #ecf5ff;
  color: #66b1ff;
}
.contextmenu li.disabled {
  cursor: not-allowed;
  color: #bbb;
  background: transparent;
}
</style>
