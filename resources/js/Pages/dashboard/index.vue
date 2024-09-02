<template>
  <div class="app-container">
    <h1>Dashboard</h1>
  </div> 
</template>

<script>


export default {
  name: 'DashboardPage',
  data() {
    return {
      user_roles: [],
    };
  },
  created() {
    this.user_roles = this.$store.getters.roles;
  }
};
</script>

<style scoped>
  .app-container{
    background: white;
    margin: 10px;
  }
</style>