<template>
    <div>
    </div>
</template>

<script>
export default {
  name: 'UserSelfProfile',
  data() {
    return {
      user_id: {},
    };
  },
  created() {
    this.getUser();
  },
  methods: {
    getUser() {
      this.user_id = this.$store.getters.userId;
      this.$router.push('/clientes/perfil/' + this.user_id);
    },
  },
};
</script>
