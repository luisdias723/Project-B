import { createI18n } from 'vue-i18n';
import Cookies from 'js-cookie';
import enLocale from './en.js';

const messages = {
  en: {
    enLocale,
  },
};


 export function getLanguage() {
   const chooseLanguage = Cookies.get('language');
  if (chooseLanguage) {
    return chooseLanguage;
  }

  // if has not choose language
  const language = (navigator.language || navigator.browserLanguage).toLowerCase();
  const locales = Object.keys(messages);
  for (const locale of locales) {
    if (language.indexOf(locale) > -1) {
      return locale;
    }
  }
  return 'en';
}

const i18n = new createI18n({
  // set locale
  // options: ptST | ptBC
  options: 'en',
  locale: 'en',
  // set locale messages
  messages: messages,
});

export default i18n;
