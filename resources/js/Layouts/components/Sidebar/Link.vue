<template>
  <!-- eslint-disable vue/require-component-is-->
  <router-link :to="to" @click="toggleSideBar">
    <slot />
  </router-link>
</template>

<script>
import { isExternal } from '@/utils/validate';

export default {
  name: 'SidebarLink',
  props: {
    to: {
      type: String,
      required: true,
    },
  },
  methods: {
    isExternalLink(routePath) {
      return isExternal(routePath);
    },
    linkProps(url) {
      if (this.isExternalLink(url)) {
        return {
          is: 'a',
          href: url,
          target: '_blank',
          rel: 'noopener',
        };
      }
      return {
        is: 'router-link',
        to: url,
      };
    },
    toggleSideBar() {
      if(this.$filters.isMobile()){
        this.$store.dispatch('app/toggleSideBar');
      }
        
    }
  },
};
</script>
