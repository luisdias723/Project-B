<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <el-col :xs="12" :sm="12" :md="4">
          <el-button class="filter-item" type="primary" @click="handleCreate">
            <el-icon class="el-icon--left">
              <Plus />
            </el-icon>
            Novo Camião
          </el-button>
        </el-col>

        <el-col
          v-if="$filters.isMobile()"
          :xs="12"
          :md="12"
          style="text-align: right"
        >
          <el-popover placement="bottom" :width="400" style="padding:10px !important" trigger="click">
            <template #reference>
              <el-button circle type="primary">
                <el-icon>
                  <Filter />
                </el-icon>
              </el-button>
            </template>

            <el-date-picker
                v-model="query.date_range"
                type="daterange"
                range-separator="até"
                start-placeholder="Inicio"
                end-placeholder="Fim"
                class="filter-item margin-item"
                clearable
                style="width: 100%; margin-bottom: 10px; margin-right: 5px !important"
                @change="handleFilter();"
              />

      <!-- marca -->
       <el-select v-model="query.brand_id" placeholder="Marca" style="width: 100%; margin-bottom: 10px" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckBrandsList"
                  :key="item.id"
                  :label="item.brand"
                  
                  :value="item.id"
                />
              </el-select>

              <!-- modelo 
       <el-select v-model="query.model_id" placeholder="Modelo" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckModelsList"
                  :key="item.id"
                  :label="item.model"
                  :value="item.id"
                />
              </el-select>-->

              <!-- tipo de transporte -->
       <el-select v-model="query.type_id" placeholder="Tipo de Transporte" style="width: 100%; margin-bottom: 10px" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in transportTypesList"
                  :key="item.id"
                  :label="item.type" 
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

        <el-col v-if="!$filters.isMobile()" :md="20" style="text-align: right">

          <el-date-picker
                v-model="query.date_range"
                type="daterange"
                range-separator="até"
                start-placeholder="Inicio"
                end-placeholder="Fim"
                class="filter-item margin-item"
                clearable
                style="margin-right: 5px !important"
                @change="handleFilter();"
              />

      <!-- marca -->
       <el-select v-model="query.brand_id" placeholder="Marca" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckBrandsList"
                  :key="item.id"
                  :label="item.brand"
                  :value="item.id"
                />
              </el-select>

              <!-- modelo 
       <el-select v-model="query.model_id" placeholder="Modelo" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in truckModelsList"
                  :key="item.id"
                  :label="item.model"
                  :value="item.id"
                />
              </el-select>-->

              <!-- tipo de transporte -->
       <el-select v-model="query.type_id" placeholder="Tipo de Transporte" clearable class="filter-item" @change="handleFilter()" >
                <el-option
                  v-for="item in transportTypesList"
                  :key="item.id"
                  :label="item.type"
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
      :default-sort="{prop: 'id', order: 'ascending'}"
        @sort-change="getList"
    >
      <el-table-column align="center" prop="id" label="#" width="80">
        <template #default="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" prop="registration" label="Matricula" sortable min-width="150">
        <template #default="scope">
          <span>{{ scope.row.registration }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="brand"  label="Marca" sortable min-width="170">
        <template #default="scope">
          <span>{{ scope.row.brand }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" prop="model" label="Modelo" sortable min-width="170">
        <template #default="scope">
          <span >{{ scope.row.model }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" prop="type" label="Tipo de Transporte" sortable min-width="170">
        <template #default="scope">
          <span>{{ scope.row.type}}</span>
        </template>
      </el-table-column>

    

      <el-table-column align="center" prop="inspection_date" label="Data de Inspecção" sortable min-width="170">
        <template #default="scope">
         <el-tag v-if="scope.row.inspection_date<=this.forCurrentDate"  type="danger"  class="ml-2" effect="dark"><span>Data Expirada {{ scope.row.inspection_date }}</span></el-tag>
         <el-tag v-if="scope.row.inspection_date >this.forCurrentDate && scope.row.inspection_date < this.limitDate"  type="warning"  class="ml-2" effect="dark"> <span>A expirar em breve: {{ scope.row.inspection_date }}</span>  </el-tag>
         <el-tag v-if="scope.row.inspection_date >this.forCurrentDate > this.limitDate"  type="warning"  class="ml-2" effect="dark"> <span> {{ scope.row.inspection_date }}</span>  </el-tag>
        </template>
      </el-table-column>


      <el-table-column
        align="center"
        label="Ações"
        fixed="right"
        min-width="120"
      >
        <template #default="scope">
          <router-link :to="'/truckFleet/' + scope.row.id">
            <el-button round type="primary" size="small"> Editar </el-button>
          </router-link>
          <el-button
            round
            type="danger"
            size="small"
            @click="handleDelete(scope.row.id, scope.row.registration)"
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
      :title="'Adicionar novo camião'"
      :width="$filters.isMobile() ? '95%' : '40%'"
    >
      <div v-loading="TruckCreating" class="form-container">
        <el-form
          ref="TruckFleetForm"
          :rules="rules"
          :model="newTruck"
          :label-position="$filters.isMobile() ? 'top' : 'left'"
          label-width="150px"
          style="max-width: 550px"
        >
          <el-form-item label="Matricula" prop="registration">
            <el-input v-model="newTruck.registration" />
          </el-form-item>

     

          <el-form-item label="Tipo de Trasporte" prop="type_id">
            <el-select filterable v-model="newTruck.type_id">
              <el-option
                v-for="item in transportTypesList"
                :key="item.id"
                :value="item.id"
                :label="item.type"
              />
            </el-select>
          </el-form-item>

          <el-form-item label="Marca" prop="brand_id">
            <el-select filterable v-model="newTruck.brand_id" @change="getTruckModels()">
              <el-option 
                v-for="item2 in truckBrandsList"   
                :key="item2.id"
                :value="item2.id"
                :label="item2.brand"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item v-if="newTruck.brand_id" label="Modelo" prop="model_id">
            <el-select filterable v-model="newTruck.model_id">
              
              <el-option 
                v-for="item3 in truckModelsList"   
                :key="item3.id"
                :value="item3.id"
                :label="item3.model"
              />
            
  
            </el-select>
          </el-form-item>
          <el-form-item label="Ano do Camião" prop="year">
            <el-date-picker
               v-model="newTruck.year"  
               placeholder="Ano do Camião "
               type="year"  
            ></el-date-picker>
         
          </el-form-item>
        

          <el-form-item label="Data de Inspeção" prop="inspection_date">
            <el-date-picker
              v-model="newTruck.inspection_date"         
              placeholder="Insira a data de Inspeção "
              size="large"
              :value-format="'YYYY/MM/DD'"
            ></el-date-picker>
          </el-form-item>

          <el-form-item label="Carga Máxima" prop="max_cargo">
            <el-input v-model="newTruck.max_cargo" 
            type="number"/>
          </el-form-item>
        </el-form>
      </div> 
      <template #footer>
        <el-button round @click="dialogFormVisible = false">
          {{ $t("Cancelar") }}
        </el-button>
        <el-button round type="primary" @click="createTruck()">
          {{ $t("Confirmar") }}
        </el-button>
      </template> 
    </el-dialog> 
  </div>
</template>

<script>
import Resource from "@/api/resource";
import TransportTypeResource from "@/api/transportType";
import TruckModelResource from "@/api/truckModel";
import TruckBrandResource from "@/api/truckBrand";
import UserResource from "@/api/users";

import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
import { format } from "path";

const transportTypesResource= new TransportTypeResource();
const truckModelsResource= new TruckModelResource();
const truckBrandsResource= new TruckBrandResource();
const usersResource = new UserResource();
const truckFleetResource = new Resource("truckFleet");

export default {
  name: "TruckFleetList",
  components: {
    Plus,
    Search,
    Filter,
    ArrowRight,
  },

  data() { 

    var validateTruckYear = (rule, value, callback) => {
      if (value > this.currentDate) {
        callback(new Error('Insira um ano válido'));
      } else {
        callback();
      }
    };
    return {
      list: [],
      transportTypesList: [],
      truckModelsList: [],
      truckBrandsList: [],
      usersList: [],
      currentDate:new Date(),
      forCurrentDate:"",
      limitDate:"",
      loading: true,
      query: {
        page: 1,
        limit: 15,
        keyword: "",
        order: 'ASC', 
        orderCol: 'inspection_date',
        date_range: [],
       
      },
      newTruck: {},
      brandID:"",
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
        registration: [{ required: true, message: 'Matricula Obrigatória', trigger: 'blur' }],
        brand_id: [{ required: true, message: 'Escolha uma marca', trigger: 'blur' }],
        model_id: [{ required: true, message: 'Escolha um modelo', trigger: 'blur' }],
        type_id: [{ required: true, message: 'Escolha um tipo de transporte', trigger: 'blur' }],
        year: [{ required: true, message: 'Insira um ano válido', trigger: ['blur'] },
        { validator: validateTruckYear, trigger: ['blur', 'change'] }],
        inspection_date: [{ required:true, type: 'date', message: 'Data não é válida', trigger: 'blur' }],
        max_cargo: [{ required:true, message: 'Deve inserir um numero', trigger: 'blur' }],
               
      }
    };
  },

  
  created() {
    this.resetNewTruck();
    this.getList();
    this.getTruckBrands();
    this.getTransportTypes();
    this.dates();
 
  },

  methods: {
    async getList(sort) {

      if(sort){
        this.query.orderCol = sort.prop;
        this.query.order = sort.order == 'ascending' ? 'ASC' : 'DESC';
    
      }
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await truckFleetResource.list(this.query);
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

    dates(){
        this.forCurrentDate= this.currentDate.toISOString().split('T')[0];
        this.limitDate=new Date(this.currentDate.setMonth(this.currentDate.getMonth()+1)).toISOString().split('T')[0];
       
    },

    handleCreate() {
      this.getUsers();
      this.resetNewTruck();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs["TruckFleetForm"].clearValidate();
      });
    },

   async getTruckModels() {
    this.newTruck.model_id="";
      const { data } = await truckModelsResource.getTruckModels({brand_id: this.newTruck.brand_id});
      this.truckModelsList = data;
    },

    async getTruckBrands() {
      const { data } = await truckBrandsResource.getTruckBrands(this.query);
      this.truckBrandsList = data;
    },

    async getTransportTypes() {
      const { data } = await transportTypesResource.getTransportTypes(this.query);
      this.transportTypesList = data;
    },


    async getUsers() {
      const { data } = await usersResource.getUsers(this.query);
      this.usersList = data;
    },


  


    resetNewTruck() {
      this.newTruck = {
        registration: "",
        brand_id: "",
        model_id: "",
        type_id: "",
        year: "",
        inspection_date: "",
        max_cargo: "",

        /* user_id: '', */
       };
    },


    createTruck() {
     
      this.$refs["TruckFleetForm"].validate((valid) => {
        if (valid) {
         // this.newTruckroles = [this.newTruck.role];
          this.TruckCreating = true;
          truckFleetResource
            .store(this.newTruck)
            .then((response) => {
              this.$message({
                message:
                  "Camião  com matrícula(" + this.newTruck.registration + ") criada com sucesso.",
                type: "success",
                duration: 5 * 1000,
              });
              this.resetNewTruck();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.TruckCreating = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },

    handleDelete(id, registration) {
      this.$confirm(
        "This will permanently delete Camião " + registration + ". Continue?",
        "Warning",
        {
          confirmButtonText: "OK",
          cancelButtonText: "Cancel",
          type: "warning",
        }
      )
        .then(() => {
          truckFleetResource
            .destroy(id)
            .then(response => {
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
            message: "Operação cancelada",
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

 <style>
.el-popper.is-light.el-popover{
padding:30px !important
}
</style> 