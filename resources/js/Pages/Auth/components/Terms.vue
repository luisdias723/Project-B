<template>
  <div class="login-container">
    <el-row style="background: #EEF1F4; height: 100vh;">
      <el-col :md="12" style="margin: auto;">
        <el-card>
          <h1>Termos e condições de serviço</h1>
          <!-- <el-scrollbar height="500px">
            <p>A sua privacidade &eacute; importante para n&oacute;s. &Eacute; pol&iacute;tica do Susana Torres &ndash; Coache de Alta Performance respeitar a sua privacidade em rela&ccedil;&atilde;o a qualquer informa&ccedil;&atilde;o sua que possamos coletar no site Susana Torres &ndash; Coache de Alta Performance, e outros sites que possu&iacute;mos e operamos.</p>
            <p>Solicitamos informa&ccedil;&otilde;es pessoais apenas quando realmente precisamos delas para lhe fornecer um servi&ccedil;o. Fazemo-lo por meios justos e legais, com o seu conhecimento e consentimento. Tamb&eacute;m informamos por que estamos coletando e como ser&aacute; usado.</p>
            <p>Apenas retemos as informa&ccedil;&otilde;es coletadas pelo tempo necess&aacute;rio para fornecer o servi&ccedil;o solicitado. Quando armazenamos dados, protegemos dentro de meios comercialmente aceit&aacute;veis ​​para evitar perdas e roubos, bem como acesso, divulga&ccedil;&atilde;o, c&oacute;pia, uso ou modifica&ccedil;&atilde;o n&atilde;o autorizados.</p>
            <p>N&atilde;o compartilhamos informa&ccedil;&otilde;es de identifica&ccedil;&atilde;o pessoal publicamente ou com terceiros, exceto quando exigido por lei.</p>
            <p>O nosso site pode ter links para sites externos que n&atilde;o s&atilde;o operados por n&oacute;s. Esteja ciente de que n&atilde;o temos controle sobre o conte&uacute;do e pr&aacute;ticas desses sites e n&atilde;o podemos aceitar responsabilidade por suas respectivas pol&iacute;ticas de privacidade.</p>
            <p>Voc&ecirc; &eacute; livre para recusar a nossa solicita&ccedil;&atilde;o de informa&ccedil;&otilde;es pessoais, entendendo que talvez n&atilde;o possamos fornecer alguns dos servi&ccedil;os desejados.</p>
            <p>O uso continuado de nosso site ser&aacute; considerado como aceita&ccedil;&atilde;o de nossas pr&aacute;ticas em torno de privacidade e informa&ccedil;&otilde;es pessoais. Se voc&ecirc; tiver alguma d&uacute;vida sobre como lidamos com dados do usu&aacute;rio e informa&ccedil;&otilde;es pessoais, entre em contacto connosco.</p>
            <p>Esta pol&iacute;tica &eacute; efetiva a partir de&nbsp;<strong>Fevereiro</strong>/<strong>2022</strong>.</p>
            <div id="gtx-trans" style="position: absolute; left: 30px; top: 38px;">
            <div class="gtx-trans-icon">&nbsp;</div>
          </div>
          </el-scrollbar> -->
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>

export default {
  name: 'TermsConditions',
  data() {
    return  {
      content : 'A carregar',
    };
  },
  created(){
    // this.getSettings();
  },
  methods: {
    async getSettings(){
      await this.$store.dispatch('settings/getInfo');
      this.content = this.$store.getters.getSettingByName('terms_and_conditions');
    },
  }
};
</script>
