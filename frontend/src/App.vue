
<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'

const route = useRoute()
const estado = ref(null)
const error = ref(null)

const esLogin = computed(() => route.name === 'login')

onMounted(async () => {
  try {
    const respuesta = await fetch('/api/health.php')

    if (!respuesta.ok) {
      throw new Error('Error al comunicarse con el backend.')
    }

    estado.value = await respuesta.json()
  } catch (e) {
    error.value = e.message
  }
})
</script>

<template>
  <AppHeader v-if="!esLogin" />

  <main>
    <RouterView />

    <section
      v-if="!esLogin"
      class="estado-sistema"
      aria-live="polite"
    >
      <h2>Estado del sistema</h2>

      <div v-if="estado">
        <p>✓ {{ estado.backend }}</p>
        <p>✓ {{ estado.database }}</p>
      </div>

      <p v-else-if="error" class="error-sistema">
        {{ error }}
      </p>

      <p v-else>Conectando con el backend...</p>
    </section>
  </main>
</template>

<style scoped>
.estado-sistema {
  width: min(1120px, calc(100% - 48px));
  margin: 0 auto 30px;
  padding: 18px 22px;
  border: 1px solid #e7e7f2;
  border-radius: 14px;
  background: #fff;
  color: #4c4d68;
}

.estado-sistema h2 {
  margin: 0 0 10px;
  color: #30314e;
  font-size: 16px;
}

.estado-sistema p {
  margin: 6px 0;
  font-size: 14px;
}

.error-sistema {
  color: #b42338;
}

@media (max-width: 480px) {
  .estado-sistema {
    width: calc(100% - 32px);
    box-sizing: border-box;
  }
}
</style>
