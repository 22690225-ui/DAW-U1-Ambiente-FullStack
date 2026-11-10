<script setup>
defineProps({
  servicio: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['seleccionar'])
</script>

<template>
  <article class="service-card">
    <h2>{{ servicio.nombre }}</h2>
    <p>{{ servicio.descripcion }}</p>

    <div class="service-category">
      <p><strong>Precio:</strong> ${{ servicio.precio }}</p>
      <p><strong>Duración:</strong> {{ servicio.duracion_minutos }} minutos</p>
      <p><strong>Estado:</strong> {{ Number(servicio.activo) === 1 ? 'Activo' : 'Inactivo' }}</p>
    </div>

    <button
      type="button"
      @click="emit('seleccionar', servicio)"
    >
      Ver información
    </button>
  </article>
</template>