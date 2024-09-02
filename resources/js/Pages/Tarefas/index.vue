<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <el-col :xs="12" :sm="12" :md="8">
          <el-button class="filter-item" type="primary" @click="handleCreate">
            <el-icon class="el-icon--left">
              <Plus />
            </el-icon>
            Nova tarefa
          </el-button>
        </el-col>

        <el-col
          v-if="$filters.isMobile()"
          :xs="12"
          :md="12"
          style="text-align: right"
        >
          <el-popover placement="bottom" :width="400" trigger="click">
            <template #reference>
              <el-button circle type="primary">
                <el-icon>
                  <Filter />
                </el-icon>
              </el-button>
            </template>

            <span class="text-muted">Palavra-chave</span>
            <el-input
              v-model="query.keyword"
              :placeholder="$t('Procurar por tarefa')"
              style="width: 100%; margin-bottom: 10px"
              class="filter-item"
              @change="handleFilter"
            />

            <el-button
              round
              class="filter-item"
              type="primary"
              style="width: 100%"
              @click="handleFilter"
            >
              Pesquisar
            </el-button>
          </el-popover>
        </el-col>

        <el-col v-if="!$filters.isMobile()" :md="16" style="text-align: right">
          <el-input
            v-model="query.keyword"
            :placeholder="$t('Procurar por tarefa')"
            style="width: 200px"
            class="filter-item"
            @change="handleFilter"
          />
          <el-button
            circle
            class="filter-item"
            type="primary"
            @click="handleFilter"
          >
            <el-icon>
              <Search />
            </el-icon>
          </el-button>
        </el-col>
      </el-row>
    </div>

    <el-table
      v-loading="loading"
      :data="list"
      border
      fit
      stripe
      highlight-current-row
      :scrollbar-always-on="true"
      style="width: 100%"
    >
      <el-table-column align="center" label="#" width="80">
        <template #default="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nome" min-width="150">
        <template #default="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Data_inicio" min-width="170">
        <template #default="scope">
          <span>{{ scope.row.data_inicio }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Data_Limite" min-width="170">
        <template #default="scope">
          <span>{{ scope.row.data_limite }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="Ações"
        fixed="right"
        min-width="120"
      >
        <template #default="scope">
          <router-link :to="'/tarefas/' + scope.row.id">
            <el-button round type="primary" size="small"> Editar </el-button>
          </router-link>
          <el-button
            round
            type="danger"
            size="small"
            @click="handleDelete(scope.row.id, scope.row.name)"
          >
            Eliminar
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-show="total > 0"
      v-model:currentPage="query.page"
      v-model:page-size="query.limit"
      :total="total"
      width="40%"
      @size-change="getList"
      @current-change="getList"
    />

    <el-dialog
      v-model="dialogFormVisible"
      :title="'Adicionar nova Tarefa'"
      :width="$filters.isMobile() ? '95%' : '40%'"
    >
      <div v-loading="TareaCreating" class="form-container">
        <el-form
          ref="tarefasForm"
          :rules="rules"
          :model="newTarefa"
          :label-position="$filters.isMobile() ? 'top' : 'left'"
          label-width="150px"
          style="max-width: 550px"
        >
          <el-form-item label="Nome" prop="name">
            <el-input v-model="newTarefa.name" />
          </el-form-item>

          <el-form-item label="Checklist" prop="name">
            <el-select filterable v-model="newTarefa.checklist_id">
              <el-option
                v-for="item in checklistList"
                :key="item.id"
                :value="item.id"
                :label="item.name"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="Utilizador" prop="name">
            <el-select filterable v-model="newTarefa.user_id">
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
              v-model="newTarefa.data_limite"
              type="date"
              placeholder="Escolha um dia "
              :size="large"
              :value-format="'YYYY/MM/DD'"
            ></el-date-picker>
          </el-form-item>
        </el-form>
      </div>
      <template #footer>
        <el-button round @click="dialogFormVisible = false">
          {{ $t("Cancelar") }}
        </el-button>
        <el-button round type="primary" @click="createTarefa()">
          {{ $t("Confirmar") }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import Resource from "@/api/resource";
import ChecklistResource from "@/api/checklists";
import UserResource from "@/api/users";

import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
import { format } from "path";

const checklistResource = new ChecklistResource();
const usersResource = new UserResource();
const tarefasResource = new Resource("tarefas");

export default {
  name: "TarefasList",
  components: {
    Plus,
    Search,
    Filter,
    ArrowRight,
  },

  data() {
    return {
      list: [],
      checklistList: [],
      usersList: [],
      loading: true,
      query: {
        page: 1,
        limit: 15,
        keyword: "",
        active: "1",
      },
      newTarefa: {},
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        name: "",
        permissions: [],
        rolePermissions: [],
      },
    };
  },
  created() {
    this.resetNewTarefa();
    this.getList();
    this.getChecklists();
    this.getUsers();
  },

  methods: {
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

    handleFilter() {
      this.query.page = 1;
      this.getList();
    },

    handleCreate() {
      this.getUsers();
      this.resetNewTarefa();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs["tarefasForm"].clearValidate();
      });
    },

    async getChecklists() {
      const { data } = await checklistResource.getChecklists(this.query);
      this.checklistList = data;
    },

    async getUsers() {
      const { data } = await usersResource.getUsers(this.query);
      this.usersList = data;
    },

    resetNewTarefa() {
      this.newTarefa = {
        name: "",
        user_id: "",
        checklist_id: "",
        data_inicio: "",
        data_limite: "",

        /* user_id: '', */
      };
    },

    createTarefa() {
      const data = new Date();
      const dataFormatada =
        data.getFullYear() + "-" + (data.getMonth() + 1) + "-" + data.getDate();

      this.newTarefa.data_inicio = dataFormatada;

      this.$refs["tarefasForm"].validate((valid) => {
        if (valid) {
          this.newTarefa.roles = [this.newTarefa.role];
          this.TarefaCreating = true;
          tarefasResource
            .store(this.newTarefa)
            .then((response) => {
              this.$message({
                message:
                  "Tarefa (" + this.newTarefa.name + ") criada com sucesso.",
                type: "success",
                duration: 5 * 1000,
              });
              this.resetNewTarefa();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.TarefaCreating = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },

    handleDelete(id, name) {
      this.$confirm(
        "This will permanently delete tarefa " + name + ". Continue?",
        "Warning",
        {
          confirmButtonText: "OK",
          cancelButtonText: "Cancel",
          type: "warning",
        }
      )
        .then(() => {
          tarefasResource
            .destroy(id)
            .then((response) => {
              this.$message({
                type: "success",
                message: "Delete completed",
              });
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "Operação canceleda",
          });
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}

mana .cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}

.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}

.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;

  .block {
    float: left;
    min-width: 250px;
  }

  .clear-left {
    clear: left;
  }
}

.selectMargin {
  margin-right: 10px;
}
</style>
