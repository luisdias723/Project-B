<template>
    <div class="form-container">
      <el-form
        ref="transportTypeForm"
        :model="transportType"
        :rules="rules"
        label-width="150px"
        style="max-width: 550px"
        class="form"
      >

      <el-form-item label="Tipo de Transporte" prop="type">
            <el-input v-model="transportType.type" />
          </el-form-item>
        
        </el-form>
     
    </div>
    <div class="botoes">
      <router-link :to="'/transportType/'">
        <el-button round id="botao_cancelar">
          {{ $t("Cancelar") }}
        </el-button>
      </router-link>
  
      <el-button
        round
        type="primary"
        @click="UpdateType()"
        id="botao_atualizar"
      >
        {{ $t("Confirmar") }}
      </el-button>
    </div>
  </template>
  
  <script>
  import Resource from "@/api/resource";
  import { Check } from "@element-plus/icons-vue";
  import UserResource from "@/api/users";
  
  import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
  import { format } from "path";
  



const transportTypeResource = new Resource("transportType");
  
  export default {
    name: "transportTypeFormulario",
    components: {
      Check,
    },
  
    data() {
      return {
        list: [],
        currentId: "",
        
        transportType: {
            type: "",

        },

        rules: {
        type: [{ required: true, message: 'Tipo de Transporte Obrigatório', trigger: 'blur' }],
       
      }
      };
    },
    created() {
      this.currentId = this.$route.params && this.$route.params.id;
      this.resetNewType();
      this.getType();
     
    
 
    
    },
  
    methods: {
      UpdateType() {

        this.$refs.transportTypeForm.validate((valid) => {
          if (valid) {
            transportTypeResource
              .update(this.currentId, this.transportType)
              .then(response => {
                console.log(response.data)
                if(response.data) {
                    this.$message({
                    message: "Informação de Tipo de Transporte actualizado com sucesso",
                    type: "success",
                    duration: 5 * 1000,
                    });
                    this.$router.push("/TransportType");
                    
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

    
   
  
      async getType() {
        const  data  = await transportTypeResource.get(this.currentId);
        this.transportType= data;
     
        if (!data.id) {
          this.$message({
            type: "error",
            message: "Não é possivel visualizar esta marca",
          });
        } else {
        }
      },

     
  
      resetNewType() {
        this.type = {
            type:"",
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