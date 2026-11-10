<script setup>
import { ref, onMounted } from 'vue'
import DataGrid from '../components/DataGrid.vue'

const servicios = ref([])
const cargando = ref(false)
const mensaje = ref('')
const error = ref('')
const editando = ref(false)

const columnas = [
  { key: 'id', label: 'ID' },
  { key: 'nombre', label: 'Nombre' },
  {
    key: 'precio',
    label: 'Precio',
    formatter: valor => `$${Number(valor).toFixed(2)}`
  },
  {
    key: 'duracion_minutos',
    label: 'Duración',
    formatter: valor => `${valor} min`
  },
  {
    key: 'activo',
    label: 'Estado',
    formatter: valor => Number(valor) === 1 ? 'Activo' : 'Inactivo'
  },
  { key: 'acciones', label: 'Acciones', searchable: false, export: false }
]

const formulario = ref({
  id: null,
  nombre: '',
  descripcion: '',
  precio: '',
  duracion_minutos: '',
  activo: 1
})

async function cargarServicios() {
  cargando.value = true
  try {
    const respuesta = await fetch('/api/servicios.php', {
      credentials: 'same-origin'
    })
    const datos = await respuesta.json()
    if (!respuesta.ok) throw new Error(datos.message || 'Error al cargar servicios.')
    servicios.value = datos.servicios || []
  } catch (e) {
    error.value = e.message || 'No se pudieron cargar los servicios.'
  } finally {
    cargando.value = false
  }
}

function limpiarFormulario() {
  formulario.value = {
    id: null,
    nombre: '',
    descripcion: '',
    precio: '',
    duracion_minutos: '',
    activo: 1
  }
  editando.value = false
}

function seleccionarServicio(servicio) {
  formulario.value = { ...servicio }
  editando.value = true
  mensaje.value = ''
  error.value = ''
}

async function guardarServicio() {
  mensaje.value = ''
  error.value = ''

  try {
    const datos = { ...formulario.value }
    const metodo = editando.value ? 'PUT' : 'POST'

    const respuesta = await fetch('/api/servicios.php', {
      method: metodo,
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })

    const resultado = await respuesta.json()
    if (!respuesta.ok) throw new Error(resultado.message || 'Error al guardar.')

    mensaje.value = editando.value
      ? 'Servicio actualizado correctamente.'
      : 'Servicio registrado correctamente.'

    limpiarFormulario()
    await cargarServicios()
  } catch (e) {
    error.value = e.message
  }
}

async function cambiarEstado(servicio) {
  const nuevoEstado = Number(servicio.activo) === 1 ? 0 : 1

  if (!confirm(`¿Deseas ${nuevoEstado ? 'activar' : 'desactivar'} este servicio?`)) return

  try {
    const respuesta = await fetch('/api/servicios.php', {
      method: 'PATCH',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: servicio.id, activo: nuevoEstado })
    })

    const datos = await respuesta.json()
    if (!respuesta.ok) throw new Error(datos.message || 'No se pudo cambiar el estado.')

    mensaje.value = 'Estado del servicio actualizado.'
    await cargarServicios()
  } catch (e) {
    error.value = e.message
  }
}

onMounted(cargarServicios)
</script>

<template>
  <main class="admin-container">
    <section class="page-heading">
      <div>
        <p class="eyebrow">PANEL DE CONTROL</p>
        <h1>Administración de servicios</h1>
        <p>Registra, modifica y controla los servicios disponibles.</p>
      </div>
      <span class="total">{{ servicios.length }} servicios</span>
    </section>

    <section class="form-card">
      <h2>{{ editando ? 'Editar servicio' : 'Registrar nuevo servicio' }}</h2>

      <form @submit.prevent="guardarServicio">
        <div class="form-grid">
          <label>
            Nombre
            <input v-model="formulario.nombre" required maxlength="120">
          </label>

          <label>
            Precio
            <input v-model="formulario.precio" type="number" min="0" step="0.01" required>
          </label>

          <label class="full">
            Descripción
            <textarea v-model="formulario.descripcion" required></textarea>
          </label>

          <label>
            Duración (minutos)
            <input v-model="formulario.duracion_minutos" type="number" min="1" required>
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
            {{ editando ? 'Guardar cambios' : 'Registrar servicio' }}
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
      <h2>Servicios registrados</h2>
      <p v-if="cargando">Cargando servicios...</p>

      <DataGrid
        v-else
        :rows="servicios"
        :columns="columnas"
        title="Servicios registrados"
        file-name="servicios"
      >
        <template #acciones="{ row }">
          <div class="row-actions">
            <button class="edit" @click="seleccionarServicio(row)">Editar</button>
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
  max-width: 1150px;
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
.full { grid-column: 1 / -1; }
input, textarea, select {
  width: 100%;
  box-sizing: border-box;
  padding: 11px;
  border: 1px solid #deddea;
  border-radius: 8px;
  font: inherit;
  color: #292748;
}
textarea { min-height: 75px; resize: vertical; }
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
.row-actions { white-space: nowrap; }
.edit { background: #eeeaff; color: #5e4dce; margin-right: 5px; }
.toggle { background: #f3f3f6; color: #55556d; }
@media(max-width:650px) {
  .page-heading { align-items: flex-start; flex-direction: column; }
  .form-grid { grid-template-columns: 1fr; }
  .full { grid-column: auto; }
  .form-card, .table-card { padding: 16px; }
}
</style>
