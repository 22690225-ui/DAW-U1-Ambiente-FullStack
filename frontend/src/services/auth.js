
async function solicitar(url, opciones = {}) {
  const respuesta = await fetch(url, {
    credentials: 'same-origin',
    ...opciones,
    headers: {
      'Content-Type': 'application/json',
      ...opciones.headers,
    },
  })

  const datos = await respuesta.json()

  if (!respuesta.ok) {
    throw new Error(datos.message || 'Ocurrió un error en la solicitud.')
  }

  return datos
}

export function iniciarSesion(username, password) {
  return solicitar('/api/login.php', {
    method: 'POST',
    body: JSON.stringify({ username, password }),
  })
}

export function consultarSesion() {
  return solicitar('/api/session.php')
}

export function cerrarSesion() {
  return solicitar('/api/login.php', {
    method: 'POST',
    body: JSON.stringify({ action: 'logout' }),
  })
}
