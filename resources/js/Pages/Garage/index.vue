<template>
  <div class="app-container">
    <div class="filter-container">
      <el-row>
        <el-col :xs="12" :sm="12" :md="8">
          <el-button class="filter-item" type="primary" @click="openDialog()">
            <el-icon class="el-icon--left">
              <Plus />
            </el-icon>
            Nova Viatura
          </el-button>
        </el-col>
        <el-col :md="16" style="text-align: right">
          <!--filtrar ativos-->
           <el-select v-model="dialogData.status_id" placeholder="Estado" clearable @change="handleFilter()"
            style="width: 200px"
            class="filter-item">
            <el-option
              v-for="item in statusList"
              :key="item.status"
              :value="item.status_id"
              :label="item.status"
            />
          </el-select>
          <!--Pesquisa-->
          <el-input
            v-model="query.keyword"
            placeholder="Palavra-Chave"
            style="width: 200px"
            class="filter-item"
            @change="handleFilter()"
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
      style="width: 100%"
      :default-sort="{ prop: 'id', order: 'ascending' }"
    >
      <!-- <el-table-column type="selection" width="55" /> -->
      <el-table-column align="center" label="#" width="80">
        <template #default="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Matricula"
        sortable
        min-width="100"
      >
        <template #default="scope">
          <span>{{ scope.row.matricula }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Tipo de Avaria" min-width="130">
        <template #default="scope">
          <span>{{ scope.row.tipoAvaria }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Dt Reporte" min-width="130">
        <template #default="scope">
          <span>{{ scope.row.dataReporte }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Dt Inicio" min-width="130">
        <template #default="scope">
          <span>{{ scope.row.dataInicio }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Dt Conclusão" min-width="130">
        <template #default="scope">
          <span>{{ scope.row.dataConclusao }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Estado" min-width="130">
        <template #default="scope">
          <span>{{ scope.row.status }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Ações"
        fixed="right"
        min-width="120"
      >
        <template #default="scope">
          <el-button round type="primary" size="small"  @click="openDialog(scope.row)"> Editar </el-button>
           <el-button v-if="scope.row.booleanStatus == true" round type="warning" size="small" @click="updateStatus(scope.row)"> Desativar </el-button>
          <el-button v-else-if="scope.row.booleanStatus == false"  round type="primary" size="small" @click="updateStatus(scope.row)"> Ativar </el-button>
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
      v-model="dialogVisible"
      :title="titleDialog"
      :before-close="closeDialog"
      :width="$filters.isMobile() ? '95%' : '40%'"
    >
      <el-form
        v-loading="TruckCreating"
        ref="TruckGarageFrom"
        :model="dialogData"
        :rules="rules"
        label-width="170px"
        :label-position="$filters.isMobile() ? 'top' : 'left'"
        class="formCar"
      >
        <el-form-item label="Matricula" prop="matricula_id">
          <el-select v-model="dialogData.matricula_id" placeholder="">
            <el-option
              v-for="item in truckGarageRegistrationList"
              :key="item.matricula_id"
              :value="item.matricula_id"
              :label="item.matricula"
            />
          </el-select>
        </el-form-item>
        <!-- <el-form-item label="Tipo de Avaria" prop="Tipo de Avaria">
          <el-input v-model="dialogData.tipoAvaria" />
        </el-form-item> -->
        <el-form-item label="Tipo de Avaria" prop="tipoAvaria_id">
          <el-select v-model="dialogData.tipoAvaria_id" placeholder="">
            <el-option
              v-for="item in tipoAvariaList"
              :key="item.tipoAvaria"
              :value="item.tipoAvaria_id"
              :label="item.tipoAvaria"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Estado" prop="status_id">
          <el-select v-model="dialogData.status_id" placeholder="">
            <el-option
              v-for="item in statusList"
              :key="item.status"
              :value="item.status_id"
              :label="item.status"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Data de Reporte" prop="dataReporte">
          <el-date-picker
            v-model="dialogData.dataReporte"
            type="datetime"
            :value-format="'YYYY/MM/DD'"
            placeholder="Selecionar Data e Hora"
          />
        </el-form-item>
        <el-form-item label="Data de Inicio" prop="dataInicio">
          <el-date-picker
            v-model="dialogData.dataInicio"
            type="datetime"
            :value-format="'YYYY/MM/DD'"
            placeholder="Selecionar Data e Hora"
          />
        </el-form-item>
        <el-form-item label="Data de Conclusão" prop="dataConclusao">
          <el-date-picker
            v-model="dialogData.dataConclusao"
            type="datetime"
            :value-format="'YYYY/MM/DD'"
            placeholder="Selecionar Data e Hora"
          />
        </el-form-item>
        <el-form-item label="Motivo da Paragem" prop="reason">
          <el-input
            v-model="dialogData.reason"
            :rows="6"
            type="textarea"
            placeholder=""
          />
        </el-form-item>
      </el-form>
      <!-- </div> -->
      <template #footer>
        <span class="dialog-footer">
          <el-button round @click="dialogVisible = false">Cancelar</el-button>
          <el-button
            v-if="!dialogData.id"
            round
            type="primary"
            @click="createTruckGarage()"
          >
            Confirmar
          </el-button>
          <el-button v-else round type="primary" @click="EditTruckGarage()">
            Atualizar
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import Resource from "@/api/resource";
//import UserResource from "@/api/users";
import GarageResource from "@/api/garage";

const garageResource = new Resource("garage");
const truckgarageResource = new GarageResource();
//const usersResource = new UserResource();

import {
  Plus,
  Close,
  Search,
  Filter,
  ArrowRight,
} from "@element-plus/icons-vue";

export default {
  name: "Garage",

  //icones
  components: {
    Plus,
    Close,
    Search,
    Filter,
    ArrowRight,
  },
  data() {
    return {
      loading: false,
      dialogVisible: false,
      query: {
        page: 1,
        keyword:"",
        limit: 25,
      },
      total: 1,

      //dados que são apresentados na na tabela
      list: [],
      truckGarageList: [],
      tipoAvariaList: [],
      statusList: [],
      loading: true,

      //modal vazio quando criado nova viatura
      dialogData: {
        matricula: "",
        matricula_id: null,
        tipoAvaria_id: "",
        dataReporte: "",
        dataInicio: "",
        dataConclusao: "",
        status_id: "",
        data: "",
      },

      titleDialog: "",

      //definição dos campos obrigatórios
      rules: {
        matricula_id: [
          {
            required: true,
            message: "Insira uma matricula",
            trigger: "blur",
          },
        ],
        tipoAvaria_id: [
          {
            required: true,
            message: "Insira um tipo de avaria ",
            trigger: "blur",
          },
        ],
        dataReporte: [
          {
            required: true,
            message: "Insira o tempo estimado de resolução ",
            trigger: "blur",
          },
        ],
        dataInicio: [
          {
            required: true,
            message: "Insira o tempo estimado de resolução ",
            trigger: "blur",
          },
        ],
        dataConclusao: [
          {
            required: true,
            message: "Insira o tempo estimado de resolução ",
            trigger: "blur",
          },
        ],
        status_id: [
          {
            required: true,
            message: "Insira o estado de resolução ",
            trigger: "blur",
          },
        ],
        reason: [
          {
            required: true,
            message: "Insira o motivo da paragem",
            trigger: "blur",
          },
        ],
      },
    };
  },

  created() {
    this.getList();
    this.getTruckFleets();
    this.getTruckStatus();
    this.getTruckBreakdows();
    this.resetNewGarage();
    console.log(this.list);
  },

  methods: {
    async getList(sort) {
      if (sort) {
        this.query.orderCol = sort.prop;
        this.query.order = sort.order == "ascending" ? "ASC" : "DESC";
      }
      //const { limit, page } = this.query;
      this.loading = true;
      const { data } = await garageResource.list(this.query);
      this.list = data;
      console.log(this.list);
      // this.list.forEach((element, index) => {
      //   element["index"] = (page - 1) * limit + index + 1;
      // });
      //this.total = meta.total;
      this.loading = false;
    },
    //info apresentar no modal
    openDialog(garageService = null) {
      this.titleDialog = "Nova Viatura";
      this.dialogData = {
        matricula: "",
        matricula_id: null,
        tipoAvaria_id: "",
        dataReporte: "",
        dataInicio: "",
        dataConclusao: "",
        status_id: "",
        reason:"",
      };
      console.log(garageService);

      if (garageService !== null) {
        this.dialogData = garageService;

        //apresenta o nome no modal
        this.titleDialog = "Editar Viatura";
      }
      this.dialogVisible = true;
    },

    //fechar modal
    closeDialog() {
      this.dialogVisible = false;
    },

    //filtro
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },

    handleCreate() {
      this.resetNewGarage();
      this.dialogVisible = true;
      this.$nextTick(() => {
        this.$refs["TruckGarageFrom"].clearValidate();
      });
    },

    async getTruckFleets() {
      const data = await truckgarageResource.getTruckFleets(this.query);
      this.truckGarageRegistrationList = data;
    },

    async getTruckStatus() {
      const data = await truckgarageResource.getTruckStatus(this.query);
      this.statusList = data;
    },

    async getTruckBreakdows() {
      const data = await truckgarageResource.getTruckBreakdows(this.query);
      this.tipoAvariaList = data;
    },

    resetNewGarage() {
      this.dialogData = {
        matricula: "",
        matricula_id: null,
        tipoAvaria: "",
        dataReporte: "",
        dataInicio: "",
        dataConclusao: "",
        status_id: "",
        reason: "",
      };
    },
    

    updateStatus(data){
         
            data.booleanStatus = !data.booleanStatus;
            truckgarageResource
                .updateStatus(data)
                .then(()=>{
                    var message = data.status === true ? 'Estado ativado com Sucesso' : 'Estado desativado com Sucesso';
                    this.$message({
                        message: message,
                        type: 'success',
                        duration: 5 * 1000,
                    });
                }).catch(error=>{
                    console.log(error);
                });
            
        },
       

    //Criar nova info

    createTruckGarage() {
      this.$refs["TruckGarageFrom"].validate((valid) => {
        if (valid) {
          this.TruckCreating = true;
          garageResource
            .store(this.dialogData)
            .then((response) => {
              // Ação após o salvamento dos dados, se necessário
              this.$message({
                message: "Viatura adicionada com sucesso.",
                type: "success",
                duration: 5 * 1000,
              });
              this.resetNewGarage();
              this.dialogVisible = false;
              this.getList();
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

    //Editar Info
    EditTruckGarage() {
      this.$refs["TruckGarageFrom"].validate((valid) => {
        if (valid) {
          this.TruckCreating = true;
          garageResource
            .update(this.dialogData.id, this.dialogData)
            .then((response) => {
              // Ação após o salvamento dos dados, se necessário
              this.$message({
                message: "Veículo alterado com sucesso.",
                type: "success",
                duration: 5 * 1000,
              });
              this.resetNewGarage();
              this.dialogVisible = false;
              this.getList();
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
  },
};
</script>

<style lang="scss" scoped>
.formCar {
  margin: auto;
  position: relative;
  max-width: 500px;
}
</style>