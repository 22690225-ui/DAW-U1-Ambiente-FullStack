import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ServicesView from '../views/ServicesView.vue'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import { consultarSesion } from '../services/auth'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/login', name: 'login', component: LoginView },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { roles: ['ADMIN_SISTEMA', 'ADMIN_SERVICIOS', 'CLIENTE'] }
  },
  {
    path: '/servicios',
    name: 'services',
    component: ServicesView,
    meta: { roles: ['ADMIN_SISTEMA', 'ADMIN_SERVICIOS', 'CLIENTE'] }
  },
  {
    path: '/servicios/admin',
    name: 'admin-services',
    component: () => import('../views/AdminServicesView.vue'),
    meta: { roles: ['ADMIN_SISTEMA', 'ADMIN_SERVICIOS'] }
  },
  {
    path: '/usuarios',
    name: 'users',
    component: () => import('../views/UsersView.vue'),
    meta: { roles: ['ADMIN_SISTEMA'] }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  let usuario = null

  try {
    const sesion = await consultarSesion()
    if (sesion.authenticated) usuario = sesion.user
  } catch {
    usuario = null
  }

  if (to.name === 'login') {
    return usuario ? { name: 'dashboard' } : true
  }

  if (to.name === 'home') {
    return usuario ? { name: 'dashboard' } : { name: 'login' }
  }

  if (!usuario) return { name: 'login' }

  if (to.meta.roles && !to.meta.roles.includes(usuario.rol)) {
    return { name: 'dashboard' }
  }

  return true
})

export default router