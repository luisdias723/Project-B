<template>
  <div class="app-container">
    <el-row>
      <el-col :xs="12" :sm="12" :md="8">
        <el-button class="filter-item" type="primary" @click="handleCreate" >
          <el-icon class="el-icon--left">
            <Plus />
          </el-icon>
          Nova Ausência
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

            <span class="text-muted">Utilizador</span>
            <el-select v-model="query.user_id" placeholder="Utilizador" class="filter-item selectmargin" style="width: 100%; margin-bottom: 10px;" @change="getEventsToCalendar">
              <el-option 
                v-for="item in userList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>

            <span class="text-muted">Ausencia</span>
            <el-select v-model="query.type" placeholder="Ausencia" class="filter-itme selectmargin" style="width: 100%; margin-bottom: 10px;" @change="getEventsToCalendar">
              <el-option v-for="item in typeList"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>

            <span class="text-muted">Palavra-chave</span>
            <el-input v-model="query.keyword" style="width: 200px" placeholder="Pesquisar" class="filter-item" @change="handleFilter"/>       
            <el-button circle classs="filter-item" type="primary" @click="handleFilter">
              Pesquisar
            </el-button>
          </el-popover>
        </el-col>
      
      <el-col  v-if="!$filters.isMobile()" :md="16" style="text-align:right">
        <el-select v-model="query.user_id" placeholder="Utilizador" class="filter-item selectMargin" @change="getEventsToCalendar">
          <el-option
            v-for="item in userList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          />
        </el-select>
        <el-select v-model="query.type" placeholder="Ausencia" class="filter-item selectMargin" @change="getEventsToCalendar">
          <el-option
            v-for="item2 in typeList"
            :key="item2.id"
            :label="item2.name"
            :value="item2.id"
          />
        </el-select>   
      </el-col>
    </el-row>
    
    <el-row>
      <el-col>        
        <Absences style="background: white; padding-top: 10px;" @open-modal="openModal" :events="events" :query="query"/>
      </el-col>
    </el-row>

    <!--New Absence Modal-->
    <el-dialog 
      v-model="dialogFormVisible"
      :title="dialogTitle"
      :width="$filters.isMobile() ? '95%' : '40%'"
    >
      <div v-loading="areaCreating" class="form-container">
        <el-form
          ref="ausenciasForm"
          :rules="rules"
          :model="newAusencia"
          :label-position="$filters.isMobile() ? 'top' : 'left'"
          label-width="150px"
          style="max-width: 550px"
        >
          <el-form-item label="Nome" prop="user_id">
            <el-select filterable v-model="newAusencia.user_id" :disabled="newAusencia.id">
              <el-option 
                v-for="item in userList"
                :key="item.id"
                :value="item.id"
                :label="item.name"
              />
            </el-select>
          </el-form-item>
          
          <el-form-item label="Tipo de Ausência" prop="type">
            <el-select filterable v-model="newAusencia.type">
              <el-option
                v-for="item2 in typeList"
                :key="item2.id"
                :value="item2.id"
                :label="item2.name"
              />
            </el-select>  
          </el-form-item>
          
          <el-form-item label="Data" prop="date_range" style="font-weight: 700">
              <el-date-picker v-model="newAusencia.date_range" type="daterange" value-format="YYYY-MM-DD" unlink-panels range-separator="Até" start-placeholder="Data Inicio" end-placeholder="Data Fim"/>
          </el-form-item>

          <el-form-item label="Confirmação" prop="confirmed">
             <el-radio-group v-model="newAusencia.confirmed">
              <el-radio :label="true">Confirmado</el-radio>
              <el-radio :label="false">Não Confirmado</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-form>
      </div>

      <template #footer>
        <el-button round @click="dialogFormVisible = false">
          {{"Cancelar"}}
        </el-button>
        <el-button v-if="isNew" :loading="newAb" round type="primary" @click="createAbsence()">
          {{"Confirmar"}}
        </el-button>
        <el-button v-else round :loading="upAb" type="primary" @click="editAbsence()">
          {{"Atualizar"}}
        </el-button>
        <el-button round v-if="!isNew" type="danger" @click="handleDelete(newAusencia.id, newAusencia.name)">
          {{"Remover"}}  
        </el-button> 
      </template>
    </el-dialog>
  </div>
</template>

<script>

import Resource from '@/api/resource';
import UserResource from "@/api/users";
import TypeResource from '@/api/types';
import AbsenceResource from '@/api/absences'; 

import Absences from './components/absencesForm.vue'
import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";

const absenceResource = new Resource('absences');
const usersResource = new UserResource();
const typeResource = new TypeResource();
const eventResource = new AbsenceResource();
const abResource = new AbsenceResource();

export default {
  name: "Calendario",
  components: {
    Plus,
    Search,
    Filter,
    ArrowRight,
    Absences,
  },
  
  data(){
    return{
      query:{
        user_id: "",
        type: "",
      },
      newAb:false,
      upAb:false,
      areaCreating:false,
      newAusencia: {
        id:"", 
        name: "",
        type: "",
        date_range: [],
        confirmed: "",
        },
      dialogFormVisible: false,
      isNew: false,
      dialogTitle :'',
      userList: [],
      typeList: [],
      events: {},
      rules: {
        user_id:[{required: true, message: 'Nome é obrigatorio', trigger: ['blur', 'change']}],
        type:[{required: true, message: 'Tipo de ausência é obrigatoria', trigger:'blur'}],
        daterange:[{required: true, message:'Ambos os campos são obrigatorias', trigger: 'change'}],
      },
    };
  },
  

  created() {
    this.resetNewAusencia();
    this.getUsers();
    this.getTypes();
    this.getEventsToCalendar();
  },

  methods:{ 
    handleFilter(){
      this.getEventsToCalendar();
    },

     async getEventsToCalendar(){
            eventResource.getEventsToCalendar(this.query)
            .then((response) => {
              this.events = response;   
            })
            .catch((error) => {
            })
        }, 

    handleCreate(){
      this.resetNewAusencia();
      this.isNew = true;
      this.dialogTitle = 'Adicionar Nova Ausência';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs["ausenciasForm"].clearValidate();
      });
    },

    async getUsers(){
      const { data } = await usersResource.getUsers();
      this.userList = data;
    },

    async getTypes(){
      const  data  = await typeResource.getTypes();
      this.typeList = data;
    },

    resetNewAusencia(){
      this.newAusencia = {
        user_id: null,
        date_range: [],
        type: "",
        confirmed: true,
        name: "",
        type_id: null,
      }
    },

    openModal(data, newEvent){
      
      if(newEvent == false){
        this.isNew = false;
        this.dialogTitle = 'Editar Ausência';
        this.newAusencia = data;
      } else{
        this.isNew = true;
        this.dialogTitle = 'Adicionar Nova Ausência';
        this.newAusencia = data;
      }
      this.dialogFormVisible = true;
    },

    createAbsence(){
      this.newAb = true;
      this.$refs["ausenciasForm"].validate((valid)=>{
        if(valid){
          this.areaCreating = true;
          absenceResource
            .store(this.newAusencia)
            .then((response)=>{
              this.$message({
                message:
                  "Ausência criada com sucesso.",
                  type: "success",
                  duration: 5 * 1000,
              });
              this.resetNewAusencia();
              this.dialogFormVisible=false;
              this.handleFilter();
              this.newAb = false;
              this.getEventsToCalendar();
            })
            .catch((error)=>{
              console.log(error);
              this.areaCreating = false;
              this.newAb = false;
            })
            .finally(()=>{
              this.areaCreating = false;
              this.newAb = false;
            });
        } else {
          console.log("error submit!!");
          this.newAb = false;
          return false;
        }
      });
    },

    editAbsence(){
      this.upAb = true;
      this.$refs["ausenciasForm"].validate((valid)=>{
        if(valid){
          this.loading = true;
          if( this.newAusencia.type == 1){
            abResource
              .updateFerias(this.newAusencia)
              .then(() => {
                this.$message({
                  message:
                    "Ausência editada com sucesso.",
                    type: "success",
                    duration: 5 * 1000,
                });
                this.upAb = false;
                this.dialogFormVisible = false;
                this.getEventsToCalendar();
              })
              .catch((error)=>{
                console.log(error);
                this.areaCreating = false;
                this.upAb = false;
              })
          }else{
            absenceResource
              .update(this.newAusencia.id, this.newAusencia)
              .then(() => {
                this.$message({
                  message:
                    "Ausência editada com sucesso.",
                    type: "success",
                    duration: 5 * 1000,
                });
                this.upAb = false;
                this.dialogFormVisible = false;
                this.getEventsToCalendar();
              })
              .catch((error)=>{
                console.log(error);
                this.areaCreating = false;
                this.upAb = false;
              })
          }
        } else {
          console.log("error submit!!");
          this.upAb = false;
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
          absenceResource
            .destroy(id)
            .then((response) => {
              this.$message({
                type: "success",
                message: "Delete completed",
              });
              this.handleFilter();
              this.areaCreating = false;
              this.dialogFormVisible = false;
              this.getEventsToCalendar();
              
            })
            .catch((error) => {
              console.log(error);
              this.areaCreating = false;
            })
            .finally(()=>{
              this.areaCreating = false;
            });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "Operação canceleda",
          });
        });
    },
  }
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

.el-row{
  margin-bottom: 10px;
}
.selectMargin {
  margin-right: 10px;
}
</style>