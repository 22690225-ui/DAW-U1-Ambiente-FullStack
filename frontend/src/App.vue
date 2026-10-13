

<script setup>
import { ref, onMounted } from 'vue'
import AppHeader from './components/AppHeader.vue'

const estado = ref(null)
const error = ref(null)

onMounted(async () => {
  try {
    const respuesta = await fetch('/api/health.php')

    if (!respuesta.ok) {
      throw new Error('Error al comunicarse con el backend')
    }

    estado.value = await respuesta.json()
  } catch (e) {
    error.value = e.message
  }
})
</script>

<template>
  <AppHeader />

  <main>
    <RouterView />

    <section class="estado-sistema">
      <h2>Estado del sistema</h2>

      <div v-if="estado">
        <p>✅ {{ estado.backend }}</p>
        <p>✅ {{ estado.database }}</p>
      </div>

      <p v-else-if="error">❌ {{ error }}</p>
      <p v-else>Conectando con el backend...</p>
    </section>
  </main>
</template>


