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

    <span class="service-category">
      {{ servicio.categoria }}
    </span>

    <button
      type="button"
      @click="emit('seleccionar', servicio)"
    >
      Ver información
    </button>
  </article>
</template>
