
<script setup>
import { ref } from 'vue'

const emit = defineEmits(['login'])

const usuario = ref('')
const contrasena = ref('')
const error = ref('')

function enviarFormulario() {
  error.value = ''

  if (!usuario.value.trim() || !contrasena.value) {
    error.value = 'Completa los dos campos para continuar.'
    return
  }

  emit('login', {
    username: usuario.value.trim(),
    password: contrasena.value,
  })
}
</script>

<template>
  <section class="login-card">
    <div class="login-icon" aria-hidden="true">🔐</div>

    <p class="login-eyebrow">PORTAL DE SERVICIOS</p>
    <h1>¡Qué gusto verte!</h1>
    <p class="login-description">
      Inicia sesión para acceder a tu espacio de trabajo.
    </p>

    <form @submit.prevent="enviarFormulario">
      <div class="form-group">
        <label for="usuario">Nombre de usuario</label>
        <input
          id="usuario"
          v-model="usuario"
          type="text"
          autocomplete="username"
          placeholder="Ingresa tu usuario"
          required
        />
      </div>

      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input
          id="contrasena"
          v-model="contrasena"
          type="password"
          autocomplete="current-password"
          placeholder="Ingresa tu contraseña"
          required
        />
      </div>

      <p v-if="error" class="login-error" role="alert">
        {{ error }}
      </p>

      <button type="submit" class="login-button">
        Iniciar sesión
        <span aria-hidden="true">→</span>
      </button>
    </form>

    <p class="login-footer">
      Acceso al sistema de gestión de servicios
    </p>
  </section>
</template>

<style scoped>
.login-card {
  width: min(100%, 430px);
  padding: 38px;
  border: 1px solid #e8eaf2;
  border-radius: 24px;
  background: #fff;
  box-shadow: 0 24px 70px rgb(31 41 85 / 12%);
}

.login-icon {
  display: grid;
  width: 58px;
  height: 58px;
  margin-bottom: 24px;
  place-items: center;
  border-radius: 18px;
  background: #eeebff;
  font-size: 28px;
}

.login-eyebrow {
  margin-bottom: 10px;
  color: #6554d9;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1.5px;
}

h1 {
  margin: 0;
  color: #202344;
  font-size: 30px;
  letter-spacing: -1px;
}

.login-description {
  margin: 12px 0 28px;
  color: #73758b;
  line-height: 1.6;
}

.form-group {
  display: grid;
  gap: 8px;
  margin-bottom: 20px;
}

label {
  color: #343650;
  font-size: 14px;
  font-weight: 650;
}

input {
  box-sizing: border-box;
  width: 100%;
  padding: 14px 15px;
  border: 1px solid #dfe1ed;
  border-radius: 12px;
  background: #fafaff;
  color: #252642;
  font: inherit;
}

input:focus {
  border-color: #7565e8;
  outline: 3px solid rgb(117 101 232 / 15%);
}

.login-button {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: space-between;
  padding: 15px 18px;
  border: 0;
  border-radius: 12px;
  background: linear-gradient(110deg, #6755df, #8873f4);
  color: #fff;
  cursor: pointer;
  font: inherit;
  font-weight: 700;
  transition: transform 0.2s, box-shadow 0.2s;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 9px 22px rgb(103 85 223 / 25%);
}

.login-error {
  color: #bd263d;
  font-size: 14px;
}

.login-footer {
  margin: 24px 0 0;
  color: #898ba0;
  font-size: 12px;
  text-align: center;
}

@media (max-width: 480px) {
  .login-card {
    padding: 26px 22px;
  }

  h1 {
    font-size: 26px;
  }
}
</style>
