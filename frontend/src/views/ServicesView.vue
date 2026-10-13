<script setup>
import { ref, computed, onMounted } from 'vue'
import ServiceCard from '../components/ServiceCard.vue'

const servicios = ref([])
const busqueda = ref('')
const cargando = ref(true)
const error = ref('')
const servicioSeleccionado = ref(null)

const serviciosFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  return servicios.value.filter((servicio) =>
    servicio.nombre.toLowerCase().includes(texto)
  )
})

function seleccionarServicio(servicio) {
  servicioSeleccionado.value = servicio
}

onMounted(async () => {
  try {
    const respuesta = await fetch('/api/servicios.php')

    if (!respuesta.ok) {
      throw new Error('No se pudieron cargar los servicios')
    }

    servicios.value = await respuesta.json()
  } catch (e) {
    console.error('Error al cargar los servicios:', e)
    error.value = 'No fue posible cargar los servicios.'
  } finally {
    cargando.value = false
  }
})
</script>

<template>
  <main class="services">
    <h1>Nuestros servicios</h1>
    <p>Consulta los servicios disponibles en nuestra plataforma.</p>

    <label for="busqueda-servicios">Buscar servicio</label>
    <input
      id="busqueda-servicios"
      v-model="busqueda"
      type="search"
      placeholder="Escribe el nombre de un servicio..."
    />

    <p v-if="cargando">Cargando servicios...</p>
    <p v-else-if="error">{{ error }}</p>

    <section
      v-else-if="serviciosFiltrados.length > 0"
      class="services-grid"
    >
      <ServiceCard
        v-for="servicio in serviciosFiltrados"
        :key="servicio.id"
        :servicio="servicio"
        @seleccionar="seleccionarServicio"
      />
    </section>

    <p v-else>No existen servicios que coincidan con la búsqueda.</p>

    <section
      v-if="servicioSeleccionado"
      class="estado-sistema"
      aria-live="polite"
    >
      <h2>Información del servicio seleccionado</h2>
      <p><strong>Nombre:</strong> {{ servicioSeleccionado.nombre }}</p>
      <p><strong>Descripción:</strong> {{ servicioSeleccionado.descripcion }}</p>
      <p><strong>Categoría:</strong> {{ servicioSeleccionado.categoria }}</p>
      <button
        type="button"
        @click="servicioSeleccionado = null"
      >
        Cerrar información
      </button>
    </section>
  </main>
</template>
