<template>
    <div class="app-container">
        <div class="filter-container">   
            <el-row>
                <el-col :xs="12" :sn="12" :md="8">
                    <el-button class="filter-item" type="primary" @click="handleCreate">
                        <el-icon class="el-icon--left">
                            <Plus />
                        </el-icon>
                        Novo Tipo de Ausência
                    </el-button>
                </el-col>
            </el-row>
        </div>

        <el-table
            :data="list"
            border
            fit
            stripe
            highlight-current-row
            :scrollbar-always-on="true"
            style="width: 100%"
            v-loading="loading"       
        >
            <el-table-column align="center" label="#" width="80">
                <template #default="scope">
                    <span>{{scope.row.id}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="Tipo de Ausência" min-width="200">
                <template #default="scope">
                    <span>{{scope.row.name}}</span>
                </template>
            </el-table-column>

            <el-table-column align="center" label="Cor" min-width="100">
                <template #default="scope">
                    <el-button round disabled :color="scope.row.color" >             </el-button>
                </template>
            </el-table-column>

            <el-table-column align="center" label="Ações" fixed="right" min-width="120">
                <template #default="scope">
                    <el-button round type="primary" size="small" @click="handleEdit(scope.row)"> Editar </el-button>
                    <el-button v-if="scope.row.status == true" round type="warning" size="small" @click="updateStatus(scope.row)"> Desativar </el-button>
                    <el-button v-else-if="scope.row.status == false" round type="primary" size="small" @click="updateStatus(scope.row)"> Ativar </el-button>
                </template>    
            </el-table-column>
        </el-table>

        <el-pagination 
            v-show="total> 0"
            v-model:currentPage="query.page"
            v-model:page-size="query.limit"
            :total="total"
            width="40
            5"
            @size-change="getList"
            @current-change="getList"
        /> 

        <el-dialog
            v-model="dialogFormVisible"
            :title="'Adicionar novo tipo de ausência'"
            :width="$filters.isMobile() ? '95%': '40%'"
        >
            <div class="form-container" v-loading="typeCreate">
                <el-form
                    ref="typesForm"
                    :rules="rules"
                    :model="newType"
                    :label-position="$filters.isMobile() ? 'top':'left'"
                    label-width="150px"
                    style="max-width: 550px"
                >
                    <el-form-item label="Nome" prop="name">
                        <el-input v-model="newType.name" />
                    </el-form-item>

                    <el-form-item label="Cor" prop="color">
                        <el-color-picker v-model="newType.color" size="large"/>
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>
                <el-button round @click="dialogFormVisible = false">
                    {{"Cancelar"}}
                </el-button>
                <el-button :loading="typeLoad" round type="primary" @click="createType()">
                    {{"Confirmar"}}
                </el-button>
            </template>
        </el-dialog>

        <el-dialog
            v-model="dialogFormV"
            :title="'Editar novo tipo de ausência'"
            :width="$filters.isMobile() ? '95%': '40%'"
        >
            <div class="form-container" v-loading="typeEdit">
                <el-form
                    ref="typesForm"
                    :model="newType"
                    
                    :label-position="$filters.isMobile() ? 'top':'left'"
                    label-width="150px"
                    style="max-width: 550px"
                >
                    <el-form-item label="Nome" prop="name">
                        <el-input v-model="newType.name" :disabled="newType.name" />
                    </el-form-item>

                    <el-form-item label="Cor" prop="color">
                        <el-color-picker v-model="newType.color" size="large"/>
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>
                <el-button round @click="dialogFormV = false">
                    {{"Cancelar"}}
                </el-button>
                <el-button :loading="editLoad" round type="primary" @click="editType()">
                    {{"Atualizar"}}
                </el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import TypeResource from '@/api/types'; 
import Resource from '@/api/resource';

import { Plus, Search, Filter, ArrowRight } from "@element-plus/icons-vue";
const typeResource = new TypeResource();
const tipoResource = new Resource('types');

export default {
    name: "Definições",

    components: {
        Plus,
        Search,
        Filter,
        ArrowRight,
    },

    data() {
        return {
            list: [],
            loading: true,
            dialogFormVisible: false,
            dialogFormV: false,
            editLoad: false,
            typeLoad: false,
            newType: {
                id:"",
                name: "",
                color: "",
                has_hours: true,
                status: true,
            },
            query: {
                page: 1,
                limit: 15,
                keyword: "",
                active: "1",
            },
            rules: {
                name: [
                    {
                        required: true, 
                        message: "Por favor insira um nome", 
                        trigger:"blur",
                    },
                ],
            },
        };
    },

    created(){
        this.resetNewType();
        this.getList();
    },

    methods:{

        async getList(){
            const {limit, page} = this.query;
            this.loading=true;
            const {data, meta} = await typeResource.list(this.query);
            this.list = data;
            this.list.forEach((element,index) => {
                element["index"] = (page - 1) * limit + index + 1;
            });
            
            this.total = meta.total;
            this.loading = false;
        },

        handleCreate(){
            this.resetNewType();
            this.dialogFormVisible = true;
            this.$nextTick(() => {
                this.$refs['typesForm'].clearValidate();
            });
        },

        handleEdit(type){
            console.log(type);
            this.resetNewType();
            this.dialogFormV = true;
            this.$nextTick(() =>{
                this.$refs['typesForm'].clearValidate();
            })
            this.newType = type;
        },

        createType(){
            this.typeLoad = true;
            this.$refs["typesForm"].validate((valid)=>{
                if(valid){
                    this.typeCreate
                    typeResource
                        .store(this.newType)
                        .then((response)=>{
                            this.$message({
                                message:
                                    "Tipo de ausência criado com sucesso.",
                                    type: "success",
                                    duration: 5 * 1000,
                            });
                            this.getList();
                            this.resetNewType();
                            this.dialogFormVisible = false;
                            this.typeLoad = false;
                        })
                        .catch((error)=>{
                            console.log(error);
                            this.typeCreate = false;
                            this.typeLoad = false;
                        })
                        .finally(()=>{
                            this.typeCreate = false;
                            this.typeLoad = false;
                        });
                } else {
                    console.log("erro submit!!");
                    this.typeLoad = false;
                    return false;
                }
            });
        },

        resetNewType(){
            this.newType = {
                name: "",
                color:"",
                has_hours: true,
            };
        },

        updateStatus(data){
         
            data.status = !data.status;
            typeResource
                .updateStatus(data)
                .then(()=>{
                    var message = data.status === true ? 'Tipo ausência ativada com Sucesso' : 'Ausência desativada com Sucesso';
                    this.$message({
                        message: message,
                        type: 'success',
                        duration: 5 * 1000,
                    });
                }).catch(error=>{
                    console.log(error);
                });
        },

        editType(){
            this.editLoad = true;
            this.$refs["typesForm"].validate((valid)=>{
                if (valid) {
                    typeResource
                    .update(this.newType.id, this.newType)
                    .then(() => {
                        this. $message({
                            message: 'Informação do tipo atualizada com sucesso',
                            type: 'success',
                            duration: 5 * 1000,
                        });
                        this.dialogFormV = false;
                        this.editLoad = false;
                    })
                    .catch(error => {
                        console.log(error);
                        this.dialogFormV = false;
                        this.editLoad = false;
                    });
                } else {
                    console.log('error');
                    this.editLoad = false;
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

    .selectMargin {
    margin-right: 10px;
    }
</style>