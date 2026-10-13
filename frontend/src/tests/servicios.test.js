import { describe, it, expect } from 'vitest'

describe('Pruebas de servicios', () => {
  it('debe filtrar los servicios por nombre', () => {
    const servicios = [
      { nombre: 'Desarrollo web' },
      { nombre: 'Diseño gráfico' },
      { nombre: 'Soporte técnico' }
    ]

    const resultado = servicios.filter((servicio) =>
      servicio.nombre.toLowerCase().includes('diseño')
    )

    expect(resultado).toHaveLength(1)
    expect(resultado[0].nombre).toBe('Diseño gráfico')
  })
})