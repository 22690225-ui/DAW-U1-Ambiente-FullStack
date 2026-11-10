<script setup>
import { ref, onMounted } from 'vue'
import DataGrid from '../components/DataGrid.vue'

const usuarios = ref([])
const mensaje = ref('')
const error = ref('')
const editando = ref(false)

const columnas = [
  { key: 'id', label: 'ID' },
  { key: 'usuario', label: 'Usuario' },
  { key: 'nombre', label: 'Nombre' },
  { key: 'email', label: 'Correo' },
  { key: 'rol', label: 'Rol' },
  {
    key: 'activo',
    label: 'Estado',
    formatter: valor => Number(valor) === 1 ? 'Activo' : 'Inactivo'
  },
  { key: 'acciones', label: 'Acciones', searchable: false, export: false }
]

const formulario = ref({
  id: null,
  usuario: '',
  nombre: '',
  email: '',
  password: '',
  rol: 'CLIENTE',
  activo: 1
})

async function cargarUsuarios() {
  try {
    const respuesta = await fetch('/api/usuarios.php', {
      credentials: 'same-origin'
    })
    const datos = await respuesta.json()
    if (!respuesta.ok) throw new Error(datos.message || 'Error al cargar usuarios.')
    usuarios.value = datos.usuarios || []
  } catch (e) {
    error.value = e.message
  }
}

function limpiarFormulario() {
  formulario.value = {
    id: null,
    usuario: '',
    nombre: '',
    email: '',
    password: '',
    rol: 'CLIENTE',
    activo: 1
  }
  editando.value = false
}

function seleccionarUsuario(usuario) {
  formulario.value = { ...usuario, password: '' }
  editando.value = true
  mensaje.value = ''
  error.value = ''
}

async function guardarUsuario() {
  mensaje.value = ''
  error.value = ''

  try {
    const datos = { ...formulario.value }
    if (editando.value && !datos.password) delete datos.password

    const respuesta = await fetch('/api/usuarios.php', {
      method: editando.value ? 'PUT' : 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })

    const resultado = await respuesta.json()
    if (!respuesta.ok) throw new Error(resultado.message || 'No se pudo guardar.')

    mensaje.value = editando.value
      ? 'Usuario actualizado correctamente.'
      : 'Usuario registrado correctamente.'

    limpiarFormulario()
    await cargarUsuarios()
  } catch (e) {
    error.value = e.message
  }
}

async function cambiarEstado(usuario) {
  const nuevoEstado = Number(usuario.activo) === 1 ? 0 : 1

  if (!confirm(`¿Deseas ${nuevoEstado ? 'activar' : 'desactivar'} este usuario?`)) return

  try {
    const respuesta = await fetch('/api/usuarios.php', {
      method: 'PATCH',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: usuario.id, activo: nuevoEstado })
    })

    const datos = await respuesta.json()
    if (!respuesta.ok) throw new Error(datos.message || 'No se pudo cambiar el estado.')

    mensaje.value = 'Estado del usuario actualizado.'
    await cargarUsuarios()
  } catch (e) {
    error.value = e.message
  }
}

onMounted(cargarUsuarios)
</script>

<template>
  <main class="admin-container">
    <section class="page-heading">
      <div>
        <p class="eyebrow">PANEL DE CONTROL</p>
        <h1>Administración de usuarios</h1>
        <p>Registra, consulta y administra los accesos del sistema.</p>
      </div>
      <span class="total">{{ usuarios.length }} usuarios</span>
    </section>

    <section class="form-card">
      <h2>{{ editando ? 'Editar usuario' : 'Registrar nuevo usuario' }}</h2>

      <form @submit.prevent="guardarUsuario">
        <div class="form-grid">
          <label>
            Nombre de usuario
            <input v-model.trim="formulario.usuario" required maxlength="50">
          </label>

          <label>
            Nombre completo
            <input v-model.trim="formulario.nombre" required maxlength="120">
          </label>

          <label>
            Correo electrónico
            <input v-model.trim="formulario.email" type="email" required maxlength="150">
          </label>

          <label>
            Contraseña {{ editando ? '(dejar vacía para conservarla)' : '' }}
            <input v-model="formulario.password" type="password" :required="!editando" minlength="6">
          </label>

          <label>
            Rol
            <select v-model="formulario.rol" required>
              <option value="ADMIN_SISTEMA">Administrador del sistema</option>
              <option value="ADMIN_SERVICIOS">Administrador de servicios</option>
              <option value="CLIENTE">Cliente</option>
            </select>
          </label>

          <label>
            Estado
            <select v-model.number="formulario.activo">
              <option :value="1">Activo</option>
              <option :value="0">Inactivo</option>
            </select>
          </label>
        </div>

        <div class="actions">
          <button class="primary" type="submit">
            {{ editando ? 'Guardar cambios' : 'Registrar usuario' }}
          </button>
          <button v-if="editando" class="secondary" type="button" @click="limpiarFormulario">
            Cancelar
          </button>
        </div>
      </form>
    </section>

    <p v-if="mensaje" class="success">{{ mensaje }}</p>
    <p v-if="error" class="error">{{ error }}</p>

    <section class="table-card">
      <h2>Usuarios registrados</h2>

      <DataGrid
        :rows="usuarios"
        :columns="columnas"
        title="Usuarios registrados"
        file-name="usuarios"
      >
        <template #acciones="{ row }">
          <div class="row-actions">
            <button class="edit" @click="seleccionarUsuario(row)">Editar</button>
            <button class="toggle" @click="cambiarEstado(row)">
              {{ Number(row.activo) === 1 ? 'Desactivar' : 'Activar' }}
            </button>
          </div>
        </template>
      </DataGrid>
    </section>
  </main>
</template>

<style scoped>
.admin-container {
  max-width: 1200px;
  margin: 35px auto;
  padding: 0 22px 50px;
  color: #292748;
}
.page-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}
.eyebrow {
  color: #6654d9;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 2px;
}
h1 { margin: 8px 0; font-size: 30px; }
.page-heading p { color: #77778c; }
.total {
  padding: 10px 16px;
  background: #eeeaff;
  color: #5e4dce;
  border-radius: 20px;
  font-weight: 700;
}
.form-card, .table-card {
  padding: 25px;
  margin-bottom: 25px;
  border: 1px solid #e9e8f3;
  border-radius: 15px;
  background: white;
  box-shadow: 0 5px 20px #2927480a;
}
h2 { margin: 0 0 20px; font-size: 19px; }
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 17px;
}
label {
  display: flex;
  flex-direction: column;
  gap: 7px;
  color: #55556d;
  font-size: 13px;
  font-weight: 700;
}
input, select {
  width: 100%;
  box-sizing: border-box;
  padding: 11px;
  border: 1px solid #deddea;
  border-radius: 8px;
  font: inherit;
  color: #292748;
}
.actions { display: flex; gap: 10px; margin-top: 20px; }
button {
  padding: 10px 14px;
  border: 0;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
}
.primary { background: #6654d9; color: white; }
.secondary { background: #eeeef4; color: #55556d; }
.success { color: #16834a; }
.error { color: #c0392b; }
.active, .inactive { padding: 5px 9px; border-radius: 15px; font-size: 12px; }
.row-actions { white-space: nowrap; }
.edit { background: #eeeaff; color: #5e4dce; margin-right: 5px; }
.toggle { background: #f3f3f6; color: #55556d; }
@media(max-width:650px) {
  .page-heading { align-items: flex-start; flex-direction: column; }
  .form-grid { grid-template-columns: 1fr; }
  .form-card, .table-card { padding: 16px; }
}
</style>
