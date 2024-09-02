<template>
    <div class="app-container">
      <div class="filter-container">
        <el-row>
          <el-col :xs="12" :sm="12" :md="8">
            <el-button class="filter-item" type="primary" @click="handleCreate">
              <el-icon class="el-icon--left">
                <Plus />
              </el-icon>
              Novo Tipo de Transporte
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
                :placeholder="$t('Palavra-Chave')"
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
              :placeholder="$t('Palavra-Chave')"
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
        :default-sort="{prop: 'id', order: 'ascending'}"
        @sort-change="getList"
      >
        <el-table-column align="center" label="#" width="80">
          <template #default="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
  
        <el-table-column align="center" label="Tipo de Transporte" sortable min-width="170">
          <template #default="scope">
            <span>{{ scope.row.type }}</span>
          </template>
        </el-table-column>
  
  
  
         <el-table-column
          align="center"
          label="Ações"
          fixed="right"
          min-width="120"
        >
         <template #default="scope">
            <router-link :to="'/transportType/' + scope.row.id">
              <el-button round type="primary" size="small"> Editar </el-button>
            </router-link>
          </template>
        </el-table-column>-->
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
        :title="'Adicionar novo Tipo de Transporte'"
        :width="$filters.isMobile() ? '95%' : '40%'"
      >
        <div v-loading="TruckTransportTypereating" class="form-container">
          <el-form
            ref="TransportTypeForm"
            :rules="rules"
            :model="newTransportType"
            :label-position="$filters.isMobile() ? 'top' : 'left'"
            label-width="150px"
            style="max-width: 550px"
          >
            <el-form-item label="Tipo de Transporte" prop="type">
              <el-input v-model="newTransportType.type" />
            </el-form-item>

          </el-form>
        </div> 
        <template #footer>
          <el-button round @click="dialogFormVisible = false">
            {{ $t("Cancelar") }}
          </el-button>
          <el-button round type="primary" @click="createType()">
            {{ $t("Confirmar") }}
          </el-button>
        </template> 
      </el-dialog> 
    </div>
  </template>
  
  <script>
  import Resource from "@/api/resource";
 
 
  
  import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
  import { format } from "path";
  

 // const truckModelsResource= new TruckModelResource();
  const transportTypeResource = new Resource("transportType");
  
  export default {
    name: "TransportTypeList",
    components: {
      Plus,
      Search,
      Filter,
      ArrowRight,
    },
  
    data() { 
  
      return {
        list: [],
        usersList: [],
        loading: true,
        query: {
          page: 1,
          limit: 15,
          keyword: "",
          active: "1",
        },
        newTransportType: {},
        dialogFormVisible: false,
        dialogPermissionVisible: false,
        dialogPermissionLoading: false,
        currentUserId: 0,
        currentUser: {
          name: "",
          permissions: [],
          rolePermissions: [],
        },
  
        rules: {
          type: [{ required: true, message: 'Escolha um tipo de transporte', trigger: 'blur' }],
                 
        }
      };
    },
  
    
    created() {
      this.resetNewTransportType();
      this.getList();
     
    
    },
  
    methods: {
      async getList(sort) {
      
        if(sort){
        this.query.orderCol = sort.prop;
        this.query.order = sort.order == 'ascending' ? 'ASC' : 'DESC';
      }
        const { limit, page } = this.query;
        this.loading = true;
        const { data, meta } = await transportTypeResource.list(this.query);
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
       // this.getUsers();
        this.resetNewTransportType();
        this.dialogFormVisible = true;
        this.$nextTick(() => {
          this.$refs["TransportTypeForm"].clearValidate();
        });
      },
  

  
      resetNewTransportType() {
        this.newTransportType = {
          type: "",
  
          /* user_id: '', */
         };
      },
  
  
      createType() {
    
        this.$refs["TransportTypeForm"].validate((valid) => {
          if (valid) {
           // this.newTruckroles = [this.newTruck.role];
            this.TransportTypeCreating = true;
            transportTypeResource
              .store(this.newTransportType)
              .then((response) => {
                this.$message({
                  message:
                    "Tipo de Transporte (" + this.newTransportType.type+ ") criada com sucesso.",
                  type: "success",
                  duration: 5 * 1000,
                });
                this.resetNewTransportType();
                this.dialogFormVisible = false;
                this.handleFilter();
              })
              .catch((error) => {
                console.log(error);
              })
              .finally(() => {
                this.TransportTypeCreating = false;
              });
          } else {
            console.log("error submit!!");
            return false;
          }
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

<style>
.el-popper.is-light.el-popover{
padding:30px !important
}
</style> 