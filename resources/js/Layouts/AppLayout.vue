<template>
  <div :class="classObj" class="app-wrapper">
    <!-- <div v-if="device==='mobile' && sidebar.opened" class="drawer-bg" /> -->
    <sidebar class="sidebar-container" />
    <div class="main-container">
      <div>
        <header class="layout-header">
          <el-row :gutter="12" style="width: 100%;">
            <el-col :span="18" :md="12">
              <hamburger id="hamburger-container" :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />
              <span class="text-muted route-name">{{ $route.name }}</span>
            </el-col>
            <el-col :span="5" :md="12" class="profile">
              <el-tag v-if="$store.getters.testModeEnabled == 'true'" class="test-mode-warning" type="info">
                Modo de teste ativo
              </el-tag>

              <div class="user-avatar" style="display: inline-block; margin-left: 10px">
                <el-avatar :src="$store.getters.avatar" />
                <!-- <img :src="$store.getters.avatar" height="40" width="40" style="border-radius: 50%;"> -->
              </div>
                    
              <el-dropdown class="alignNotifications header-profile-dropdown" placement="bottom-end" trigger="click" @command="handleCommand">
                <div>
                  <span v-if="!$filters.isMobile()">Bem-vindo(a), {{ $store.getters.name }}</span>
                  <el-icon class="el-icon--right">
                    <ArrowDown />
                  </el-icon>
                </div>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item disabled>
                      {{ $store.getters.name }}
                    </el-dropdown-item>
                    <router-link
                      v-if="$store.getters.roles.includes('admin') || $store.getters.roles.includes('gestor') " 
                      :to="'/administrador/utilizadores/perfil/' + $store.getters.userId"
                    >
                      <el-dropdown-item>Perfil</el-dropdown-item>
                    </router-link>
                    <router-link v-if="$store.getters.roles.includes('admin') || $store.getters.roles.includes('gestor') " :to="'/configuracoes'">
                      <el-dropdown-item>Configurações</el-dropdown-item>
                    </router-link>
                    <el-dropdown-item command="logout" divided>
                      Sair
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </el-col>
          </el-row>
        </header>
      </div>
      <router-view />
    </div>
  </div>
</template>


<script>
import { mapGetters } from 'vuex';
import Sidebar from './components/Sidebar/sidebar.vue';
import Hamburger from './components/Hamburguer';

import store from '../store';

import { ArrowDown, ArrowRight } from '@element-plus/icons-vue';


export default {
  components: {
    Sidebar,
    Hamburger,
    ArrowDown,
    ArrowRight
  },
  data() {
    return {
      drawer: false,
      showingNavigationDropdown: false,
      total: 0,
      allUsers: [],
      user_id: [],
      enable_video: true,
      active: false,
      video_url: ''
    };
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'device',
      'userId',
    ]),
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
      };
    },
  },
  mounted() {
    // this.getSettings();
    // Get if of auth user
    store.dispatch('user/getInfo').then(result => {
      this.user_id = result.id;
    }).catch(error => {
      console.log(error);
    });
  },
  methods: {
    checkCookie(cookie){
      // if cookie doesnt exist => show video
      let value = this.getCookie(cookie);
      if(value != ''){
        return false;
      }
      return true;
    },
    getCookie(cname) {
      let name = cname + '=';
      let ca = document.cookie.split(';');
      for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return '';
    },
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    handleCommand(command) {
      switch (command) {
      case 'profile':
        // this.logout();
        // console.log(profile);
        break;

      case 'logout':
        this.logout();
        break;
        
      default:
        break;
      }
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push('/login');
    },
  },
};
</script>

<style lang="scss">
  @import "~@/styles/mixin.scss";
  @import "~@/styles/variables.scss";

  .app-wrapper {
    @include clearfix;
    position: relative;
    height: 100%;
    width: 100%;

    &.mobile.openSidebar {
      position: fixed;
      top: 0;
    }
  }

  .layout-header {
    // background: white;
    // background: rgb(3,71,105);
    // background: linear-gradient(270deg, rgba(3,71,105,1) 0%, rgba(3,25,38,1) 100%);
    background: #436872;
    border-bottom: 1px solid #d8dce5;
    -webkit-box-shadow: 0 1px 3px 0 rgb(0 0 0 / 12%), 0 0 3px 0 rgb(0 0 0 / 4%);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 12%), 0 0 3px 0 rgb(0 0 0 / 4%);
    color: white;
  }
  
  .hideSidebar .fixed-header {
    width: calc(100% - 54px)
  }

  .mobile .fixed-header {
    width: 100%;
  }

  .main-container {
    background: #EEF1F4;
  }

  .profile {
    position: relative;
    top: 15px;
    text-align: right;
  }

  .chatButton{
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 30px !important;
    font-size: 30px;
    z-index: 1;
  }

  .chatButton>button{
    padding: 30px !important;
    font-size: 30px !important;
  }

  .chatButton>.el-badge__content.el-badge__content--danger.is-fixed{
    top: 5px !important;
    right: 15px !important;
  }

  #hamburger-container {
    display: inline-block;
    svg {
      fill: white;
    }
  }

  .alignNotifications{
    margin-top: 5px !important;
    margin-left: 10px !important;
    cursor: pointer !important;
  }

  .box-card{
    width: 100% !important;
  }

  .item {
    margin-right: 10px;
  }

  .addNotificationMargin {
    width: 100% !important;
    text-align: left !important;
  }

  .addNotificationMargin span.el-link__inner{
    width: 100% !important;
    display: block !important;
  }

  .noNotifications {
    margin: 25px 10px !important;
    min-height: 30px !important;
    text-align: left !important;
  }

  .addNotificationLoading {
    margin-top: 25px !important;
    min-height: 80px !important;
    text-align: center !important;
  }

  .notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .filterNotifications {
    display: flex !important;
    text-align: left !important;
    margin-bottom: 10px !important;
  }

  .box-card>div.el-card__body {
    padding: 0px !important;
  }

  .notification-item.missing-setting {
    background: #8575503b !important;
    color: #7f6227;
    display: inline-flex;

    .el-icon {
      font-size: 20px;
      height: 100%;
      margin-right: 10px;
    }
  }

  .notification-badge{
    cursor: pointer;
    position: relative;
    bottom: 8px;
    right: 5px;
  }

  .notifications-bell {
    font-size: 22px !important;
    color: white !important;
  }

  .notifications-bell.is-active {
    color: #e1b71d !important;
  }
  
  .filterNotifications .el-button.is-text {
    background: #ebebeb;
  }

  .notification-item {
    display: inline-grid;
    padding: 10px !important;
    width: 100%;
    word-break: break-word;
  }

  .notification-item small {
    color: #3b3b3b;
    margin-top: 5px;
  }

  .notification-item > a {
    width: 90%;
  }

  .notification-item.unread{
    background: #2275de36;
  }

  .el-badge>sup.is-dot{
    top: 50% !important;
  }

  .el-badge>.el-badge__content.is-fixed.is-dot {
    right: 5% !important;
  }

//   .notification-item.unread::after {
//     content: '';
//     height: 6px;
//     width: 6px;
//     background: #f56c6c;
//     position: absolute;
//     border-radius: 50%;
//     right: 5%;
//     top: 25px;
// }

.notification-item:hover {
  background: #d3d3d336;
}

.user-avatar .el-avatar {
  --el-avatar-size: 30px !important;
  background: white;
}

.test-mode-warning{
  position: relative;
  bottom: 8px;
  right: 10px;
}

.header-profile-dropdown{
  color: white !important;
}

.user-avatar .el-avatar > img {
    object-fit: cover;
    height: 100% !important;
    width: 100% !important;
  }
  
</style>
