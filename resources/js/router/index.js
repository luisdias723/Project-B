import { createRouter, createWebHashHistory  } from 'vue-router';

// /**
//  * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
//  * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
//  * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
//  */


// /* Layout */
import Layout from '@/Layouts/AppLayout';

// /* Router for modules */
import adminRoutes from './modules/admin';
import errorRoutes from './modules/error';

// /**
//  * Sub-menu only appear when children.length>=1
//  * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
//  **/

// /**
// * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
// * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
// *                                if not set alwaysShow, only more than one route under the children
// *                                it will becomes nested mode, otherwise not show the root menu
// * redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
// * name:'router-name'             the name is used by <keep-alive> (must set!!!)
// * meta : {
//     roles: ['admin', 'editor']   Visible for these roles only
//     permissions: ['view menu zip', 'manage user'] Visible for these permissions only
//     title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
//     icon: 'svg-name'             the icon show in the sidebar
//     noCache: true                if true, the page will no be cached(default is false)
//     breadcrumb: false            if false, the item will hidden in breadcrumb (default is true)
//     affix: true                  if true, the tag will affix in the tags-view
//   }
// **/

export const constantRoutes = [
  // {
  //   path: '/redirect',
  //   component: Layout,
  //   hidden: true,
  //   children: [
  //     {
  //       path: '/redirect/:path*',
  //       component: () => import('@/views/redirect/index'),
  //     },
  //   ],
  // },
  {
    path: '/',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/Pages/dashboard/index.vue'),
        name: 'Dashboard',
        meta: { title: 'Dashboard', icon: 'fa-solid fa-chart-line', noCache: false },
      },
    ],
  },
  {
    path: '/login',
    component: () => import('@/Pages/Auth/Login.vue'),
    hidden: true,
  },
  {
    path: '/recuperar/password',
    component: () => import('@/Pages/Auth/ForgotPassword.vue'),
    hidden: true,
  },
  {
    path: '/password',
    component: () => import('@/Pages/Auth/RecoverPassword.vue'),
    hidden: true,
  },
  {
    path: '/registar',
    component: () => import('@/Pages/Auth/Register.vue'),
    hidden: true,
  },
  {
    path: '/termos',
    component: () => import('@/Pages/Auth/components/Terms.vue'),
    hidden: true,
  },
  {
    path: '/privacidade',
    component: () => import('@/Pages/Auth/components/Privacy.vue'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/Pages/Auth/AuthRedirect'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
  },
];

export const asyncRoutes = [
  ...adminRoutes,
  errorRoutes,

  {
    path: '/checklists',
    component: Layout,
    redirect: '/checklists',
    children: [
      {
        path: '/checklists',
        component: () => import('@/Pages/checklists/index.vue'),
        name: 'Checklists',
        meta: { title: 'checklists', icon: 'fa-solid fa-chart-line', noCache: false },
      },
      {
        path: '/checklists/editar/:id(\\d+)',
        component: () => import('@/Pages/checklists/edit.vue'),
        name: 'Editar checklist',
        meta: { title: 'Editar checklist', icon: 'fa-solid fa-person', noCache: false },
        hidden: true,
      },
    ],
  },
  {
    path: '/tarefas',
    component: Layout,
    redirect: '/tarefas',
    children: [
      {
        path: '/tarefas',
        component: () => import('@/Pages/Tarefas/index.vue'),
        name: 'Lista de Tarefas',
        meta: { title: 'Tarefas', icon: 'fa-solid fa-bars-progress', noCache: false },
      },
      {
        path: '/tarefas/:id(\\d+)',
        component: () => import('@/Pages/Tarefas/tarefa.vue'),
        name: 'Editar Tarefa',
        meta: { title: 'Tarefas', icon: 'fa-solid fa-bars-progress', noCache: false },
        hidden: true,
      },

     
    ],

  },
  {
    path: '/garage',
    component: Layout,
    redirect: '/garage',
    children: [
      {
        path: '/garage',
        component: () => import('@/Pages/Garage/index.vue'),
        name: 'Lista de Viaturas',
        meta: { title: 'Lista de Viaturas', icon: 'fa-solid fa-bars-progress', noCache: false },
      },
      // {
      //   path: '/garage/:id(\\d+)',
      //   component: () => import('@/Pages/Tarefas/tarefa.vue'),
      //   name: 'Editar Tarefa',
      //   meta: { title: 'Tarefas', icon: 'fa-solid fa-bars-progress', noCache: false },
      //   hidden: true,
      // },

     
    ],

  },
  {
    path: '/absences',
    component: Layout,
    redirect: '/absences',
    children: [
      {
        path: '/absences',
        component: () => import('@/Pages/absences/index.vue'),
        name: 'Calendário',
        meta: { title: 'Calendário', icon: 'fa-solid fa-calendar', noCache: false },
      },
     
    ],
  },

  {
    path: '/settings',
    component: Layout,
    redirect: '/settings',
    children: [
      {
        path: '/settings',
        component: () => import('@/Pages/types/index.vue'),
        name: 'Definições',
        meta: { title: 'Definições', icon: 'fa-solid fa-gear', noCache: false},
      },
    ],
  },


 {
    path: '/truckFleet',
    component: Layout,
    redirect: '/truckFleet',
    alwaysShow: true,
    meta: {
      title: 'Frota',
      icon: 'fa-solid fa-truck', 
      roles: ['admin', 'gestor']
    },
  
    children: [

      {
        path: '/truckFleet/:id(\\d+)',
        component: () => import('@/Pages/TruckFleet/TruckFleet/truckFleet.vue'),
        name: 'Editar Camião',
        meta: { title: 'Frota', icon: 'fa-solid fa-truck-fast', noCache: false },
        hidden: true,
      },
      {
        path: '/truckFleet',
        component: () => import('@/Pages/TruckFleet/TruckFleet/index.vue'),
        name: 'Frota de Camiões',
        meta: { title: 'Frota', icon: 'fa-solid fa-truck', noCache: false },
      
      },
      {
        path: '/truckBrand',
        component: () => import('@/Pages/TruckFleet/TruckBrand/index.vue'),
        name: 'Marca de Camião',
        meta: { title: 'Marca', icon: 'fa-solid fa-tag', noCache: false },
      
      },
      {
        path: '/truckBrand/:id(\\d+)',
        component: () => import('@/Pages/TruckFleet/TruckBrand/truckBrand.vue'),
        name: 'Editar Marca',
        meta: { title: 'Marca', icon: 'fa-solid fa-tag', noCache: false },
        hidden: true,
      },
      {
        path: '/truckModel',
        component: () => import('@/Pages/TruckFleet/TruckModel/index.vue'),
        name: 'Modelo de Camião',
        meta: { title: 'Modelo', icon: 'fa-solid fa-truck-front', noCache: false },
      
      },
      {
        path: '/truckModel/:id(\\d+)',
        component: () => import('@/Pages/TruckFleet/TruckModel/truckModel.vue'),
        name: 'Editar Modelo',
        meta: { title: 'Modelo', icon: 'fa-solid fa-truck-front', noCache: false },
        hidden: true,

      },
      {
        path: '/transportType',
        component: () => import('@/Pages/TruckFleet/TransportType/index.vue'),
        name: 'Tipo de Transporte',
        meta: { title: 'Tipo de Transporte', icon: 'fa-solid fa-truck-ramp-box', noCache: false },
      
      },
      {
        path: '/transportType/:id(\\d+)',
        component: () => import('@/Pages/TruckFleet/TransportType/transportType.vue'),
        name: 'Editar Tipo de Transporte',
        meta: { title: 'Tipo de Transporte', icon: 'fa-solid fa-truck-ramp-couch', noCache: false },
        hidden: true,
      },
      
     

     
    ],

  },

  { path: '/:catchAll(.*)', redirect: '/404', hidden: true },
];

const router = createRouter({
  // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
  base: '',
  scrollBehavior: () => ({ y: 0 }),
  history: createWebHashHistory(),
  routes: constantRoutes, // short for `routes: routes`
});



// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter({
    // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
    base: '',
    scrollBehavior: () => ({ y: 0 }),
    history: createWebHashHistory(),
    routes: constantRoutes, // short for `routes: routes`
  });
  router.matcher = newRouter.matcher; // reset router
}

export default router;