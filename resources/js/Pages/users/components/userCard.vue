<template>
  <el-card class="user-container">
    <el-skeleton v-if="!user.id" style="--el-skeleton-circle-size: 100px">
      <template #template>
        <el-skeleton-item variant="circle" />
      </template>
    </el-skeleton>
    <el-skeleton v-if="!user.id" :rows="1" animated />

    <div v-if="user.id" class="user-avatar box-center">
      <img :src="user.avatar" height="150" width="150" loading="lazy" style="border-radius: 50%;">
    </div>
    <div v-if="user.id" class="box-center">
      <div class="user-name text-center">
        {{ user.name }}
      </div>
      <div class="user-role text-center text-muted">
        {{ $filters.uppercaseFirst(getRole()) }}
      </div>
      <div class="user-actions" style="width:100%;">
        <el-upload
          ref="upload"
          class="upload-demo-user"
          action=""
          :show-file-list="false"
          :auto-upload="false"
          :on-change="onChangeFiles"
        >
          <template #trigger>
            <el-button :loading="avatarLoading" round>
              <el-icon><PictureFilled /></el-icon>Alterar imagem de perfil
            </el-button>
          </template>
        </el-upload>
        <el-button v-if="$store.getters.roles.includes('admin') && !user.active" round type="primary" :loading="loading" @click="updateActive(1)">
          Ativar conta
        </el-button>
        <el-button v-if="$store.getters.roles.includes('admin') && user.active" round type="warning" :loading="loading" @click="updateActive(0)">
          Desativar conta
        </el-button>
      </div>
    </div>
    <el-divider />
  </el-card>
</template>

<script>
import UserResource from '@/api/users';
import { PictureFilled } from '@element-plus/icons-vue';

const userResource = new UserResource();

export default {
  name: 'UserCard',
  components: { PictureFilled },
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          id: null,
          name: '',
          email: '',
          phone: '',
          role: '',
          roles: [],
          active: 1,
        };
      },
    },
  },
  data() {
    return {
      loading: false,
      newFile: {},
      avatarLoading: false,
    };
  },
  methods: {
    getRole() {
      // const roles = this.user.roles.map(value => this.$options.filters.uppercaseFirst(value));
      return this.user.roles.join(' | ');
    },
    updateActive(active) {
      this.loading = true;
      userResource.updateActive({user_id: this.user.id, active: active}).then(response => {
        
        ElMessage({
          type: 'success',
          message: 'Utilizador ' + (active ? 'ativado' : 'desativado'),
        });

        this.loading = false;
        this.user.active = active;
      }).catch(error => {
        console.log(error);
      });
    },
    onChangeFiles(e) {
      this.avatarLoading = true;
      this.newFile = e.raw;
      const dataFile = new FormData();
      dataFile.append('title', this.newFile.name);
      dataFile.append('file', this.newFile);
      dataFile.append('user', this.user.id);

      userResource
        .changeAvatar(dataFile)
        .then((response) => {
          this.newFile = {};
          if (response !== null) {
            ElMessage({
              message: 'Imagem alterada com sucesso.',
              type: 'success',
              duration: 5 * 1000,
            });
            this.user.avatar = response;
            this.$store.dispatch('user/getInfo');
            
          } else {
            ElMessage({
              message: 'Não foi possível adicionar a imagem. Tente novamente',
              type: 'error',
              duration: 5 * 1000,
            });
          }
          this.avatarLoading = false;
        })
        .catch((error) => {
          ElMessage({
            message: 'Não foi possível adicionar a imagem. Tente novamente',
            type: 'error',
            duration: 5 * 1000,
          });
          this.avatarLoading = false;
          console.log(error);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.rank-icon {
  color: #ffcc00;
  font-size: 18px;
  position: relative;
  top: 3px;
}
.el-skeleton {
  width: 80%;
  text-align: center;
  margin: 10px auto;
  text-align: center;
}

.el-card {
  margin-bottom: 10px;
}
.user-container {
  .user-name {
    font-weight: bold;
  }
  .box-center {
    padding-top: 10px;
    width: 80%;
  }
  .user-avatar.box-center{
    padding-top: 10px;
    width: 1% !important;
  }
  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }
  .box-social {
    padding-top: 30px;
    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }
  .user-avatar {
    padding-top: 20px;
  }
  .user-actions {
    padding-top: 10px;
    display: inline-flex;

    > * {
      margin: 0px 5px;
      width: 50%;
    }
  }
}

</style>
<style>
  .upload-demo-user>div {
    max-width: 94% !important;
  }
</style>