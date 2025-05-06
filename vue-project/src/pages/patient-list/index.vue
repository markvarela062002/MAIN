<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import { Patients } from '@/composables/patients';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const confirm = useConfirm();
const toast = useToast();
const { patients, get_all_patients, delete_patient, generate_pdf } = Patients();

// Patient search term
const searchTerm = ref('');

// Sorting state
const sortField = ref('id');
const sortOrder = ref(1);  // 1 for ascending, -1 for descending

const onSort = (event: { sortField: string; sortOrder: number }) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;
};

// Redirect to New Patient form
function redirectToNewPatient() {
  window.location.href = "http://hospital.test/new-ballard-form";
}

// Handle delete patient
const handleDelete = (id: number) => {
  confirm.require({
    message: 'Are you sure you want to delete this patient?',
    header: 'Confirmation',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      try {
        await delete_patient(id);
        toast.add({
          severity: 'success',
          summary: 'Success',
          detail: 'Patient deleted successfully',
          life: 3000
        });
        // Refresh the patient list after deletion
        await get_all_patients();
      } catch (error) {
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete patient',
          life: 3000
        });
        console.error('Delete error:', error);
      }
    },
    reject: () => {
      // User rejected the deletion
    }
  });
};

// Ensure patients is initialized as array
if (!patients.value) {
  patients.value = [];
}

// Get all patients on mounted
onMounted(() => {
  get_all_patients();
});

// Filter patients based on search term
const filteredPatients = computed(() => {
  return patients.value.filter(patient => {
    return (
      patient.name?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      patient.hospital_number?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      patient.sex?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      patient.examiner?.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
  });
});
</script>

<template>
  <div class="patient-list-container">
    <h1 class="table-title">Patient List</h1>
  </div>
  <br>
  <div class="controls">
    <!-- Search bar beside the Add New Patient button -->
    <input v-model="searchTerm" type="text" placeholder="Search patients..." class="search-bar" />
    <button class="create-button" @click="redirectToNewPatient">
      <i class="fas fa-plus"></i> Add New Patient
    </button>
  </div>

  <div class="surface-0 text-center">
    <DataTable 
      :value="filteredPatients" 
      tableStyle="min-width: 100%" 
      :sortField="sortField" 
      :sortOrder="sortOrder"
      @sort="onSort"
      :sortableColumns="true"
    >
      <Column field="id"                header="ID"                sortable></Column>
      <Column field="hospital_number"   header="Hospital Number"   sortable></Column>
      <Column field="name"              header="Name"              sortable></Column>  
      <Column field="sex"               header="Sex"               sortable></Column>
      <Column field="birth_date_time"   header="Birth Date"        sortable></Column>
      <Column field="exam_date_time"    header="Exam Date"         sortable></Column>
      <Column field="examiner"          header="Examiner"          sortable></Column>
      <Column field="age_when_examined" header="Age When Examined" sortable></Column>
      <Column header="Actions">
        <template #body="slotProps">
          <Button 
            icon="pi pi-trash" 
            class="p-button-rounded p-button-danger p-button-text"
            @click="handleDelete(slotProps.data.id)"
            v-tooltip.top="'Delete patient'"
          />
          <Button 
            icon="pi pi-file" 
            class="p-button-rounded p-button-info p-button-text"
            @click="generate_pdf(slotProps.data.id)"
            v-tooltip.top="'Generate File'"
          />
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<style scoped>
.patient-list-container {
  text-align: center;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table-title {
  font-size: 24px;
  margin-bottom: 20px;
  text-align: center;
}

.controls {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.search-bar {
  padding: 8px;
  width: 250px;
  border-radius: 4px;
  border: 1px solid #ccc;
  font-size: 16px;
}

.create-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.create-button:hover {
  background-color: #2980b9;
}

.create-button i {
  margin-right: 5px;
}

.p-button-danger {
  color: #e74c3c;
}

.p-button-danger:hover {
  color: #c0392b;
}
</style>
