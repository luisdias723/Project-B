<template>
  <div class="form-container">
    <el-form
      ref="userForm"
      :rules="rules"
      :model="user"
      label-position="left"
      label-width="200px"
      style="max-width: 600px;"
    >
      <el-form-item label="Nome" prop="name">
        <el-input v-model="user.name" />
      </el-form-item>

      <el-form-item label="Email" prop="email">
        <el-input v-model="user.email" autocomplete="chrome-off" />
      </el-form-item>

      <el-form-item id="phoneInput" label="Telefone" prop="phone">
        <el-input v-model="user.phone">
          <template #prepend>
            <el-select
              v-model="user.mobile_code"
              placeholder="Indicativo"
              style="width: 115px"
              filterable
              :filter-method="filterMobile"
              @change="resetCountries()"
            >
              <el-option v-for="country in filteredOptions" :key="country.code" :label="country.mobileCode" :value="country.mobileCode">
                <span style="float: left">{{ country.name }}</span>
                <span
                  style="
                      float: right;
                      color: var(--el-text-color-secondary);
                      font-size: 13px; "
                >{{ country.mobileCode }}</span>
              </el-option>
            </el-select>
          </template>
        </el-input>
      </el-form-item>

      <el-form-item label="Password" prop="password">
        <el-input v-model="user.newPassword" show-password autocomplete="new-password" />
      </el-form-item>

      <el-form-item label="Confirmar password" prop="confirmPassword">
        <el-input v-model="user.confirmPassword" show-password autocomplete="new-password" />
      </el-form-item>

      <el-divider content-position="left">
        Outros dados
      </el-divider>

      <!-- <el-form-item label="Doc. de Indentificação" prop="doc_id">
          <el-select v-model="user.doc_id" placeholder="Documento de Indentificação" no-match-text="Sem dados" no-data-text="Sem dados">
            <el-option
                v-for="item in doc_list"
                :key="item.value"
                :label="item.label"
                :value="item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="Nº de Indentificação" prop="id_number">
          <el-input v-model="user.id_number" />
        </el-form-item> -->

      <el-form-item label="Data de nascimento" prop="birthday">
        <!-- <el-date-picker
            v-model="user.birthday"
            type="date"
            placeholder="Escolha um dia"
            format="DD-MM-YYYY"
            value-format="YYYY-MM-DD"
            /> -->
        <el-input v-model="user.birthday" type="date" placeholder="Escolha um dia" />
      </el-form-item>

      <el-form-item label="Morada" prop="address">
        <el-input v-model="user.address" />
      </el-form-item>

      <el-form-item label="Código-postal" prop="zipcode">
        <el-input v-model="user.zipcode" />
      </el-form-item>

      <el-form-item label="Cidade" prop="city">
        <el-input v-model="user.city" />
      </el-form-item>

      <el-form-item label="País" prop="country">
        <el-select v-model="user.country" placeholder="País" filterable style="width: 100%;" autocomplete="new-password">
          <el-option v-for="country in filteredOptions" :key="country.code" :label="country.name" :value="country.name">
            <span style="float: left">{{ country.name }}</span>
            <span
              style="
                  float: right;
                  color: var(--el-text-color-secondary);
                  font-size: 13px;
                "
            > {{ country.code }}</span>
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="Férias Disponíveis" prop="férias">
        <el-input-number v-model="user.num_days" :min="0" max="100" />
      </el-form-item>

      <el-divider content-position="left">
        Dados para faturação
      </el-divider>

      <el-form-item label="Usar estes dados acima para faturação?" prop="use_invoice_data" style="margin-bottom: 40px;">
        <el-checkbox v-model="user.same_invoice_data" maxlength="9" /> Sim
      </el-form-item>

      <el-form-item v-if="user.same_invoice_data" label="Nome" prop="invoice_name">
        <el-input v-model="user.invoice_name" />
      </el-form-item>

      <el-form-item v-if="user.same_invoice_data" label="Morada" prop="invoice_address">
        <el-input v-model="user.invoice_address" type="textarea" rows="3" />
      </el-form-item>

      <el-form-item v-if="user.same_invoice_data" label="IBAN" prop="invoice_iban">
        <el-input v-model="user.invoice_iban" />
      </el-form-item>

      <el-form-item v-if="user.same_invoice_data" label="NIF" prop="invoice_nif">
        <el-input v-model="user.invoice_nif" type="text" maxlength="9" show-word-limit />
      </el-form-item>
    </el-form>
    <div class="user-save">
      <el-button type="primary" round :loading="loading" @click="updateUser()">
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
import { Check } from '@element-plus/icons-vue';

const userResource = new Resource('users');

export default {
  name: 'UserForm',
  components: {
    Check,
  },
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          phone: '',
          role: '',
          password: '',
          vac_days: '',
          id: '',
          num_days: 0,
        };
      },
    },
  },
  data() {
    var validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.user.newPassword && this.user.password) {
        callback(new Error('As palavras-passe não correspondem!'));
      } else {
        callback();
      }
    };
    return {
      vacation_list: [],
      country_list: [],
      filteredOptions: [],
      loading: false,
      rules: {
        name: [{ required: true, message: 'Nome é obrigatório', trigger: 'blur' }],
        email: [{ type: 'email', message: 'Por favor introduza um email correcto', trigger: ['blur'] },
          { required: true, message: 'Por favor introduza um email correcto', trigger: ['blur'] },
        ],
        phone: [{ required: true, message: 'Número de telefone é obrigatório', trigger: 'blur' }],
        // password: [{ required: true, message: 'Password é obrigatório', trigger: 'blur' }],
        confirmPassword: [{ validator: validateConfirmPassword, trigger: 'blur' }],
        birthday: [{ type: 'date', message: 'Data não é válida', trigger: 'blur' }],
        address: [{ max: 250, message: 'Morada demasiado longa max (250 caracteres máx)', trigger: 'blur' }],
        zipcode: [{ max: 50, message: 'Código postal demasiado longo (50 caracteres máx)', trigger: 'blur' }],
        city: [{ max: 50, message: 'Cidade demasiado extensa (50 caracteres máx)', trigger: 'blur' }],
        country: [{ max: 50, message: 'País demasiado extenso (50 caracteres máx)', trigger: 'blur' }],
        iban: [{ max: 250, message: 'IBAN demasiado extenso (250 caracteres máx)', trigger: 'blur' }],
      },
      doc_list: [
        { 'value': '1', 'label': 'Bilhete de Indentidade' },
        { 'value': '2', 'label': 'Cartão de Cidadão' },
        { 'value': '3', 'label': 'Passaporte' },
      ],
    };
  },

  created() {
    this.country_list = this.$store.getters.countries;
    this.filteredOptions = this.country_list;
  },
  methods: {
    updateUser() {
      this.$refs.userForm.validate((valid) => {
        if (valid) {
          this.loading = true;
          userResource
            .update(this.user.id, this.user)
            .then(() => {
              this.$message({
                message: 'Informação de utilizador actualizada com sucesso',
                type: 'success',
                duration: 5 * 1000,
              });
              this.$store.dispatch('user/getInfo');
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
  .user-profile {
    .user-name {
      font-weight: bold;
    }
    .box-center {
      padding-top: 10px;
    }
    .user-role {
      padding-top: 10px;
      font-weight: 400;
      font-size: 14px;
    }
    .box-social {
      padding-top: 30px;
      .el-table {
        border-top: 1px solid #dfe6ec;
      }
    }
    .user-delete {
      padding-top: 20px;
    }
    .el-form {
      margin-left: 10%;
    }
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
