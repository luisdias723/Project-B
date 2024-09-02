<template>
  <aside aria-label="Sidebar">
    <hamburger v-if="smallScreen()" id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />
    <!-- Primary Navigation Menu -->
    <div class="drawer-bg">
      <!-- Logo -->
      <div class="logo-container">
        <el-link href="/">
          <el-image src="/storage/images/logo-hovo.png" />
        </el-link>
      </div>
      <!-- Navigation Links -->
      <el-scrollbar wrap-class="scrollbar-wrapper">
        <el-menu
          :show-timeout="200"
          :default-active="$route.path"
          :collapse="isCollapse"
          mode="vertical"
          background-color="#FFFFFF"
          text-color="#304156"
          active-text-color="#409EFF"
        >
          <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" :collapse="isCollapse" />
        </el-menu>
      </el-scrollbar>
    </div>
  </aside>
</template>


<script>
import { defineComponent } from 'vue';
import { mapGetters } from 'vuex';
import SidebarItem from './SidebarItem.vue';
import Hamburger from '../Hamburguer';


export default defineComponent({
  name: 'SidebarModule',
  components: {
    SidebarItem,
    Hamburger,
  }, 
  computed: {
    ...mapGetters([
      'sidebar',
      'permissionRoutes',
    ]),
    routes() {
      return this.$store.state.permission.routes;
    },
    isCollapse() {
      return !this.sidebar.opened;
    },
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    handleFocusOut() {
      this.$store.dispatch('app/toggleSideBar');
    },
    smallScreen(){
      if( screen.width <= 1000 ) {
        return true;
      }
      else {
        return false;
      }
    }
  }
});
</script>

<style lang="scss">

    .openSidebar .logo-container {
        padding: 20px;
        text-align: center
    }

    .hideSidebar {

        .logo-container {
            padding: 20px 0px;
            text-align: center;

            .el-image {
                width: 40px;
                height: auto;
            }
        }
    }

    aside{
        background: white;
        height: 400px;

        ul{
            list-style: none;
            padding: 0;
        }
    }

    .drawer-bg {
      position: absolute;
        top: 0;
        width: 100%;
        height: calc(100% - 210px);
        z-index: 999;
    }

    
</style>