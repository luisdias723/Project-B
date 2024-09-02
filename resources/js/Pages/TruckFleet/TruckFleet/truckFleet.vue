<template>
    <div class="form-container">
      <el-form
        ref="TruckFleetForm"
        :rules="rules"
        :model="truck"
        label-width="150px"
        style="max-width: 550px"
        class="form"
      >

      <el-form-item label="Matricula" prop="registration">
            <el-input v-model="truck.registration" />
          </el-form-item>
        
          <el-form-item label="Tipo de Trasporte" prop="type_id">
            <el-select filterable v-model="truck.type_id">
              <el-option
                v-for="item in transportTypesList"
                :key="item.id"
                :value="item.id"
                :label="item.type"
              />
            </el-select>
          </el-form-item>
  
          <el-form-item label="Marca" prop="brand_id">
            <el-select filterable v-model="truck.brand_id" @change="getTruckModels()">
              <el-option 
                v-for="item2 in truckBrandsList"   
                :key="item2.id"
                :value="item2.id"
                :label="item2.brand"
              />
            </el-select>
          </el-form-item>
  
          <el-form-item v-if="truck.brand_id" label="Modelo" prop="model_id">
            <el-select filterable v-model="truck.model_id">
              
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
               v-model="truck.year"  
               placeholder="Ano do Camião "
               type="year"  
            ></el-date-picker>
         
          </el-form-item>

          <el-form-item label="Data de Inspeção" prop="inspection_date">
            <el-date-picker
              v-model="truck.inspection_date"
              type="date"
              placeholder="Insira o Ano do Camião "
              size="large"
              :value-format="'YYYY/MM/DD'"
            ></el-date-picker>
          </el-form-item>
  
          <el-form-item label="Carga Máxima" prop="max_cargo">
            <el-input v-model="truck.max_cargo"
            type="number" />
          </el-form-item>
        </el-form>
     
    </div>
    <div class="botoes">
      <router-link :to="'/truckFleet/'">
        <el-button round id="botao_cancelar">
          {{ $t("Cancelar") }}
        </el-button>
      </router-link>
  
      <el-button
        round
        type="primary"
        @click="UpdateTruck()"
        id="botao_atualizar"
      >
        {{ $t("Confirmar") }}
      </el-button>
    </div>
  </template>
  
  <script>
  import Resource from "@/api/resource";
  import { Check } from "@element-plus/icons-vue";
  import TransportTypeResource from "@/api/transportType";
import TruckModelResource from "@/api/truckModel";
import TruckBrandResource from "@/api/truckBrand";
  import UserResource from "@/api/users";
  
  import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
  import { format } from "path";
  


const transportTypesResource= new TransportTypeResource();
const truckModelsResource= new TruckModelResource();
const truckBrandsResource= new TruckBrandResource();
const truckFleetsResource = new Resource("truckFleet");
  
  export default {
    name: "TruckFleetFormulario",
    components: {
      Check,
    },
  
    data() {

      var validateTruckYear = (rule, value, callback) => {
       console.log(value);
      if (value > this.currentDate) {
        callback(new Error('Insira um ano válido'));
      } else {
        callback();
      }
    };
      return {
        list: [],
        currentId: "",
        transportTypesList: [],
        truckModelsList: [],
        truckBrandsList: [],
        usersList: [],
        currentDate:new Date(),
        truck: {
            registration: "",
            brand_id: "",
            model_id: "",
            type_id: "",
            year: "",
            inspection_date: "",
            max_cargo: "",
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
      this.currentId = this.$route.params && this.$route.params.id;
      this.resetNewTruck();
      this.getTruck();
 
    this.getTransportTypes();
    },
  
    methods: {
      UpdateTruck() {
        
        this.$refs.TruckFleetForm.validate((valid) => {
          if (valid) {
            truckFleetsResource
              .update(this.currentId, this.truck)
              .then(response => {
                
                if(response.data) {
                    this.$message({
                    message: "Informação de Camião actualizada com sucesso",
                    type: "success",
                    duration: 5 * 1000,
                    });
                    this.$router.push("/TruckFleet");
                    
                }else{
                    this.$message({
                    message: "Tente novamente",
                    type: "error",
                    duration: 5 * 1000,
                    });
                }
                
              })
              .catch((error) => {
                console.log(error);
                this.loading = false;
              });
          } else {
            console.log("error");
            return false;
          }
        }).catch(error => {console.log(error)});
      },

    
      async getTruckModelsInit() {
       
      const { data } = await truckModelsResource.getTruckModels({brand_id: this.truck.brand_id});
      this.truckModelsList = data;
      
    },
      async getTruckModels() {
        this.truck.model_id=""; 
      const { data } = await truckModelsResource.getTruckModels({brand_id: this.truck.brand_id});
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
  
  
      async getTruck() {
        const  data  = await truckFleetsResource.get(this.currentId);
        this.truck = data;
  
        if (!data.id) {
          this.$message({
            type: "error",
            message: "Não é possivel visualizar este perfil",
          });
        } else {
        this.getTruckModelsInit();
         this.getTruckBrands();
        }
      },

     
  
      resetNewTruck() {
        this.truck = {
            registration: "",
            brand_id: "",
        model_id: "",
        type_id: "",
        year: "",
        inspection_date: "",
        max_cargo: "",
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