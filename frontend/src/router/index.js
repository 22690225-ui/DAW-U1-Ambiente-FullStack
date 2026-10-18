
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ServicesView from '../views/ServicesView.vue'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import { consultarSesion } from '../services/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiereAutenticacion: true },
  },
  {
    path: '/servicios',
    name: 'services',
    component: ServicesView,
    meta: { requiereAutenticacion: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  let autenticado = false

  try {
    const sesion = await consultarSesion()
    autenticado = sesion.authenticated === true
  } catch {
    autenticado = false
  }

  if (to.meta.requiereAutenticacion && !autenticado) {
    return { name: 'login' }
  }

  if (to.name === 'login' && autenticado) {
    return { name: 'dashboard' }
  }

  if (to.name === 'home') {
    return autenticado
      ? { name: 'dashboard' }
      : { name: 'login' }
  }
})

export default router
