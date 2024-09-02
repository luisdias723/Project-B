/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/Layouts/AppLayout';

const adminRoutes = [
  {
    path: '/utilizadores',
    component: Layout,
    redirect: '/utilizadores',
    name: 'Utilizadores',
    alwaysShow: true,
    meta: {
      title: 'Utilizadores',
      icon: 'fa-solid fa-user',
      roles: ['admin', 'gestor']
    },
    children: [
      {
        path: '/administrador/utilizadores/perfil/:id(\\d+)',
        component: () => import('@/Pages/users/profile'),
        name: 'Perfil',
        meta: { title: 'Perfil', icon: 'fa-solid fa-person', noCache: false, roles: ['admin', 'gestor'] },
        hidden: true,
      },
      {
        path: 'utilizadores',
        component: () => import('@/Pages/users/list'),
        name: 'Utilizadores',
        meta: { title: 'Utilizadores', icon: 'fa-solid fa-user',  roles: ['admin'] },
      },
    ]
  },
  // {
  //   path: '/administrador',
  //   component: Layout,
  //   redirect: '/administrador/users',
  //   name: 'Sistema',
  //   alwaysShow: true,
  //   meta: {
  //     title: 'System',
  //     icon: 'fa-solid fa-gear',
  //     roles: ['admin', 'gestor']
  //   },
  //   children: [
      /** User managements */
      // {
      //   path: 'users/edit/:id(\\d+)',
      //   component: () => import('@/Pages/Profile/Show'),
      //   name: 'UserProfile',
      //   meta: { title: 'userProfile', noCache: true, permissions: ['manage user'] },
      //   hidden: true,
      // },
      // {
      //   path: 'perfil',
      //   component: () => import('@/views/users/SelfProfile'),
      //   name: 'Perfil',
      //   meta: { title: 'Perfil', icon: 'fa-solid fa-user', noCache: true },
      // },
      // /** Role and permission */
      // {
      //   path: 'roles',
      //   component: () => import('@/views/role-permission/List'),
      //   name: 'RoleList',
      //   meta: { title: 'rolePermission', icon: 'fa-solid fa-briefcase', permissions: ['manage permission'] },
      // },
      // {
      //   path: 'settings',
      //   component: () => import('@/views/admin-settings/index'),
      //   name: 'Definições',
      //   meta: { title: 'Definições', icon: 'fa-solid fa-screwdriver-wrench', permissions: ['manage permission'] },
      // },
    // ],
  // }
];

export default adminRoutes;
