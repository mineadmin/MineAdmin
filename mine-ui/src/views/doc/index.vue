<template>
  <main-page v-if="$TOOL.data.get('apiAuth')" />
  <auth-page v-else />
</template>

<script>
import AuthPage from './components/auth'
import MainPage from './components/docMain'
export default {
  components: {
    AuthPage,
    MainPage
  },

  created () {
    if (this.$TOOL.data.get('apiAuth')) {
      this.$router.push({name: 'interfaceList'})
    } else {
      this.$router.push({name: 'doc'})
    }
  }
}
</script>