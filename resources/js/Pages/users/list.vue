<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <el-col :xs="12" :sm="12" :md="8">
          <el-button class="filter-item" type="primary" @click="handleCreate">
            <el-icon class="el-icon--left">
              <Plus />
            </el-icon> Novo utilizador
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

            <span class="text-muted">Estado</span>
            <el-select v-model="query.active" placeholder="Estado" class="filter-item selectMargin" style="width: 100%; margin-bottom: 10px;" @change="handleFilter">
              <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>

            <span class="text-muted">Papel</span>
            <el-select v-model="query.role" placeholder="Papéis" style="width: 100%; margin-bottom: 10px;" class="filter-item" @change="handleFilter">
              <el-option v-for="item in roles" :key="item" :label="item" :value="item" />
            </el-select>

            <span class="text-muted">Palavra-chave</span>
            <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 100%; margin-bottom: 10px;" class="filter-item" @change="handleFilter" />

            <el-button round class="filter-item" type="primary" style="width: 100%;" @click="handleFilter">
              Pesquisar
            </el-button>
          </el-popover>
        </el-col>

        <el-col v-if="!$filters.isMobile()" :md="16" style="text-align: right;">
          <el-select v-model="query.active" placeholder="Estado" class="filter-item selectMargin" @change="handleFilter()">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
          <el-select v-model="query.role" placeholder="Papéis" style="width: 170px" class="filter-item" @change="handleFilter">
            <el-option v-for="item in roles" :key="item" :label="item" :value="item" />
          </el-select>
          <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @change="handleFilter" />
          <el-button circle class="filter-item" type="primary" @click="handleFilter">
            <el-icon><Search /></el-icon>
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
          <span>{{ scope.row.index }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nome" min-width="150">
        <template #default="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Email" min-width="170">
        <template #default="scope">
          <el-link type="primary" :href="'mailto:' + scope.row.email" target="_blank">
            {{ scope.row.email }}
          </el-link>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Contacto" min-width="150">
        <template #default="scope">
          <el-link type="primary" :href="'tel:' + scope.row.phone" target="_blank">
            {{ scope.row.phone }}
          </el-link>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Papel" min-width="120">
        <template #default="scope">
          <span>{{ scope.row.roles.join(', ') }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Estado" sortable min-width="100">
        <template #default="scope">
          <el-tag v-if="scope.row.active" class="ml-2" type="success">
            Ativo
          </el-tag>
          <el-tag v-if="!scope.row.active" class="ml-2" type="danger">
            Não ativo
          </el-tag>
        </template>
      </el-table-column>


      <el-table-column align="center" label="Ações" fixed="right" min-width="120">
        <template #default="scope">
          <router-link :to="'/administrador/utilizadores/perfil/'+scope.row.id">
            <el-button round type="primary" size="small">
              Perfil
              <el-icon><ArrowRight /></el-icon>
            </el-button>
          </router-link>
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

    <el-dialog v-model="dialogFormVisible" :title="'Adicionar novo utilizador'" :width="$filters.isMobile() ? '95%' : '40%'">
      <div v-loading="userCreating" class="form-container">
        <el-form
          ref="userForm"
          :rules="rules"
          :model="newUser"
          :label-position="$filters.isMobile() ? 'top' : 'left'"
          label-width="150px"
          style="max-width: 550px;"
        >
          <el-form-item :label="'Papel'" prop="role">
            <el-select v-model="newUser.role" class="filter-item" placeholder="Selecione um papel">
              <el-option v-for="item in adminRoles" :key="item" :label="$filters.uppercaseFirst(item)" :value="item" />
            </el-select>
          </el-form-item>

          <el-form-item :label="'Nome'" prop="name">
            <el-input v-model="newUser.name" />
          </el-form-item>

          <el-form-item :label="'Email'" prop="email">
            <el-input v-model="newUser.email" />
          </el-form-item>

          <el-form-item label="Telefone" prop="phone">
            <el-input v-model="newUser.phone" type="number" />
          </el-form-item>

          <el-form-item :label="'Password'" prop="password" class="password-item">
            <el-input v-model="newUser.password" show-password minlength="8" maxlength="25" />
          </el-form-item>

          <el-form-item :label="'Confirmar Password'" prop="confirmPassword" style="margin-top: 40px;">
            <el-input v-model="newUser.confirmPassword" show-password minlength="8" />
            <!-- <small class="password-rules">A palavra-passe deverá conter pelo menos 8 caracteres, incluindo uma maiúscula e um caractere especial.</small> -->
          </el-form-item>

          <el-form-item :label="'Dias'" prop="vaccationDays">
            <el-input v-model="newUser.vac_days" type="number"/>
          </el-form-item>
        </el-form>
      </div>
      <template #footer>
        <el-button round @click="dialogFormVisible = false">
          {{ "Cancelar" }}
        </el-button>
        <el-button round type="primary" @click="createUser()">
          {{ "Adicionar" }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
// import UserResource from '@/api/user';
import Resource from '@/api/resource';
import { Plus, Search, Filter, ArrowRight } from '@element-plus/icons-vue';

// import permission from '@/directive/permission'; // Permission directive
// import checkPermission from '@/utils/permission'; // Permission checking

const userResource = new Resource('users');
// const permissionResource = new Resource('permissions');

export default {
  name: 'UserList',
  //   directives: { permission },
  components: {
    Plus,
    Search,
    Filter,
    ArrowRight
  },
  data() {
    var validatePasswordPattern = (rule, value, callback) => {

      if (!value.match(/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,64})/g)) {
        callback(new Error('A palavra-passe deve conter pelo menos 8 caracteres, incluindo uma maiúscula e um caractere especial.'));
      } else {
        callback();
      }
    };

    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.newUser.password) {
        callback(new Error('As palavras-passe não correspondem!'));
      } else {
        callback();
      }
    };

    var validateVac_Days = (rule, value, callback)=>{
      if(value < 0){
        callback(new Error('Não podemos ter menos de 0 dias!'));
      } else{
        callback();
      }
    };

    return {
      adminRoles: ['admin', 'gestor'],
      list: null,
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        active: '1',
        role:  'admin',
      },
      options: [
        {
          value: '',
          label: 'Todos',
        },
        {
          value: '1',
          label: 'Ativos',
        },
        {
          value: '0',
          label: 'Não ativos',
        },
      ],
      roles: ['admin', 'gestor'],
      newUser: {},
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
        role: [{ required: true, message: 'Grupo é obrigatório', trigger: 'change' }],
        name: [{ required: true, message: 'Nome é obrigatório', trigger: 'blur' }],
        telephone: [{ required: true, message: 'Telefone é obrigatório', trigger: 'blur' }],
        email: [
          { required: true, message: 'Email é obrigatório', trigger: 'blur' },
          { type: 'email', message: 'Por favor introduza um email correcto', trigger: ['blur', 'change'] },
        ],
        password: [
          { required: true, message: 'Password é obrigatório', trigger: 'blur' },
          { min: 6, message: 'Password demasiado curta. Min: 8', trigger: 'blur' },
          { max: 25, message: 'Password demasiado longa', trigger: 'blur' },
          { validator: validatePasswordPattern, trigger: ['blur', 'change'] }
        ],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: ['blur', 'change'] }],
        vac_days: [
          {required: true, message: 'Dias de férias é obrigatório', trigger: 'blur'},
          {min: 0, message: 'Numero mínimo de férias', trigger:'blur'},
          {max: 50, message: 'Numero máximo de férias',trigger: 'blur'},
          {validator: validateVac_Days, trigger: ['blur', 'change']}
        ],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },
  computed: {
    // normalizedMenuPermissions() {
    //   let tmp = [];
    //   this.currentUser.permissions.role.forEach(permission => {
    //     tmp.push({
    //       id: permission.id,
    //       name: permission.name,
    //       disabled: true,
    //     });
    //   });
    //   const rolePermissions = {
    //     id: -1, // Just a faked ID
    //     name: 'Inherited from role',
    //     disabled: true,
    //     children: this.classifyPermissions(tmp).menu,
    //   };

    //   tmp = this.menuPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
    //   const userPermissions = {
    //     id: 0, // Faked ID
    //     name: 'Extra menus',
    //     children: tmp,
    //     disabled: tmp.length === 0,
    //   };

    //   return [rolePermissions, userPermissions];
    // },
    // normalizedOtherPermissions() {
    //   let tmp = [];
    //   this.currentUser.permissions.role.forEach(permission => {
    //     tmp.push({
    //       id: permission.id,
    //       name: permission.name,
    //       disabled: true,
    //     });
    //   });
    //   const rolePermissions = {
    //     id: -1,
    //     name: 'Inherited from role',
    //     disabled: true,
    //     children: this.classifyPermissions(tmp).other,
    //   };

    //   tmp = this.otherPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
    //   const userPermissions = {
    //     id: 0,
    //     name: 'Extra permissions',
    //     children: tmp,
    //     disabled: tmp.length === 0,
    //   };

    //   return [rolePermissions, userPermissions];
    // },
    // userMenuPermissions() {
    //   return this.classifyPermissions(this.userPermissions).menu;
    // },
    // userOtherPermissions() {
    //   return this.classifyPermissions(this.userPermissions).other;
    // },
    // userPermissions() {
    //   return this.currentUser.permissions.role.concat(this.currentUser.permissions.user);
    // },
  },
  created() {
    this.resetNewUser();
    this.getList();
    // if (checkPermission(['manage permission'])) {
    //   this.getPermissions();
    // }
  },
  methods: {
    // async getPermissions() {
    //   const { data } = await permissionResource.list({});
    //   const { all, menu, other } = this.classifyPermissions(data);
    //   this.permissions = all;
    //   this.menuPermissions = menu;
    //   this.otherPermissions = other;
    // },
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await userResource.list(this.query);
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
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('This will permanently delete user ' + name + '. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        userResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Delete completed',
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
    // async handleEditPermissions(id) {
    //   this.currentUserId = id;
    //   this.dialogPermissionLoading = true;
    //   this.dialogPermissionVisible = true;
    //   const found = this.list.find(user => user.id === id);
    //   const { data } = await userResource.permissions(id);
    //   this.currentUser = {
    //     id: found.id,
    //     name: found.name,
    //     permissions: data,
    //   };
    //   this.dialogPermissionLoading = false;
    //   this.$nextTick(() => {
    //     this.$refs.menuPermissions.setCheckedKeys(this.permissionKeys(this.userMenuPermissions));
    //     this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.userOtherPermissions));
    //   });
    // },
    createUser() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          this.newUser.roles = [this.newUser.role];
          this.userCreating = true;
          userResource
            .store(this.newUser)
            .then(response => {
              this.$message({
                message: 'Novo utilizador ' + this.newUser.name + '(' + this.newUser.email + ') criado com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewUser();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.userCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewUser() {
      this.newUser = {
        name: '',
        email: '',
        password: '',
        set_password: 'yes',
        set_vacation: 'NULL',
        phone: '',
        confirmPassword: '',
        vac_days: '',
        role: 'gestor',
      };
    },
    // formatJson(filterVal, jsonData) {
    //   return jsonData.map(v => filterVal.map(j => v[j]));
    // },
    // permissionKeys(permissions) {
    //   return permissions.map(permssion => permssion.id);
    // },
    // classifyPermissions(permissions) {
    //   const all = []; const menu = []; const other = [];
    //   permissions.forEach(permission => {
    //     const permissionName = permission.name;
    //     all.push(permission);
    //     if (permissionName.startsWith('view menu')) {
    //       menu.push(this.normalizeMenuPermission(permission));
    //     } else {
    //       other.push(this.normalizePermission(permission));
    //     }
    //   });
    //   return { all, menu, other };
    // },

    // normalizeMenuPermission(permission) {
    //   return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name.substring(10)), disabled: permission.disabled || false };
    // },

    // normalizePermission(permission) {
    //   const disabled = permission.disabled || permission.name === 'manage permission';
    //   return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name), disabled: disabled };
    // },

    // confirmPermission() {
    //   const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
    //   const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
    //   const checkedPermissions = checkedMenu.concat(checkedOther);
    //   this.dialogPermissionLoading = true;
    //   console.log(checkedPermissions);
    //   userResource.updatePermission(this.currentUserId, { permissions: checkedPermissions }).then(response => {
    //     this.$message({
    //       message: 'Permissões actualizadas',
    //       type: 'success',
    //       duration: 5 * 1000,
    //     });
    //     this.dialogPermissionLoading = false;
    //     // this.dialogPermissionVisible = false;
    //   });
    // },
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
