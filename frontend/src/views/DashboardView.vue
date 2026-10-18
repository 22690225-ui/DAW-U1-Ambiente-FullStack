
<script setup>
import { RouterLink } from 'vue-router'

const tarjetas = [
  {
    icono: '✦',
    titulo: 'Explora los servicios',
    descripcion: 'Consulta las opciones disponibles en la plataforma.',
    enlace: '/servicios',
    accion: 'Ver servicios',
    clase: 'tarjeta-morada',
  },
  {
    icono: '↗',
    titulo: 'Navega con facilidad',
    descripcion: 'Encuentra la información que necesitas desde un solo lugar.',
    enlace: '/servicios',
    accion: 'Explorar catálogo',
    clase: 'tarjeta-azul',
  },
]
</script>

<template>
  <div class="dashboard">
    <section class="welcome-banner">
      <div class="welcome-content">
        <span class="welcome-label">PANEL PRINCIPAL</span>
        <h1>¡Bienvenido a Servicios+! 👋</h1>
        <p>
          Tu espacio para consultar y explorar los servicios de nuestra plataforma.
        </p>

        <RouterLink to="/servicios" class="welcome-button">
          Explorar servicios <span aria-hidden="true">→</span>
        </RouterLink>
      </div>

      <div class="welcome-illustration" aria-hidden="true">
        <div class="illustration-circle">
          <span>✦</span>
        </div>
        <div class="floating-card floating-card-one">✓ Todo organizado</div>
        <div class="floating-card floating-card-two">✧ Fácil de usar</div>
      </div>
    </section>

    <section class="dashboard-section">
      <div class="section-heading">
        <div>
          <p class="section-eyebrow">TU PLATAFORMA</p>
          <h2>¿Qué te gustaría hacer?</h2>
        </div>
        <span class="status-pill"><span></span> Sistema disponible</span>
      </div>

      <div class="dashboard-grid">
        <article
          v-for="tarjeta in tarjetas"
          :key="tarjeta.titulo"
          class="dashboard-card"
          :class="tarjeta.clase"
        >
          <div class="card-icon" aria-hidden="true">{{ tarjeta.icono }}</div>
          <h3>{{ tarjeta.titulo }}</h3>
          <p>{{ tarjeta.descripcion }}</p>

          <RouterLink :to="tarjeta.enlace" class="card-link">
            {{ tarjeta.accion }} <span aria-hidden="true">→</span>
          </RouterLink>
        </article>
      </div>
    </section>

    <section class="dashboard-note">
      <span class="note-icon" aria-hidden="true">💡</span>
      <div>
        <h3>Un espacio pensado para ti</h3>
        <p>
          Usa el menú superior para desplazarte entre las diferentes secciones.
        </p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.dashboard {
  width: min(1120px, 100%);
  margin: 0 auto;
  padding: 38px 24px 60px;
}

.welcome-banner {
  position: relative;
  display: flex;
  min-height: 310px;
  align-items: center;
  justify-content: space-between;
  gap: 25px;
  overflow: hidden;
  padding: 42px 48px;
  border-radius: 25px;
  background: linear-gradient(115deg, #5141bb 0%, #7665e8 58%, #9b83f4 100%);
  color: #fff;
}

.welcome-content {
  position: relative;
  z-index: 1;
  max-width: 620px;
}

.welcome-label,
.section-eyebrow {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 2px;
}

.welcome-label {
  color: #e2dcff;
}

.welcome-banner h1 {
  margin: 16px 0 12px;
  font-size: clamp(28px, 4vw, 42px);
  letter-spacing: -1.5px;
  line-height: 1.15;
}

.welcome-banner p {
  max-width: 480px;
  color: #eeeaff;
  line-height: 1.7;
}

.welcome-button {
  display: inline-flex;
  align-items: center;
  gap: 18px;
  margin-top: 16px;
  padding: 13px 18px;
  border-radius: 11px;
  background: #fff;
  color: #5545c3;
  font-size: 14px;
  font-weight: 750;
  text-decoration: none;
}

.welcome-button:hover {
  background: #f0edff;
}

.welcome-illustration {
  position: relative;
  width: 210px;
  height: 210px;
  flex-shrink: 0;
}

.illustration-circle {
  display: grid;
  width: 180px;
  height: 180px;
  place-items: center;
  border: 1px solid rgb(255 255 255 / 35%);
  border-radius: 50%;
  background: rgb(255 255 255 / 12%);
  box-shadow: inset 0 0 35px rgb(255 255 255 / 12%);
}

.illustration-circle span {
  color: #fff;
  font-size: 95px;
}

.floating-card {
  position: absolute;
  padding: 11px 14px;
  border: 1px solid rgb(255 255 255 / 40%);
  border-radius: 11px;
  background: rgb(255 255 255 / 95%);
  box-shadow: 0 8px 24px rgb(31 25 95 / 15%);
  color: #4a4384;
  font-size: 11px;
  font-weight: 750;
  white-space: nowrap;
}

.floating-card-one {
  top: 22px;
  right: -14px;
}

.floating-card-two {
  right: 0;
  bottom: 20px;
}

.dashboard-section {
  margin-top: 43px;
}

.section-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 22px;
}

.section-eyebrow {
  margin: 0 0 8px;
  color: #7868db;
}

.section-heading h2 {
  margin: 0;
  color: #292a4b;
  font-size: 25px;
  letter-spacing: -0.6px;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 12px;
  border: 1px solid #d7f0e1;
  border-radius: 999px;
  background: #f0fbf4;
  color: #287c4c;
  font-size: 12px;
  white-space: nowrap;
}

.status-pill span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #38b66a;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px;
}

.dashboard-card {
  padding: 26px;
  border: 1px solid #e7e7f2;
  border-radius: 19px;
  background: #fff;
  box-shadow: 0 8px 28px rgb(40 40 90 / 4%);
  transition: transform 0.2s, box-shadow 0.2s;
}

.dashboard-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 35px rgb(40 40 90 / 9%);
}

.card-icon {
  display: grid;
  width: 48px;
  height: 48px;
  place-items: center;
  border-radius: 14px;
  font-size: 25px;
  font-weight: 800;
}

.tarjeta-morada .card-icon {
  background: #efebff;
  color: #6955d9;
}

.tarjeta-azul .card-icon {
  background: #e4f4ff;
  color: #3187c8;
}

.dashboard-card h3 {
  margin: 19px 0 8px;
  color: #30314e;
  font-size: 19px;
}

.dashboard-card p {
  min-height: 48px;
  color: #74758c;
  font-size: 14px;
  line-height: 1.7;
}

.card-link {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-top: 12px;
  color: #6353d0;
  font-size: 14px;
  font-weight: 750;
  text-decoration: none;
}

.card-link:hover {
  text-decoration: underline;
}

.dashboard-note {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  margin-top: 25px;
  padding: 21px;
  border: 1px solid #e6e4fb;
  border-radius: 16px;
  background: #f8f7ff;
}

.note-icon {
  font-size: 25px;
}

.dashboard-note h3 {
  margin: 0 0 6px;
  color: #37345f;
  font-size: 15px;
}

.dashboard-note p {
  margin: 0;
  color: #73738a;
  font-size: 13px;
  line-height: 1.6;
}

@media (max-width: 760px) {
  .welcome-banner {
    padding: 32px 27px;
  }

  .welcome-illustration {
    display: none;
  }

  .section-heading {
    align-items: flex-start;
    flex-direction: column;
  }
}

@media (max-width: 560px) {
  .dashboard {
    padding: 25px 16px 40px;
  }

  .welcome-banner {
    min-height: auto;
    padding: 30px 23px;
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .section-heading h2 {
    font-size: 22px;
  }
}
</style>
