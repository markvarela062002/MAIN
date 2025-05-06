<template>
    <div class="assessment-view">
      <PatientForm 
        v-model:patient="patient" 
        v-model:assessment="assessment"
        @load-patient="loadPatient"
      />
      
      <NeuromuscularTable 
        v-model:scores="neuromuscular"
        :total="neuromuscularTotal"
      />
      
      <PhysicalTable 
        v-model:scores="physical"
        :total="physicalTotal"
      />
      
      <AssessmentSummary
        :neuromuscular-total="neuromuscularTotal"
        :physical-total="physicalTotal"
        :combined-score="combinedScore"
        :gestational-age="estimatedGestationalAge"
      />
      
      <div class="actions">
        <button @click="reset" class="btn-reset">Reset</button>
        <button 
          @click="save" 
          class="btn-save"
          :disabled="!isFormComplete"
        >
          Save Assessment
        </button>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { defineComponent, computed } from 'vue';
  import { useBallardAssessment } from '@/composables/useBallardAssessment';
  import PatientForm from '@/components/PatientForm.vue';
  import NeuromuscularTable from '@/components/NeuromuscularTable.vue';
  import PhysicalTable from '@/components/PhysicalTable.vue';
  import AssessmentSummary from '@/components/AssessmentSummary.vue';
  
  export default defineComponent({
    name: 'AssessmentView',
    components: {
      PatientForm,
      NeuromuscularTable,
      PhysicalTable,
      AssessmentSummary
    },
    setup() {
      const {
        patient,
        assessment,
        neuromuscular,
        physical,
        neuromuscularTotal,
        physicalTotal,
        combinedScore,
        estimatedGestationalAge,
        loadPatient,
        save,
        reset
      } = useBallardAssessment();
  
      const isFormComplete = computed(() => {
        return (
          patient.value.hospital_number &&
          patient.value.name &&
          patient.value.sex &&
          patient.value.birth_date &&
          assessment.value.exam_date
        );
      });
  
      return {
        patient,
        assessment,
        neuromuscular,
        physical,
        neuromuscularTotal,
        physicalTotal,
        combinedScore,
        estimatedGestationalAge,
        loadPatient,
        save,
        reset,
        isFormComplete
      };
    }
  });
  </script>
  
  <style scoped>
  .assessment-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }
  
  .btn-reset {
    padding: 10px 20px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-save {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-save:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
  }
  </style>