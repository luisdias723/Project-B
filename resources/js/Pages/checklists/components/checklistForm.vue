<template>
  <div class="form-container">
    <el-form
      ref="ChecklistForm"
      :rules="rules"
      :model="checklist"
      label-position="center"
      label-width="200px"
      style="max-width: 600px;"
    >
    
      <el-form-item label="Nome" prop="name">
        <el-input v-model="checklist.name" />
      </el-form-item>
      <div class="questions-form"
        v-for="(question) in questions">
        <el-form-item>
          <el-col :span="11">
            <div class="question-input">
              <el-input 
                v-model="question.title"
                placeholder="Pergunta sem título"
                style="width: 100%"
              />
            </div>
          </el-col>

          <el-col :span="12">
            <el-select
              placeholder="Selecione tipo de pergunta"
              v-model="question.type"
            />
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />

          </el-col>
        </el-form-item>
      </div>
  </el-form>
  <el-button class="filter-item" type="primary" @click="addNewQuestionForm()">
      <el-icon class="el-icon--left">
        <Plus />
      </el-icon> Adicionar questão
    </el-button>

    <div class="checklist-save">
      <el-button type="primary" round :loading="loading" @click="updateChecklist()">
        <el-icon class="el-icon--left">
          <Check />
        </el-icon>
        Atualizar dados
      </el-button>
    </div>
  </div>
</template>

<script>
import Resource from '@/api/resource';
import { Plus, Check } from '@element-plus/icons-vue';

const checklistResource = new Resource('checklists');

export default {
  name: 'ChecklistForm',
  components: {
    Check,
    Plus,
  },
  props: {
    checklist: {
      type: Object,
      default: () => {
        return {
          name: '',
        };
      },
    },
  },
  data() {
    

    return {
      questions: [
      {
        title: '',
        type: '',
      },
    ],

      filteredOptions: [],
      loading: false,
      rules: {
        name: [{ required: true, message: 'Nome é obrigatório', trigger: 'blur' }],
      },

    };
  },

  methods: {
    updateChecklist() {
      this.$refs.checklistForm.validate((valid) => {
        if (valid) {
          this.loading = true;
          checklistResource
            .update(this.checklist.id, this.checklist)
            .then(() => {
              this.$message({
                message: 'Informação de checklist actualizada com sucesso',
                type: 'success',
                duration: 5 * 1000,
              });
              this.$store.dispatch('checklist/getInfo');
              this.loading = false;
            })
            .catch(error => {
              console.log(error);
              this.loading = false;
            });
        } else {
          console.log('error');
          return false;
        }
      });
    },

    addNewQuestionForm(){
      this.questions.push({
        title: '',
        tipo: '',
      })
    },

    filterMobile(value){
      this.filteredOptions = this.country_list.filter((item) => {
        return (item.name.toLowerCase().includes(value.toLowerCase()) || item.code.includes(value));
      });
    },
    resetCountries(){
      this.filteredOptions = this.country_list;
    }
  },
};
</script>

<style lang="scss">
  .questions-form {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
  }

  .question-input {
    width: 100%;
  }

  #phoneInput .el-input-group__prepend{
    border-radius: 20px 0px 0px 20px !important;
    box-shadow: none;
  }

  #phoneInput > div.el-form-item__content > div > div.el-input__wrapper {
    border-radius: 0px 20px 20px 0px !important;
  }

  #phoneInput > div.el-form-item__content > div > div.el-input-group__prepend > div > div > div > div {
    border-top-right-radius: 0px !important;
    border-bottom-right-radius: 0px !important;
  }
</style>
