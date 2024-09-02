<template>
  <div class="app-container">
    <el-card style="text-align: center; padding: 50px 0px;">
      <img src="/storage/images/work_in_progress.png" alt="Trabalho em progresso" style="height: 400px;">
      <h2>Trabalho em progresso</h2>
    </el-card>
  </div> 
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'DashboardPage'
});
</script>

<style scoped>
    h2 {
        font-weight: bold;
        color: #666;
    }
</style>