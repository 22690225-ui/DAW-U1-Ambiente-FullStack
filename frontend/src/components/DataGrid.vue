<script setup>
import { ref, computed, watch } from 'vue'
import * as XLSX from 'xlsx'
import { jsPDF } from 'jspdf'
import { autoTable } from 'jspdf-autotable'

const props = defineProps({
  rows: { type: Array, default: () => [] },
  columns: { type: Array, required: true },
  title: { type: String, default: 'Datos' },
  fileName: { type: String, default: 'datos' }
})

const search = ref('')
const pageSize = ref(5)
const currentPage = ref(1)

function displayValue(row, column) {
  const value = row[column.key]
  return column.formatter ? column.formatter(value, row) : value ?? ''
}

const searchableColumns = computed(() =>
  props.columns.filter(column => column.searchable !== false && column.key !== 'acciones')
)

const filteredRows = computed(() => {
  const term = search.value.toLowerCase().trim()
  if (!term) return props.rows

  return props.rows.filter(row =>
    searchableColumns.value.some(column =>
      String(displayValue(row, column)).toLowerCase().includes(term)
    )
  )
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredRows.value.length / pageSize.value))
)

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredRows.value.slice(start, start + pageSize.value)
})

watch([search, pageSize], () => {
  currentPage.value = 1
})

watch(totalPages, total => {
  if (currentPage.value > total) currentPage.value = total
})

const exportColumns = computed(() =>
  props.columns.filter(column => column.export !== false && column.key !== 'acciones')
)

function exportRows() {
  return filteredRows.value.map(row => {
    const result = {}
    exportColumns.value.forEach(column => {
      result[column.label] = displayValue(row, column)
    })
    return result
  })
}

function exportExcel() {
  const worksheet = XLSX.utils.json_to_sheet(exportRows())
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, props.title)
  XLSX.writeFile(workbook, `${props.fileName}.xlsx`)
}

function exportPDF() {
  const doc = new jsPDF({
    orientation: exportColumns.value.length > 5 ? 'landscape' : 'portrait'
  })

  doc.text(props.title, 14, 15)

  autoTable(doc, {
    startY: 22,
    head: [exportColumns.value.map(column => column.label)],
    body: filteredRows.value.map(row =>
      exportColumns.value.map(column => displayValue(row, column))
    ),
    styles: { fontSize: 8 },
    theme: 'striped'
  })

  doc.save(`${props.fileName}.pdf`)
}
</script>

<template>
  <section class="data-grid">
    <div class="toolbar">
      <input
        v-model="search"
        type="search"
        placeholder="Buscar..."
        aria-label="Buscar registros"
      >

      <div class="exports">
        <button type="button" @click="exportExcel">Exportar Excel</button>
        <button type="button" @click="exportPDF">Exportar PDF</button>
      </div>
    </div>

    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.key">
              {{ column.label }}
            </th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(row, index) in paginatedRows" :key="row.id ?? index">
            <td v-for="column in columns" :key="column.key">
              <slot
                v-if="column.key === 'acciones'"
                name="acciones"
                :row="row"
              />
              <span v-else>{{ displayValue(row, column) }}</span>
            </td>
          </tr>

          <tr v-if="paginatedRows.length === 0">
            <td :colspan="columns.length" class="empty">
              No se encontraron registros.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="footer">
      <span>{{ filteredRows.length }} registros</span>

      <label>
        Mostrar
        <select v-model.number="pageSize">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        por página
      </label>

      <div class="pagination">
        <button type="button" :disabled="currentPage === 1" @click="currentPage--">
          Anterior
        </button>
        <span>Página {{ currentPage }} de {{ totalPages }}</span>
        <button
          type="button"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Siguiente
        </button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.data-grid { width: 100%; }
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 18px;
}
.toolbar input {
  max-width: 350px;
  flex: 1;
  min-width: 180px;
}
.exports, .pagination {
  display: flex;
  align-items: center;
  gap: 8px;
}
button, select, input {
  padding: 10px 12px;
  border: 1px solid #deddea;
  border-radius: 8px;
  font: inherit;
}
button {
  cursor: pointer;
  background: #eeeaff;
  color: #5e4dce;
  font-weight: 700;
}
button:disabled {
  opacity: .45;
  cursor: not-allowed;
}
.table-scroll { overflow-x: auto; }
table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}
th, td {
  padding: 13px 10px;
  border-bottom: 1px solid #eeeef4;
  font-size: 13px;
}
th { color: #77778c; background: #faf9fd; }
.empty { text-align: center; color: #77778c; }
.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-top: 18px;
  color: #77778c;
  font-size: 13px;
}
.footer label {
  display: flex;
  align-items: center;
  gap: 7px;
}
.pagination span { white-space: nowrap; }
@media(max-width:650px) {
  .footer { align-items: flex-start; flex-direction: column; }
}
</style>
