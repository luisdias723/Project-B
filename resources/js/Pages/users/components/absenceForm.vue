<template>
    <div class="form-container">
        <el-row>
            <el-col :span="8"/>
            <el-col :span="8"/> 
            <el-col :span="8">
                <div class="block">
                    <span class="date_filter"/>
                    <el-date-picker v-model="query.date_range" type="daterange" value-format="YYYY/MM/DD" unlink-panels range-separator="Até" start-placeholder="Data Inicio" end-placeholder="Data Fim" :shortcuts="shortcuts" @change="getList"/>    
                </div>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">    
                <el-form label-position="left" label-width="200px" style="max-width: 100%">
                    
                    <h2>Férias</h2>
                    <el-table :data="vacationList">
                        <el-table-column prop="start_date"  label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/> 
                    </el-table>
                    
                    <h2>Baixa</h2>
                    <el-table :data="sickLeaveList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Falta Justificada</h2>    
                    <el-table :data="unjustifiedAbsenceList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Falta Injustificada</h2>
                    <el-table :data="justifiedAbsenceList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Licenças</h2>
                    <el-table :data="licenseAbsenceList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Acidentes</h2>
                    <el-table :data="accidentList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Compensação Feriado</h2>
                    <el-table :data="holidayCompList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                    
                    <h2>Luto</h2>
                    <el-table :data="mourningList">
                        <el-table-column prop="start_date" label="Data de Início"/>
                        <el-table-column prop="end_date" label="Data de Fim"/>
                        <el-table-column prop="days" label="Dias"/>  
                    </el-table>
                </el-form>
            </el-col>
        </el-row> 
    </div>
</template>

<script>

    import Resource from '@/api/resource';

    const absenceResource = new Resource('absences');
    
    export default {
        
        name: "AbsenceForm",
        
        props:{
            userId:{
                type: Number,
                default: null,
            },
        },

        data(){
            return{
                vacationList: [],
                sickLeaveList: [],
                justifiedAbsenceList: [],
                unjustifiedAbsenceList: [],
                licenseAbsenceList: [],
                accidentList: [],
                holidayCompList: [],
                mourningList: [],
                daysList:[],
                                
                loading: true,
                list: [],
                total: 0,
                days: '',
                query:{
                    user_id: null,
                    id: null,
                    date_range: [],
                },
            
                shortcuts : [
                    {
                        text: 'Semana Passada',
                        value: () =>{
                            const end = new Date()
                            const start = new Date()
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                            return [start, end]
                        },
                    },
                    {
                        text: 'Mês Passado',
                        value: () =>{
                            const end = new Date()
                            const start = new Date()
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
                            return [start, end]
                        },
                    },
                    {
                        text: 'Ultimos 3 meses',
                        value: () =>{
                            const end = new Date()
                            const start = new Date()
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
                            return [start, end]
                        },
                    },
                ]
            }
        },

        created(){
            
            this.query.user_id=this.userId;

            this.getList();
        },
        methods:{

            async getList() {
                const { limit, page } = this.query;
                this.loading = true;
                const { ferias, baixas, faltasJ, faltasI, licensas, acidentes, conpensacaoF, luto, days } = await absenceResource.list(this.query);
                this.vacationList = ferias;
                this.sickLeaveList = baixas;
                this.justifiedAbsenceList = faltasJ;
                this.unjustifiedAbsenceList = faltasI;
                this.licenseAbsenceList = licensas;
                this.accidentList = acidentes;
                this.holidayCompList = conpensacaoF;
                this.mourningList = luto;
                this.daysList = days;
                this.loading = false;
            },
            
        }
    }
</script>

<style scoped> 
.demo-date-picker {
  display: flex;
  width: 100%;
  padding: 0;
  flex-wrap: wrap;
}

.demo-date-picker .block {
  padding: 30px 0;
  text-align: center;
  border-right: solid 1px var(--el-border-color);
  flex: 1;
}

.demo-date-picker .block:last-child {
  border-right: none;
}

.demo-date-picker .demonstration {
  display: block;
  color: var(--el-text-color-secondary);
  font-size: 14px;
  margin-bottom: 20px;
}

</style>
