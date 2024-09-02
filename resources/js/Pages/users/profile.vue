<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col :sm="24" :md="8">
        <user-card :user="current_user" />
      </el-col>
      <el-col :sm="24" :md="16">
        <el-card>
          <el-tabs
            v-model="activeName"
            class="user-tabs"
          >
            <el-tab-pane label="Informação" name="first">
              <user-form :user="current_user" />
            </el-tab-pane>
            
            <el-tab-pane label="Ausencias" name="second">
              <absence-form v-if="current_user.id" :user-id="current_user.id"/>
            </el-tab-pane> 
          </el-tabs>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Resource from '@/api/resource';

import UserForm from './components/userForm.vue';
import UserCard from './components/userCard.vue';
import AbsenceForm from './components/absenceForm.vue';

const userResource = new Resource('users');

export default {
  name: 'UserProfile',
  components: { UserForm, UserCard, AbsenceForm },
  data() {
    return {
      doc_id: 0,
      form: {
        first: 4,
        second: 3,
        third: 4,
        fourth: 5,
        five: 2,
      },
      activeName: 'first',
      current_user: {
        id: '',
        name: '',
        phone: '',
        email: '',
      },
      doc_list: [{ value: '1', label: 'Bilhete de Indentidade' },
        { value: '2', label: 'Cartão de Cidadão' },
        { value: '3', label: 'Passaporte' },
      ],
      user_roles: [],
      canEditSettings: false,
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    const currentUserId = this.$store.getters.userId;
    const roles = this.$store.getters.roles;

    if(!roles.includes('admin') && !roles.includes('gestor') && !roles.includes('terapeuta')){
      this.$router.push('/404');
    }

    if (roles.includes('terapeuta') && id !== currentUserId) {
      this.$router.push('/404');
      return;
    }

    this.user_roles = this.$store.getters.roles;
    var auth_user_id = this.$store.getters.userId;
    this.doc_id = id;
    
    this.canEditSettings = (this.user_roles.includes('admin')) || (this.user_roles.includes('gestor')) || (auth_user_id === this.doc_id);

    this.getUser();
  },
  methods: {
    async getUser() {

      const { data } = await userResource.get(this.doc_id);
      this.current_user = data;
      if (!data.id){
        this.$message({
          type: 'error',
          message: 'Não é possivel visualizar este perfil',
        });
      }
    },
  },
};
</script>

<style scoped>
  .filter-container {
        width: 100%;
  }
  .user-tabs > .el-tabs__content {
    padding: 32px;
    color: #6b778c;
    font-weight: 600;
  }
</style>
