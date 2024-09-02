<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <el-col :xs="12" :sm="12" :md="8">
          <el-button class="filter-item" type="primary" @click="handleCreate">
            <el-icon class="el-icon--left">
              <Plus />
            </el-icon> Nova checklist
          </el-button>
        </el-col>

        <!-- mobile filter -->
        <el-col v-if="$filters.isMobile()" :xs="12" :md="12" style="text-align: right;">
          <el-popover placement="bottom" :width="400" trigger="click">
            <template #reference>
              <el-button circle type="primary">
                <el-icon><Filter /></el-icon>
              </el-button>
            </template>

            <span class="text-muted">Palavra-chave</span>
            <el-input v-model="query.keyword" :placeholder="$t('Procurar por checklist')" style="width: 100%; margin-bottom: 10px;" class="filter-item" @change="handleFilter" />

            <el-button round class="filter-item" type="primary" style="width: 100%;" @click="handleFilter">
              Pesquisar
            </el-button>
          </el-popover>
        </el-col>

        <el-col v-if="!$filters.isMobile()" :md="16" style="text-align: right;">
          <el-input v-model="query.keyword" :placeholder="$t('Procurar por checklisat')" style="width: 200px;" class="filter-item" @change="handleFilter" />
          <el-button circle class="filter-item" type="primary" @click="handleFilter">
            <el-icon><Search/></el-icon>
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

      <el-table-column align="center" label="Nome" min-width="200">
        <template #default="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Criado em" min-width="200">
        <template #default="scope">
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Ações" fixed="right" min-width="120">
        <template #default="scope">
          <router-link :to="'/checklists/editar/'+scope.row.id">
            <el-button round type="primary" size="small">
              Editar
            </el-button>
          </router-link>
            <el-button round type="danger" size="small" @click="handleDelete(scope.row.id)">
              Eliminar
            </el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-show="total>0"
      v-model:currentPage="query.page"
      v-model:page-size="query.limit"
      :total="total"
      width="40%"
      @size-change="getList"
      @current-change="getList"
    />
    
    <el-dialog v-model="dialogFormVisible" :title="'Adicionar nova checklist'" :width="$filters.isMobile() ? '95%' : '40%'">
      <div v-loading="checklistCreating" class="form-container">
        <el-form
          ref="checklistForm"
          :rules="rules"
          :model="newChecklist"
          :label-position="$filters.isMobile() ? 'top' : 'left'"
          label-width="150px"
          style="max-width: 550px;"
        >

          <el-form-item :label="$t('Nome')" prop="name">
            <el-input v-model="newChecklist.name" />
          </el-form-item>

        </el-form>
      </div>
      <template #footer>
        <el-button round @click="dialogFormVisible = false">
          {{ $t('Cancelar') }}
        </el-button>
        <el-button round type="primary" @click="createChecklist()">
          {{ $t('Confirmar') }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import Resource from '@/api/resource';
import { Plus, Search, Filter, ArrowRight } from '@element-plus/icons-vue';

const checklistResource = new Resource('checklists');

export default {
  name: 'ChecklistsList',
  components: {
    Plus,
    Search,
    Filter,
    ArrowRight
  },
  data() {
    return {
      list: [],
      loading: true,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        active: '1',
      },
      newChecklist: {},
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],
      },
      rules: {
        name: [
          { required: true, message: 'Por favor insira o nome', trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    this.resetNewChecklist();
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await checklistResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.resetNewChecklist();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['checklistForm'].clearValidate();
      });
    },
    createChecklist() {
      this.$refs['checklistForm'].validate((valid) => {
        if (valid) {
          this.newChecklist.roles = [this.newChecklist.role];
          this.checklistCreating = true;
          checklistResource
            .store(this.newChecklist)
            .then(response => {
              this.$message({
                message: 'Checklist (' + this.newChecklist.name + ') criada com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewChecklist();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.checklistCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    updateChecklist() {
      this.$refs.checklistForm.validate((valid) => {
        if (valid) {
          this.loading = true;
          checklistResource
            .update(this.checklist.id, this.checklist)
            .then(() => {
              this.$message({
                message: 'Informação de checklist actualizada com sucesso',
                type: 'success',
                duration: 5 * 1000,
              });
              this.$store.dispatch('checklist/getInfo');
              this.loading = false;
            })
            .catch(error => {
              console.log(error);
              this.loading = false;
            });
        } else {
          console.log('error');
          return false;
        }
      });
    },
    resetNewChecklist() {
      this.newChecklist = {
        name: '',
      };
    },
    handleDelete(id) {
      this.$confirm('Tem a certeza que pretende eliminar esta checklist?', 'Atenção', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        checklistResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Checklist eliminada com sucesso!',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Operação canceleda',
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
.cancel-btn {
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

.selectMargin{
  margin-right: 10px;
}

</style>
