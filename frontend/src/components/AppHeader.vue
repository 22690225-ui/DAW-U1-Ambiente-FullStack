<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { cerrarSesion, consultarSesion } from '../services/auth'

const router = useRouter()
const rol = ref('')

async function cargarRol() {
  try {
    const sesion = await consultarSesion()
    rol.value = sesion.user?.rol || ''
  } catch {
    rol.value = ''
  }
}

onMounted(cargarRol)
watch(() => router.currentRoute.value.fullPath, cargarRol)

async function salir() {
  try {
    await cerrarSesion()
  } catch (error) {
    console.error('No fue posible cerrar la sesión:', error)
  } finally {
    await router.push('/login')
  }
}
</script>

<template>
  <header class="app-header">
    <RouterLink to="/dashboard" class="brand">
      <span class="brand-icon" aria-hidden="true">◆</span>
      <span>Servicios+</span>
    </RouterLink>

    <nav class="navigation" aria-label="Navegación principal">
      <RouterLink to="/dashboard">Inicio</RouterLink>
      <RouterLink to="/servicios">Servicios</RouterLink>

      <RouterLink
        v-if="['ADMIN_SISTEMA', 'ADMIN_SERVICIOS'].includes(rol)"
        to="/servicios/admin"
      >
        Administrar servicios
      </RouterLink>

      <RouterLink
        v-if="rol === 'ADMIN_SISTEMA'"
        to="/usuarios"
      >
        Usuarios
      </RouterLink>

      <button type="button" class="logout-button" @click="salir">
        Cerrar sesión
        <span aria-hidden="true">↗</span>
      </button>
    </nav>
  </header>
</template>

<style scoped>
.app-header {
  display: flex;
  min-height: 76px;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: 0 6%;
  border-bottom: 1px solid #e9e8f3;
  background: #fff;
}
.brand {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: #292748;
  font-size: 19px;
  font-weight: 850;
  text-decoration: none;
  white-space: nowrap;
}
.brand-icon {
  display: grid;
  width: 37px;
  height: 37px;
  place-items: center;
  border-radius: 11px;
  background: #eeeaff;
  color: #6654d9;
  font-size: 20px;
}
.navigation {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.navigation a {
  padding: 10px 13px;
  border-radius: 9px;
  color: #64657d;
  font-size: 14px;
  font-weight: 650;
  text-decoration: none;
}
.navigation a:hover,
.navigation a.router-link-active {
  background: #f0edff;
  color: #5e4dce;
}
.logout-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border: 1px solid #e2def8;
  border-radius: 10px;
  background: #fff;
  color: #5e4dce;
  cursor: pointer;
  font: inherit;
  font-size: 13px;
  font-weight: 750;
}
.logout-button:hover {
  background: #f5f3ff;
}
@media (max-width: 600px) {
  .app-header {
    align-items: flex-start;
    flex-direction: column;
    gap: 12px;
    padding: 16px 18px;
  }
  .navigation {
    width: 100%;
    gap: 5px;
  }
  .navigation a {
    padding: 9px;
  }
  .logout-button {
    margin-left: auto;
  }
}
</style>