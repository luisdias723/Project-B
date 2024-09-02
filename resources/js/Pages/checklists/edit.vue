<template>
  <div class="app-container">
    <el-row :gutter="20">
      <el-col :sm="24">
        <el-card>
              <checklist-form :checklist="selected_checklist" />
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Resource from '@/api/resource';

import ChecklistForm from './components/checklistForm.vue';

const checklistResource = new Resource('checklists');

export default {
  name: 'ChecklistEdit',
  components: { ChecklistForm },
  data() {
    return {
      doc_id: 0,

      selected_checklist: {
        id: '',
        name: '',

      },

      canEditSettings: true,
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    const currentUserId = this.$store.getters.userId;
    const roles = this.$store.getters.roles;

    this.user_roles = this.$store.getters.roles;

    this.getChecklist(id);
  },
  methods: {
    async getChecklist(id) {

      const { data } = await checklistResource.get(id);
      this.selected_checklist = data;
      if (!data.id){
        this.$message({
          type: 'error',
          message: 'Não é possivel visualizar esta checklist',
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
  .checklist-tabs > .el-tabs__content {
    padding: 32px;
    color: #6b778c;
    font-weight: 600;
  }
</style>
