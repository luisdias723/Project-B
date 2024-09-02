<template>
  <div class="form-container">
    <el-form
      ref="tarefasForm"
      :model="tarefa"
      label-width="150px"
      style="max-width: 550px"
      class="form"
    >
      <el-form-item label="Nome" prop="name">
        <el-input v-model="tarefa.name" />
      </el-form-item>

      <el-form-item label="Checklist" prop="name">
        <el-select filterable v-model="tarefa.checklist_id">
          <el-option
            v-for="item in checklistList"
            :key="item.id"
            :value="item.id"
            :label="item.name"
          />
        </el-select>
      </el-form-item>

      <el-form-item label="Utilizador" prop="name">
        <el-select filterable v-model="tarefa.user_id">
          <el-option
            v-for="item2 in usersList"
            :key="item2.id"
            :value="item2.id"
            :label="item2.name"
          />
        </el-select>
      </el-form-item>

      <el-form-item label="Data Limite" prop="name">
        <el-date-picker
          v-model="tarefa.data_limite"
          type="date"
          placeholder="Escolha um dia "
          :size="large"
          :value-format="'YYYY/MM/DD'"
        ></el-date-picker>
      </el-form-item>
    </el-form>
  </div>
  <div class="botoes">
    <router-link :to="'/tarefas/'">
      <el-button round id="botao_cancelar">
        {{ $t("Cancelar") }}
      </el-button>
    </router-link>

    <el-button
      round
      type="primary"
      @click="UpdateTarefa()"
      id="botao_atualizar"
    >
      {{ $t("Confirmar") }}
    </el-button>
  </div>
</template>

<script>
import Resource from "@/api/resource";
import { Check } from "@element-plus/icons-vue";
import ChecklistResource from "@/api/checklists";
import UserResource from "@/api/users";

import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
import { format } from "path";

const checklistResource = new ChecklistResource();
const usersResource = new UserResource();
const tarefasResource = new Resource("tarefas");

export default {
  name: "tarefasFormulario",
  components: {
    Check,
  },

  data() {
    return {
      list: [],
      currentId: "",
      checklistList: [],
      usersList: [],
      tarefa: {
        name: "",
        checklist_id: "",
        user_id: "",
        data_limite: "",
      },
    };
  },
  created() {
    this.currentId = this.$route.params && this.$route.params.id;
    this.resetNewTarefa();
    this.getList();
    this.getChecklists();
    this.getUsers();
    this.getTarefa();
  },

  methods: {
    UpdateTarefa() {
      this.$refs.tarefasForm.validate((valid) => {
        if (valid) {
          tarefasResource
            .update(this.currentId, this.tarefa)
            .then(() => {
              this.$message({
                message: "Informação de tarefa actualizada com sucesso",
                type: "success",
                duration: 5 * 1000,
              });
              this.$router.push("/tarefas");
            })
            .catch((error) => {
              console.log(error);
              this.loading = false;
            });
        } else {
          console.log("error");
          return false;
        }
      });
    },

    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await tarefasResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element["index"] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },

    async getChecklists() {
      const { data } = await checklistResource.getChecklists(this.query);
      this.checklistList = data;
    },

    async getUsers() {
      const { data } = await usersResource.getUsers(this.query);
      this.usersList = data;
    },

    async getTarefa() {
      const { data } = await tarefasResource.get(this.currentId);
      this.tarefa = data;
      if (!data.id) {
        this.$message({
          type: "error",
          message: "Não é possivel visualizar este perfil",
        });
      }
    },

    resetNewTarefa() {
      this.tarefa = {
        name: "",
        user_id: "",
        checklist_id: "",
        data_inicio: "",
        data_limite: "",
      };
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

.form {
  display: flex;
  flex-direction: column;
  align-items: left;
  justify-content: center;
  margin-top: 20px;
  background-color: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: inset 0px 0px 1px 0px #000000;
}

.form-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 40px;
}

.botoes {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  margin: 40px;
}
</style>
