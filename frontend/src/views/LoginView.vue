
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import LoginForm from '../components/LoginForm.vue'
import { iniciarSesion } from '../services/auth'

const router = useRouter()
const error = ref('')
const cargando = ref(false)

async function procesarLogin(credenciales) {
  error.value = ''
  cargando.value = true

  try {
    await iniciarSesion(
      credenciales.username,
      credenciales.password
    )

    await router.push('/dashboard')
  } catch (e) {
    error.value = e.message || 'No fue posible iniciar sesión.'
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-decoration decoration-one"></div>
    <div class="login-decoration decoration-two"></div>

    <section class="login-layout">
      <div class="login-intro">
        <span class="brand-badge">◆ SERVICIOS+</span>

        <p class="intro-label">TU ESPACIO DIGITAL</p>
        <h2>Todo lo que necesitas, en un solo lugar.</h2>

        <p class="intro-description">
          Accede a la plataforma y descubre una forma más sencilla
          de administrar tus servicios.
        </p>

        <div class="feature-list">
          <div class="feature-item">
            <span aria-hidden="true">✓</span>
            <p>Acceso rápido y sencillo</p>
          </div>

          <div class="feature-item">
            <span aria-hidden="true">✓</span>
            <p>Servicios organizados para ti</p>
          </div>

          <div class="feature-item">
            <span aria-hidden="true">✓</span>
            <p>Una experiencia clara y moderna</p>
          </div>
        </div>
      </div>

      <div class="login-form-container">
        <LoginForm @login="procesarLogin" />

        <p
          v-if="error"
          class="login-error"
          role="alert"
        >
          {{ error }}
        </p>

        <p
          v-if="cargando"
          class="login-loading"
          role="status"
        >
          Verificando credenciales...
        </p>
      </div>
    </section>

    <footer class="login-page-footer">
      © 2026 Servicios+ · Plataforma de gestión
    </footer>
  </div>
</template>

<style scoped>
.login-page {
  position: relative;
  display: flex;
  min-height: 82vh;
  flex-direction: column;
  justify-content: center;
  overflow: hidden;
  padding: 45px 6%;
  background: linear-gradient(
    135deg,
    #f4f2ff 0%,
    #f9fbff 55%,
    #edf7ff 100%
  );
}

.login-layout {
  position: relative;
  z-index: 1;
  display: flex;
  width: min(1100px, 100%);
  align-items: center;
  justify-content: space-between;
  gap: 65px;
  margin: 0 auto;
}

.login-intro {
  max-width: 470px;
}

.brand-badge {
  display: inline-block;
  margin-bottom: 75px;
  padding: 11px 15px;
  border: 1px solid #e1dcff;
  border-radius: 12px;
  background: rgb(255 255 255 / 75%);
  color: #6554d9;
  font-size: 13px;
  font-weight: 850;
  letter-spacing: 1px;
}

.intro-label {
  color: #6b59dc;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 2px;
}

.login-intro h2 {
  margin: 14px 0 18px;
  color: #242448;
  font-size: clamp(32px, 4vw, 49px);
  letter-spacing: -1.8px;
  line-height: 1.13;
}

.intro-description {
  max-width: 430px;
  color: #71738c;
  font-size: 16px;
  line-height: 1.8;
}

.feature-list {
  display: grid;
  gap: 13px;
  margin-top: 30px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.feature-item span {
  display: grid;
  width: 26px;
  height: 26px;
  flex-shrink: 0;
  place-items: center;
  border-radius: 50%;
  background: #e4e0ff;
  color: #5c4bce;
  font-weight: 800;
}

.feature-item p {
  margin: 0;
  color: #454761;
  font-size: 14px;
}

.login-form-container {
  width: min(100%, 430px);
  flex-shrink: 0;
}

.login-error {
  margin: 14px 0 0;
  padding: 12px 14px;
  border: 1px solid #f4c7cf;
  border-radius: 10px;
  background: #fff1f3;
  color: #a91e35;
  font-size: 14px;
  line-height: 1.5;
}

.login-loading {
  margin: 14px 0 0;
  color: #6554d9;
  font-size: 14px;
  text-align: center;
}

.login-decoration {
  position: absolute;
  border-radius: 50%;
  filter: blur(2px);
  pointer-events: none;
}

.decoration-one {
  top: -170px;
  right: -100px;
  width: 430px;
  height: 430px;
  background: rgb(164 145 255 / 15%);
}

.decoration-two {
  bottom: -180px;
  left: 28%;
  width: 370px;
  height: 370px;
  background: rgb(115 196 255 / 14%);
}

.login-page-footer {
  position: relative;
  z-index: 1;
  margin-top: 48px;
  color: #85869b;
  font-size: 12px;
  text-align: center;
}

@media (max-width: 850px) {
  .login-layout {
    flex-direction: column;
    align-items: stretch;
    gap: 35px;
  }

  .brand-badge {
    margin-bottom: 30px;
  }

  .login-intro {
    max-width: 560px;
    margin: 0 auto;
  }

  .login-form-container {
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .login-page {
    padding: 30px 18px;
  }

  .login-intro h2 {
    font-size: 32px;
  }
}
</style>
