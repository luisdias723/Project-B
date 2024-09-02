<template>
    <div class="app-container">
      <div class="filter-container">
        <el-row>
          <el-col :xs="12" :sm="12" :md="8">
            <el-button class="filter-item" type="primary" @click="handleCreate">
              <el-icon class="el-icon--left">
                <Plus />
              </el-icon>
              Novo Modelo de Camião
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
  
  
             <!-- marca -->
             <el-select v-model="query.brand_id" placeholder="Marca" style="width: 100%; margin-bottom: 10px" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckBrandsList"
                  :key="item.id"
                  :label="item.brand"
                  :value="item.id"
                />
              </el-select>
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
               <!-- marca -->
               <el-select v-model="query.brand_id" placeholder="Marca" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckBrandsList"
                  :key="item.id"
                  :label="item.brand"
                  :value="item.id"
                />
              </el-select>
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
        :default-sort="{prop: 'model', order: 'ascending'}"
        @sort-change="getList"
      >
        <el-table-column align="center" label="#" width="80">
          <template #default="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
  
        <el-table-column align="center" prop='model' label="Modelo" sortable min-width="170">
          <template #default="scope">
            <span>{{ scope.row.model }}</span>
          </template>
        </el-table-column>
  
       <el-table-column align="center" prop='brand' label="Marca" sortable min-width="170">
          <template #default="scope">
            <span >{{ scope.row.brand }}</span>
          </template>
        </el-table-column>-->
  
  
         <el-table-column
          align="center"
          label="Ações"
          fixed="right"
          min-width="120"
        >
         <template #default="scope">
            <router-link :to="'/truckModel/' + scope.row.id">
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
        :title="'Adicionar novo Modelo'"
        :width="$filters.isMobile() ? '95%' : '40%'"
      >
        <div v-loading="TruckModelreating" class="form-container">
          <el-form
            ref="TruckModelForm"
            :rules="rules"
            :model="newModel"
            :label-position="$filters.isMobile() ? 'top' : 'left'"
            label-width="150px"
            style="max-width: 550px"
          >

          <el-form-item label="Marca" prop="brand_id">
            <el-select filterable v-model="newModel.brand_id" >
              <el-option 
                v-for="item2 in truckBrandsList"   
                :key="item2.id"
                :value="item2.id"
                :label="item2.brand"
              />
            </el-select>
          </el-form-item>
            <el-form-item label="Modelo" prop="model">
              <el-input v-model="newModel.model" />
            </el-form-item>

          </el-form>
        </div> 
        <template #footer>
          <el-button round @click="dialogFormVisible = false">
            {{ $t("Cancelar") }}
          </el-button>
          <el-button round type="primary" @click="createModel()">
            {{ $t("Confirmar") }}
          </el-button>
        </template> 
      </el-dialog> 
    </div>
  </template>
  
  <script>
  import Resource from "@/api/resource";

  import TruckBrandResource from "@/api/truckBrand";
  import UserResource from "@/api/users";
  
  import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
  import { format } from "path";
  

  const truckBrandsResource= new TruckBrandResource();
  const truckModelResource = new Resource("truckModel");
  
  export default {
    name: "TruckModelList",
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
        truckBrandsList: [],
        loading: true,
        query: {
          page: 1,
          limit: 15,
          keyword: "",
          active: "1",
        },
        newModel: {},
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
          brand_id: [{ required: true, message: 'Escolha uma marca', trigger: 'blur' }],
          model: [{ required: true, message: 'Escreva um modelo', trigger: 'blur' }],
                 
        }
      };
    },
  

    created() {
      this.resetNewModel();
      this.getList();
      this.getTruckBrands();
     
       
    },
  
    methods: {
      async getList(sort) {

        if(sort){
        this.query.orderCol = sort.prop;
        this.query.order = sort.order == 'ascending' ? 'ASC' : 'DESC';
    
      }
        const { limit, page } = this.query;
        this.loading = true;
        const { data, meta } = await truckModelResource.list(this.query);
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
        this.resetNewModel();
        this.dialogFormVisible = true;
        this.$nextTick(() => {
          this.$refs["TruckModelForm"].clearValidate();
        });
      },
  
  
  
      async getTruckBrands() {
        const { data } = await truckBrandsResource.getTruckBrands(this.query);
        this.truckBrandsList = data;
      },
  
     
  
      resetNewModel() {
        this.newModel = {
          model: "",
          brand_id:"",
  
          /* user_id: '', */
         };
      },
  
  
      createModel() {
    
        this.$refs["TruckModelForm"].validate((valid) => {

            console.log(this.newModel.brand_id)
          if (valid) {
           // this.newTruckroles = [this.newTruck.role];
            this.TruckModelCreating = true;
            truckModelResource
              .store(this.newModel)
              .then((response) => {
                this.$message({
                  message:
                    "Modelo (" + this.newModel.model + ") criado com sucesso.",
                  type: "success",
                  duration: 5 * 1000,
                });
                this.resetNewModel();
                this.dialogFormVisible = false;
                this.handleFilter();
              })
              .catch((error) => {
                console.log(error);
              })
              .finally(() => {
                this.TruckModelCreating = false;
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