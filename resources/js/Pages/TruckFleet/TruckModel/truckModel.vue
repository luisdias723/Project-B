<template>
    <div class="form-container">
      <el-form
        ref="TruckModelForm"
        :model="model"
        :rules="rules"
        label-width="150px"
        style="max-width: 550px"
        class="form"
      >
  
          <el-form-item label="Marca" prop="brand_id">
            <el-select filterable v-model="model.brand_id" >
              <el-option 
                v-for="item2 in truckBrandsList"   
                :key="item2.id"
                :value="item2.id"
                :label="item2.brand"
              />
            </el-select>
          </el-form-item>
            <el-form-item label="Modelo" prop="model">
              <el-input v-model="model.model" />
            </el-form-item>
        </el-form>
     
    </div>
    <div class="botoes">
      <router-link :to="'/truckModel/'">
        <el-button round id="botao_cancelar">
          {{ $t("Cancelar") }}
        </el-button>
      </router-link>
  
      <el-button
        round
        type="primary"
        @click="UpdateModel()"
        id="botao_atualizar"
      >
        {{ $t("Confirmar") }}
      </el-button>
    </div>
  </template>
  
  <script>
  import Resource from "@/api/resource";
  import { Check } from "@element-plus/icons-vue";
  import TruckBrandResource from "@/api/truckBrand";

  
  import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
  import { format } from "path";
  

const truckBrandsResource= new TruckBrandResource();
const truckModelsResource = new Resource("truckModel");
  
  export default {
    name: "TruckModelFormulario",
    components: {
      Check,
    },
  
    data() {
      return {
        list: [],
        currentId: "", 
        truckBrandsList: [],
        model: {
            model: "",
            brand_id: "",
            
        },

        rules: {
       brand_id: [{ required: true, message: 'Escolha uma marca', trigger: 'blur' }],       
        model: [{ required: true, message: 'ModeloObrigatória', trigger: 'blur' }],    
      }
      };
    },
    created() {
      this.currentId = this.$route.params && this.$route.params.id;
      this.resetNewModel();
      this.getModel();
 
    this.getTruckBrands();
    },
  
    methods: {
      UpdateModel() {
        
        this.$refs.TruckModelForm.validate((valid) => {
          if (valid) {
            truckModelsResource
              .update(this.currentId, this.model)
              .then(response => {
                
                if(response.data) {
                    this.$message({
                    message: "Informação de Modelo actualizada com sucesso",
                    type: "success",
                    duration: 5 * 1000,
                    });
                    this.$router.push("/TruckModel");
                    
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


      
      async getModel() {
        const  data  = await truckModelsResource.get(this.currentId);
        this.model = data;
      
        if (!data.id) {
          this.$message({
            type: "error",
            message: "Não é possivel visualizar esta marca",
          });
        } else {
        }
      },
    

    async getTruckBrands() {
      const { data } = await truckBrandsResource.getTruckBrands(this.query);
      this.truckBrandsList = data;
    },

  
      resetNewModel() {
        this.model = {
            model: "",
            brand_id: "",
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