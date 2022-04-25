<!--
 * @Descripttion: 处理iframe持久化，涉及store(VUEX)
 * @version: 1.0
 * @Author: sakuya
 * @Date: 2021年6月30日13:20:41
 * @LastEditors:
 * @LastEditTime:
-->

<template>
  <div v-show="$route.meta.type == 'I'" class="iframe-pages">
    <iframe
      v-for="item in iframeList"
      :key="item.meta.url"
      v-show="$route.meta.url == item.meta.url"
      :src="item.meta.url"
      frameborder="0"
    ></iframe>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  watch: {
    $route(e) {
      this.push(e);
    },
  },
  created() {
    this.push(this.$route);
  },
  computed: {
    iframeList() {
      return this.$store.state.iframe.iframeList;
    },
    ismobile() {
      return this.$store.state.global.ismobile;
    },
    layoutTags() {
      return this.$store.state.global.layoutTags;
    },
  },
  mounted() {},
  methods: {
    push(route) {
      if (route.meta.type == "I") {
        if (this.ismobile || !this.layoutTags) {
          this.$store.commit("setIframeList", route);
        } else {
          this.$store.commit("pushIframeList", route);
        }
      } else {
        if (this.ismobile || !this.layoutTags) {
          this.$store.commit("clearIframeList");
        }
      }
    },
  },
};
</script>

<style scoped>
.iframe-pages {
  width: 100%;
  height: 100%;
  background: #fff;
}
iframe {
  border: 0;
  width: 100%;
  height: 100%;
  display: block;
}
</style>
