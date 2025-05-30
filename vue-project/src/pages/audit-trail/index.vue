<script setup>
import { ref, onMounted, watch } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dialog from 'primevue/dialog'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import axios from 'axios'

const isActive = ref(false)
const toggleAccordion = () => {
  isActive.value = !isActive.value
}

const visible = ref(false)
const selectedAudit = ref(null)

const records = ref([])
const totalRecords = ref(0)
const rows = ref(20)
const loading = ref(false)

const filters = ref({
  global: { value: '', matchMode: 'contains' },
  user_id: { value: '', matchMode: 'contains' },
  table_name: { value: '', matchMode: 'contains' },
  event: { value: '', matchMode: 'contains' },
  ip_address: { value: '', matchMode: 'contains' },
  created_at: { value: '', matchMode: 'contains' },
})

// Add a function to clear all filter fields
const clearFilters = () => {
  filters.value.global.value = ''
  filters.value.user_id.value = ''
  filters.value.table_name.value = ''
  filters.value.event.value = ''
  filters.value.ip_address.value = ''
  filters.value.created_at.value = ''
  
  // After clearing filters, fetch data again
  page.value = 0
  fetchData()
}

const globalFilterFields = ['user_id', 'table_name', 'event', 'ip_address', 'created_at']
const multiSortMeta = ref([])

const page = ref(0)
const sortField = ref('user_id')
const sortOrder = ref("")

const fetchData = async () => {
  loading.value = true
  const params = {
    first: page.value * rows.value,
    rows: rows.value,
    sortField: sortField.value,
    sortOrder: sortOrder.value,
    filters: JSON.stringify(filters.value),
    multiSortMeta: JSON.stringify(multiSortMeta.value)
  }

  try {
    const { data } = await axios.get('/api/v1/get/audit_trails', { params })
    records.value = data.data
    totalRecords.value = data.totalRecords
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

watch(() => filters.value.global.value, () => {
  page.value = 0
  fetchData()
}, { immediate: true })

watch(() => filters.value.user_id.value, () => {
  page.value = 0
  fetchData()
})

watch(() => filters.value.table_name.value, () => {
  page.value = 0
  fetchData()
})

watch(() => filters.value.event.value, () => {
  page.value = 0
  fetchData()
})
watch(() => filters.value.ip_address.value, () => {
  page.value = 0
  fetchData()
})
watch(() => filters.value.created_at.value, () => {
  page.value = 0
  fetchData()
})

const onPage = (event) => {
  page.value = event.page
  fetchData()
}

const onSort = (event) => {
  sortField.value = event.sortField
  sortOrder.value = event.sortOrder
  multiSortMeta.value = event.multiSortMeta || []
  fetchData()
}

const onFilter = () => {
  page.value = 0
  fetchData()
}

const handleView = async (record) => {
  selectedAudit.value = record
  visible.value = true

  try {
    const { data } = await axios.get('/api/v1/get/user_id', {
      params: {
        id: record.id,
      }
    })

    selectedAudit.value.auditTrailDetails = data?.data ?? []
  } catch (error) {
    console.error('Error fetching audit details:', error)
    selectedAudit.value.auditTrailDetails = []
  }
}

const generateDescription = (entry) => {
  const { event, old_values, new_values, user_id, column_mappings } = entry

  let oldObj = {}
  let newObj = {}

  try {
    oldObj = typeof old_values === 'string' ? JSON.parse(old_values) : old_values || {}
  } catch (e) {
    console.warn('Failed to parse old_values', e)
  }

  try {
    newObj = typeof new_values === 'string' ? JSON.parse(new_values) : new_values || {}
  } catch (e) {
    console.warn('Failed to parse new_values', e)
  }

  const changes = []
  const allKeys = new Set([...Object.keys(oldObj), ...Object.keys(newObj)])

  for (const key of allKeys) {
    const oldVal = oldObj[key] ?? '—'
    const newVal = newObj[key] ?? '—'
    const columnDescription = column_mappings?.[key] || key

    if (event === 'update') {
      if (oldVal !== newVal) {
        changes.push(`Updated "${columnDescription}": from "${oldVal}" to "${newVal}"`)
      }
    } else if (event === 'insert') {
      changes.push(`Set "${columnDescription}" to "${newVal}"`)
    } else if (event === 'delete') {
      changes.push(`Removed "${columnDescription}" which was "${oldVal}"`)
    }
  }

  return changes.length > 0 ? changes : ['No changes recorded']
}

const getGroupKey = (item) => `${item.user_id}-${item.table_name}-${item.event}`

const getRowSpanForGroup = (groupKey) =>
  records.value.filter(item => getGroupKey(item) === groupKey).length

const shouldPrintGroup = (index, groupKey) =>
  index === records.value.findIndex(item => getGroupKey(item) === groupKey)

const getGroupRowClass = (item) => {
  const groupKey = getGroupKey(item)
  const uniqueGroups = [...new Set(records.value.map(getGroupKey))]
  const groupIndex = uniqueGroups.indexOf(groupKey)
  return groupIndex % 2 === 0 ? 'bg-white' : 'bg-green-200'
}

const exportData = (type) => {
  const params = {
    export: 1,
    exportType: type,
    filters: JSON.stringify(filters.value),
    multiSortMeta: JSON.stringify(multiSortMeta.value),
  }

  const query = new URLSearchParams(params).toString()
  window.open(`/api/v1/get/dictionary?${query}`, '_blank')
}

onMounted(() => {
  fetchData()
})
</script>

<template>
  <div class="p-6 space-y-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-semibold text-gray-800">Audit Trails</h1>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <InputText
        v-model="filters.global.value"
        placeholder="Search audits..."
        class="w-full sm:w-[75%] p-2 border border-gray-300 rounded-md"
      />
      <div class="flex flex-wrap gap-2 sm:ml-auto">
        <Button icon="pi pi-filter" @click="toggleAccordion" class="p-button-sm" v-tooltip.top="'Advance Filter'" />
        <Button icon="pi pi-file" @click="exportData('csv')" class="p-button-sm" v-tooltip.top="'Export CSV'" />
        <Button icon="pi pi-file-excel" @click="exportData('excel')" class="p-button-success p-button-sm" v-tooltip.top="'Export Excel'" />
        <Button icon="pi pi-file-pdf" @click="exportData('pdf')" class="p-button-danger p-button-sm" v-tooltip.top="'Export PDF'" />
      </div>
    </div>

    <Accordion :activeIndex="isActive ? 0 : null" class="w-full">
      <AccordionTab header="">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <InputText v-model="filters.user_id.value" placeholder="Filter by User ID" class="p-inputtext-sm" />
          <InputText v-model="filters.table_name.value" placeholder="Filter by Table Name" class="p-inputtext-sm" />
          <InputText v-model="filters.event.value" placeholder="Filter by Event" class="p-inputtext-sm" />
          <InputText v-model="filters.ip_address.value" placeholder="Filter by IP" class="p-inputtext-sm" />
          <InputText v-model="filters.created_at.value" placeholder="Filter by Created At" class="p-inputtext-sm" />
        </div>
        <div class="flex justify-end mt-4">
          <Button 
            label="Clear Filters" 
            icon="pi pi-times" 
            @click="clearFilters" 
            class="p-button-outlined p-button-danger p-button-sm"
          />
        </div>
      </AccordionTab>
    </Accordion>

    <DataTable
      :value="records"
      :lazy="true"
      :paginator="true"
      :rowsPerPageOptions="[1, 5, 10]"
      :rows="rows"
      :totalRecords="totalRecords"
      :loading="loading"
      :filters="filters"
      :globalFilterFields="globalFilterFields"
      @page="onPage"
      @sort="onSort"
      @filter="onFilter"
      :multiSortMeta="multiSortMeta"
      :rowClass="getGroupRowClass"
      class="rounded-lg shadow-sm"
    >
      <Column field="user_id" header="User ID" sortable>
        <template #body="{ data, index }">
          <span v-if="shouldPrintGroup(index, getGroupKey(data))">
            {{ data.user_id }}
          </span>
        </template>
      </Column>

      <Column field="event" header="Event" sortable>
        <template #body="{ data, index }">
          <span v-if="shouldPrintGroup(index, getGroupKey(data))">
            {{ data.event }}
          </span>
        </template>
      </Column>

      <Column field="table_name" header="Table Name" sortable>
        <template #body="{ data, index }">
          <span v-if="shouldPrintGroup(index, getGroupKey(data))">
            {{ data.table_name }}
          </span>
        </template>
      </Column>

      <Column field="ip_address" header="IP Address" sortable>
        <template #body="{ data, index }">
          <span v-if="shouldPrintGroup(index, getGroupKey(data))">
            {{ data.ip_address }}
          </span>
        </template>
      </Column>

      <Column field="created_at" header="Created At" sortable class="text-sm">
        <template #body="{ data }">
          {{ new Date(data.created_at).toLocaleString() }}
        </template>
      </Column>

      <Column header="Actions">
        <template #body="{ data }">
          <Button
            icon="pi pi-eye"
            class="p-button-text text-gray-600 hover:text-blue-500"
            @click="handleView(data)"
            v-tooltip.top="'View details'"
          />
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="visible" header="Audit Details" modal class="w-full sm:w-[60%] md:w-[50%]">
      <div v-if="selectedAudit?.auditTrailDetails?.length > 0" class="space-y-4">
        <div
          v-for="(detail, i) in selectedAudit.auditTrailDetails"
          :key="i"
          class="p-4 bg-gray-50 rounded-md border"
        >
          <div class="text-sm text-gray-600 mb-2">
            <strong>User:</strong> {{ detail.user_id }} |
            <strong>Event:</strong> {{ detail.event }} |
            <strong>Time:</strong> {{ new Date(detail.created_at).toLocaleString() }}
          </div>
          <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
            <li v-for="(desc, j) in generateDescription(detail)" :key="j">
              {{ desc }}
            </li>
          </ul>
        </div>
      </div>
      <div v-else class="text-sm text-gray-600">No audit trail details found.</div>
    </Dialog>
  </div>
</template>