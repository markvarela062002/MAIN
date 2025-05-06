<template>
  <!-- Personal Details Section -->
  <div class="card personal-details">
    <h2 class="section-title">Patient Information</h2>
    <div class="form-grid">
      <div class="form-group">
        <label for="patientName">Patient Name</label>
        <input type="text" id="patientName" v-model="patientInfo.name" class="form-input" placeholder="First Name/ Middle Initial/ Surname">
      </div>
      <div class="form-group">
        <label for="patientSex">Sex</label>
        <select id="patientSex" v-model="patientInfo.sex" class="form-input">
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="form-group">
        <label for="hospitalNumber">Hospital No.</label>
        <input type="text" id="hospitalNumber" v-model="patientInfo.hospitalNumber" class="form-input" placeholder="Hospital number">
      </div>
      <div class="form-group">
        <label for="birthWeight">Birth Weight (grams)</label>
        <input type="number" id="birthWeight" v-model="patientInfo.birthWeight" class="form-input" placeholder="Birth weight">
      </div>
      <div class="form-group">
        <label for="patientRace">Race</label>
        <input type="text" id="patientRace" v-model="patientInfo.race" class="form-input" placeholder="Race">
      </div>
      <div class="form-group">
        <label for="length">Length (cm)</label>
        <input type="number" id="length" v-model="patientInfo.length" class="form-input" placeholder="Length">
      </div>
      <div class="form-group">
        <label for="birthDateTime">Date/Time of Birth</label>
        <input type="datetime-local" id="birthDateTime" v-model="patientInfo.birthDateTime" class="form-input">
      </div>
      <div class="form-group">
        <label for="headCircumference">Head Circ. (cm)</label>
        <input type="number" id="headCircumference" v-model="patientInfo.headCircumference" class="form-input" placeholder="Head circumference">
      </div>
      <div class="form-group">
        <label for="examDateTime">Date/Time of Exam</label>
        <input type="datetime-local" id="examDateTime" v-model="patientInfo.examDateTime" class="form-input">
      </div>
      <div class="form-group">
        <label for="examiner">Examiner</label>
        <input type="text" id="examiner" v-model="patientInfo.examiner" class="form-input" placeholder="Examiner name">
      </div>
      <div class="form-group">
        <label for="ageWhenExamined">Age When Examined (hours)</label>
        <input type="number" id="ageWhenExamined" v-model="patientInfo.ageWhenExamined" class="form-input" placeholder="Age in hours">
      </div>
      <div class="form-group span-full">
        <label>APGAR Score</label>
        <div class="apgar-container">
          <div class="apgar-item">
            <span class="apgar-label">1 Minute</span>
            <input type="number" min="0" max="10" v-model="patientInfo.apgarOneMinute" class="form-input">
          </div>
          <div class="apgar-item">
            <span class="apgar-label">5 Minutes</span>
            <input type="number" min="0" max="10" v-model="patientInfo.apgarFiveMinutes" class="form-input">
          </div>
          <div class="apgar-item">
            <span class="apgar-label">10 Minutes</span>
            <input type="number" min="0" max="10" v-model="patientInfo.apgarTenMinutes" class="form-input">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ballard Assessment Section -->
  <div class="ballard-assessment">
    <!-- Neuromuscular Maturity Table -->
    <div class="card">
      <h2 class="section-title">Neuromuscular Maturity</h2>
      <div class="table-container">
        <table class="assessment-table">
          <thead>
            <tr>
              <th>Neuromuscular Maturity Sign</th>
              <th v-for="score in [-1, 0, 1, 2, 3, 4, 5]" :key="'n-h-' + score">{{ score }}</th>
              <th class="recorded-column">Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sign, index) in neuromuscularSigns" :key="'n-' + sign.name">
              <td class="sign-name"><strong>{{ sign.name }}</strong></td>
              <td
                v-for="(desc, scoreIndex) in sign.scores"
                :key="'n-' + index + '-' + scoreIndex"
                @click="setNeuromuscularScore(index, scoreIndex - 1)"
                :class="['clickable', neuromuscularScores[index] === scoreIndex - 1 ? 'selected' : '', desc === '' ? 'disabled' : '']"
              >
                {{ desc }}
                <img v-if="sign.images" :src="sign.images[scoreIndex]" :alt="desc" class="score-image">
              </td>
              <td class="recorded-column">{{ neuromuscularScores[index] !== null ? neuromuscularScores[index] : '-' }}</td>
            </tr>
            <tr class="table-footer">
              <td colspan="8" class="table-total-label">Total Neuromuscular Score:</td>
              <td class="table-total-value">{{ totalNeuromuscular }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </div>


    <!-- Physical Maturity Table -->
    <div class="card">
      <h2 class="section-title">Physical Maturity</h2>
      <div class="table-container">
        <table class="assessment-table">
          <thead>
            <tr>
              <th>Sign</th>
              <th v-for="score in [-1, 0, 1, 2, 3, 4, 5]" :key="'p-h-' + score">{{ score }}</th>
              <th class="recorded-column">Score</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sign, index) in physicalSigns" :key="'p-' + sign.name">
              <td class="sign-name"><strong>{{ sign.name }}</strong></td>
              <td
                v-for="(desc, scoreIndex) in sign.scores"
                :key="'p-' + index + '-' + scoreIndex"
                @click="setPhysicalScore(index, scoreIndex - 1)"
                :class="['clickable', physicalScores[index] === scoreIndex - 1 ? 'selected' : '', desc === '' ? 'disabled' : '']"
              >
                {{ desc }}
              </td>
              <td class="recorded-column">{{ physicalScores[index] !== null ? physicalScores[index] : '-' }}</td>
            </tr>
            <tr class="table-footer">
              <td colspan="8" class="table-total-label">Total Physical Score:</td>
              <td class="table-total-value">{{ totalPhysical }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Summary Section -->
    <div class="card summary-section">
      <div class="totals-container">
        <div class="total-score">
          <span class="total-label">Neuromuscular Score:</span>
          <span class="total-value">{{ totalNeuromuscular }}</span>
        </div>
        <div class="total-score">
          <span class="total-label">Physical Score:</span>
          <span class="total-value">{{ totalPhysical }}</span>
        </div>
        <div class="total-score">
          <span class="total-label">Combined Score:</span>
          <span class="total-value">{{ combinedScore }}</span>
        </div>
      </div>

      <div class="summary-card">
        <h3>Assessment Summary</h3>
        <div class="summary-info">
          <div class="info-item">
            <div class="info-label">Neuromuscular Maturity</div>
            <div class="info-value">{{ totalNeuromuscular }} points</div>
          </div>
          <div class="info-item">
            <div class="info-label">Physical Maturity</div>
            <div class="info-value">{{ totalPhysical }} points</div>
          </div>
          <div class="info-item">
            <div class="info-label">Combined Maturity Score</div>
            <div class="info-value">{{ combinedScore }} points</div>
          </div>
          <div class="info-item">
            <div class="info-label">Estimated Gestational Age</div>
            <div class="info-value">{{ estimatedGestationalAge }} weeks</div>
          </div>
        </div>
      </div>

      <div class="actions">
    <!-- Reset Scores button -->
    <button class="btn-reset" @click="resetFormFields">Reset Form</button>

    <!-- Save Assessment button with dynamic text based on saving status -->
    <button class="btn-save" @click="handleSave" :disabled="isSaving">
      {{ isSaving ? 'Saving...' : 'Save Assessment' }}
    </button>        
  </div>
  </div>
    
</template>

<script setup>
import axios from 'axios';
import { ref, computed, watch } from 'vue';
import { useBallard } from '@/composables/ballard';

const { patientInfo, saveAssessment: ballardSaveAssessment, isSaving, resetForm} = useBallard(); // Rename `saveAssessment` to avoid conflict


// Neuromuscular Maturity Table Data
const neuromuscularSigns = [
   { 
     name: "Posture",
     scores: ["", "Slight Flexion", "Moderate Flexion", "Full Flexion", "Strong Flexion", "Full Recoil", ""],
     images: [
      "",
       "vue-project/src/assets/images/posture_0.png",
       "vue-project/src/assets/images/posture_1.png",
       "vue-project/src/assets/images/posture_2.png",
       "vue-project/src/assets/images/posture_3.png",
       "vue-project/src/assets/images/posture_4.png",

   ]
   },
   { 
     name: "Square Window (Wrist)",
     scores: [">90°", "90°", "60°", "45°", "30°", "0°", ""],
     images: [
       "vue-project/src/assets/images/sqaure_window_wrist_0.png",
       "vue-project/src/assets/images/sqaure_window_wrist_1.png",
       "vue-project/src/assets/images/sqaure_window_wrist_2.png",
       "vue-project/src/assets/images/sqaure_window_wrist_3.png",
       "vue-project/src/assets/images/sqaure_window_wrist_4.png",
       "vue-project/src/assets/images/sqaure_window_wrist_n1.png",
       ""
     ]
   },
   { 
     name: "Arm Recoil",
     scores: ["", "140°-180°", "110°-140°", "90°-110°", "60°-90°", "<60°", ""],
     images: [
       "",
       "vue-project/src/assets/images/arm_recoil_0.png",
       "vue-project/src/assets/images/arm_recoil_1.png",
       "vue-project/src/assets/images/arm_recoil_2.png",
       "vue-project/src/assets/images/arm_recoil_3.png",
       "vue-project/src/assets/images/arm_recoil_4.png",
       ""
     ]
   },
   { 
     name: "Popliteal Angle",
     scores: ["180°", "160°", "140°", "120°", "100°", "90°", "<90°"],
     images: [
       "vue-project/src/assets/images/popliteal_angle_n1.png",
       "vue-project/src/assets/images/popliteal_angle_0.png",
       "vue-project/src/assets/images/popliteal_angle_1.png",
       "vue-project/src/assets/images/popliteal_angle_2.png",
       "vue-project/src/assets/images/popliteal_angle_3.png",
       "vue-project/src/assets/images/popliteal_angle_4.png",
       "vue-project/src/assets/images/popliteal_angle_5.png"
     ]
   },
   { 
     name: "Scarf Sign",
     scores: ["No resistance", "Slight resistance", "Moderate resistance", "Considerable resistance", "Difficult to reach", "Cannot reach", ""],
     images: [
       "vue-project/src/assets/images/scarf_sign_n1.png",
       "vue-project/src/assets/images/scarf_sign_0.png",
       "vue-project/src/assets/images/scarf_sign_1.png",
       "vue-project/src/assets/images/scarf_sign_2.png",
       "vue-project/src/assets/images/scarf_sign_3.png",
       "vue-project/src/assets/images/scarf_sign_4.png",
       ""       
     ]
   },
   { 
     name: "Heel to Ear",
     scores: ["No resistance", "Slight resistance", "Moderate resistance", "Considerable resistance", "Difficult to reach", "Cannot reach", ""],
     images: [
       "vue-project/src/assets/images/heal_to_ear_n1.png",
       "vue-project/src/assets/images/heal_to_ear_0.png",
       "vue-project/src/assets/images/heal_to_ear_1.png",
       "vue-project/src/assets/images/heal_to_ear_2.png",
       "vue-project/src/assets/images/heal_to_ear_3.png",
       "vue-project/src/assets/images/heal_to_ear_4.png",
       ""        
     ]
   }
 ];

// Physical Maturity Table Data
const physicalSigns = [
   {
     name: "Skin",
     scores: [
       "Sticky, friable, transparent",
       "Gelatinous, red, translucent",
       "Smooth, pink, visible veins",
       "Superficial peeling, rash, few veins",
       "Cracking, pale areas, rare veins",
       "Parchment, deep cracking, no vessels",
       "Leathery, cracked, wrinkled"
     ]
   },
   {
     name: "Lanugo",
     scores: ["None", "Sparse", "Abundant", "Thinning", "Bald areas", "Mostly bald", ""]
   },
   {
     name: "Plantar Surface",
     scores: [
       "Heel-toe 40-50 mm: -1",
       ">50 mm, no crease",
       "Faint red marks",
       "Anterior transverse crease only",
       "Creases anterior 2/3",
       "Creases over entire sole",
       ""
     ]
   },
   {
     name: "Breast",
     scores: [
       "Imperceptible",
       "Barely perceptible",
       "Flat areola, no bud",
       "Stippled areola, 1-2 mm bud",
       "Raised areola, 3-4 mm bud",
       "Full areola, 5-10 mm bud",
       ""
     ]
   },
   {
     name: "Eye/Ear",
     scores: [
       "Lids fused, loosely",
       "Lids open, slow recoil",
       "Not-curved pinna, soft recoil",
       "Well-curved pinna, soft recoil",
       "Formed & firm, instant recoil",
       "Thick cartilage, ear stiff",
       ""
     ]
   },
   {
     name: "Genitals (Male)",
     scores: [
       "Scrotum flat, smooth",
       "Scrotum empty, faint rugae",
       "Testes in canal, rare rugae",
       "Testes descending, few rugae",
       "Testes down, good rugae",
       "Testes pendulous, deep rugae",
       ""
     ]
   },
   {
     name: "Genitals (Female)",
     scores: [
       "Clitoris prominent, labia flat",
       "Prominent clitoris, small labia",
       "Prominent clitoris, enlarging labia",
       "Majora & minora equal",
       "Majora large, minora small",
       "Majora covers minora",
       ""
     ]
   }
 ];

// Reactive state to track selected scores
const neuromuscularScores = ref(neuromuscularSigns.map(() => null));
const physicalScores = ref(physicalSigns.map(() => null));

// Score setting functions
const setNeuromuscularScore = (index, score) => {
  if (neuromuscularSigns[index].scores[score + 1] !== '') {
    neuromuscularScores.value[index] = score;
  }
};

const setPhysicalScore = (index, score) => {
  if (physicalSigns[index].scores[score + 1] !== '') {
    physicalScores.value[index] = score;
  }
};

// Calculate the total scores
const totalNeuromuscular = computed(() =>
  neuromuscularScores.value.reduce((sum, val) => sum + (val !== null ? val : 0), 0)
);

const totalPhysical = computed(() =>
  physicalScores.value.reduce((sum, val) => sum + (val !== null ? val : 0), 0)
);

const combinedScore = computed(() => totalNeuromuscular.value + totalPhysical.value);

// Calculate estimated gestational age
const estimatedGestationalAge = computed(() => {
  const score = combinedScore.value;
  // Ballard Score to gestational age conversion (approximate)
  if (score <= 0) return 0;
  if (score <= 5) return 26;
  if (score <= 10) return 28;
  if (score <= 15) return 30;
  if (score <= 20) return 32;
  if (score <= 25) return 34;
  if (score <= 30) return 36;
  if (score <= 35) return 38;
  if (score <= 40) return 40;
  if (score <= 45) return 42;
  return 44;
});

// Check if assessment is complete
const isComplete = computed(() => {
  const neuroComplete = !neuromuscularScores.value.includes(null);
  const physicalComplete = physicalSigns.every((sign, index) => {
    const hasValidScores = sign.scores.some(desc => desc !== '');
    return !hasValidScores || physicalScores.value[index] !== null;
  });
  const patientInfoComplete = !!patientInfo.value.name && 
                              !!patientInfo.value.sex && 
                              !!patientInfo.value.hospitalNumber && 
                              !!patientInfo.value.examDateTime;
  return neuroComplete && physicalComplete && patientInfoComplete;
});

const resetFormFields = () => {
  neuromuscularScores.value = neuromuscularSigns.map(() => null);
  physicalScores.value = physicalSigns.map(() => null);
  
  patientInfo.value = {
    name: '',
    sex: '',
    hospitalNumber: '',
    birthWeight: '',
    race: '',
    length: '',
    birthDateTime: '',
    headCircumference: '',
    examDateTime: '',
    examiner: '',
    ageWhenExamined: '',
    apgarOneMinute: '',
    apgarFiveMinutes: '',
    apgarTenMinutes: '',
  };
};





// Calculate age when examined automatically
const calculateAgeWhenExamined = () => {
  if (patientInfo.value.birthDateTime && patientInfo.value.examDateTime) {
    const birthDate = new Date(patientInfo.value.birthDateTime);
    const examDate = new Date(patientInfo.value.examDateTime);
    const diffHours = Math.floor((examDate - birthDate) / (1000 * 60 * 60));
    patientInfo.value.ageWhenExamined = diffHours;
  }
};

// Watch for changes in birth or exam dates to update age
watch([
  () => patientInfo.value.birthDateTime,
  () => patientInfo.value.examDateTime
], calculateAgeWhenExamined);


// Function to handle saving assessment
const handleSave = async () => {
  // Update patientInfo with the calculated scores
  patientInfo.value.posture = neuromuscularScores.value[0];
  patientInfo.value.squareWindow = neuromuscularScores.value[1];
  patientInfo.value.armRecoil = neuromuscularScores.value[2];
  patientInfo.value.poplitealAngle = neuromuscularScores.value[3];
  patientInfo.value.scarfSign = neuromuscularScores.value[4];
  patientInfo.value.heelToEar = neuromuscularScores.value[5];

  patientInfo.value.skin = physicalScores.value[0];
  patientInfo.value.lanugo = physicalScores.value[1];
  patientInfo.value.plantarSurface = physicalScores.value[2];
  patientInfo.value.breast = physicalScores.value[3];
  patientInfo.value.eyeEar = physicalScores.value[4];
  patientInfo.value.genitals = physicalScores.value[5];

  patientInfo.value.totalNeuromuscular = totalNeuromuscular.value;
  patientInfo.value.totalPhysical = totalPhysical.value;
  patientInfo.value.combinedScore = combinedScore.value;
  patientInfo.value.estimatedGestationalAge = estimatedGestationalAge.value;

  try {
    // Save the assessment (which queues the job)
    await ballardSaveAssessment();
    alert('Assessment queued successfully!');
    resetFormFields();
  } catch (error) {
    console.error('Error saving assessment:', error);
  }
};
</script>

<style scoped>
 .personal-details {
   margin-bottom: 1.5rem;
 }
 
 .form-grid {
   display: grid;
   grid-template-columns: repeat(3, 1fr);
   gap: 1rem;
 }
 
 .form-group {
   display: flex;
   flex-direction: column;
 }
 
 .span-full {
   grid-column: 1 / -1;
 }
 
 .form-input {
   padding: 0.625rem;
   border: 1px solid #cbd5e1;
   border-radius: 6px;
   font-size: 0.875rem;
   transition: border-color 0.2s ease;
 }
 
 .form-input:focus {
   outline: none;
   border-color: #3b82f6;
   box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
 }
 
 label {
   font-size: 0.875rem;
   font-weight: 500;
   color: #475569;
   margin-bottom: 0.375rem;
 }
 
 .apgar-container {
   display: flex;
   gap: 1rem;
 }
 
 .apgar-item {
   display: flex;
   flex-direction: column;
   flex: 1;
 }
 
 .apgar-label {
   font-size: 0.75rem;
   color: #64748b;
   margin-bottom: 0.25rem;
 }
 
 @media (max-width: 768px) {
   .form-grid {
     grid-template-columns: 1fr;
   }
 }
 
 /* Ballard assessment styles */
 .ballard-assessment {
   font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
   max-width: 1200px;
   margin: 0 auto;
   color: #334155;
 }
 
 .card {
   background-color: white;
   border-radius: 12px;
   box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
   padding: 1.5rem;
   margin-bottom: 1.5rem;
   border: 1px solid #e2e8f0;
 }
 
 .section-title {
   color: #1e40af;
   font-weight: 600;
   font-size: 1.5rem;
   margin-top: 0;
   margin-bottom: 1.5rem;
   padding-bottom: 0.75rem;
   border-bottom: 2px solid #e5e7eb;
 }
 
 .table-container {
   overflow-x: auto;
   margin-bottom: 1rem;
   border-radius: 8px;
 }
 
 .assessment-table {
   width: 100%;
   border-collapse: separate;
   border-spacing: 0;
   overflow: hidden;
 }
 
 .assessment-table th {
   background-color: #1e40af;
   color: white;
   font-weight: 500;
   text-align: center;
   padding: 1rem 0.75rem;
   position: sticky;
   top: 0;
 }
 
 .assessment-table th:first-child {
   text-align: left;
   width: 170px;
   border-top-left-radius: 8px;
 }
 
 .assessment-table th:last-child {
   border-top-right-radius: 8px;
 }
 
 .assessment-table td {
   padding: 0.875rem;
   border-top: 1px solid #e5e7eb;
   border-top: 1px solid #25272b;
   border-bottom: 1px solid #25272b;
   font-size: 0.875rem;
   text-align: center;
   vertical-align: middle;
 }
 
 .assessment-table td:first-child {
   text-align: left;
 }
 
 .assessment-table tbody tr:nth-child(even) {
   background-color: #f8fafc;
 }
 
 .assessment-table tbody tr:hover {
   background-color: #f1f5f9;
 }
 
 .sign-name {
   background-color: #f1f5f9;
   font-weight: 500;
   color: #334155;
 }
 
 .clickable {
   cursor: pointer;
   transition: all 0.2s ease;
   position: relative;
 }
 
 .clickable:hover {
   background-color: #dbeafe !important;
 }
 
 .selected {
   background-color: #3b82f6 !important;
   font-weight: 500;
   color: white;
 }
 
 .disabled {
   background-color: #f1f5f9;
   background-color: #f5faff;
   cursor: default;
 }
 
 .disabled:hover {
   background-color: #f1f5f9 !important;
 }
 
 .recorded-column {
   background-color: #f1f5f9;
   font-weight: 600;
   width: 80px;
   text-align: center !important;
   color: #1e40af;
 }
 
 .summary-section {
   background-color: #f8fafc;
 }
 
 .totals-container {
   display: flex;
   justify-content: space-between;
   flex-wrap: wrap;
   gap: 1rem;
   margin-bottom: 1.5rem;
 }
 
 .total-score {
   background-color: #1e40af;
   color: white;
   padding: 1rem 1.5rem;
   border-radius: 8px;
   display: flex;
   flex-direction: column;
   min-width: 150px;
   flex: 1;
   box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
 }
 
 .total-label {
   font-size: 0.875rem;
   opacity: 0.9;
   margin-bottom: 0.25rem;
 }
 
 .total-value {
   font-size: 1.5rem;
   font-weight: 700;
 }
 
 .summary-card {
   background-color: white;
   border-radius: 8px;
   border: 1px solid #e2e8f0;
   padding: 1.5rem;
   margin-bottom: 1.5rem;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
 }
 
 .summary-card h3 {
   color: #1e40af;
   margin-top: 0;
   margin-bottom: 1.25rem;
   padding-bottom: 0.75rem;
   border-bottom: 1px solid #e5e7eb;
   font-size: 1.25rem;
 }
 
 .summary-info {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
   gap: 1rem;
 }
 
 .info-item {
   background-color: #f1f5f9;
   padding: 1rem;
   border-radius: 8px;
   transition: transform 0.2s ease, box-shadow 0.2s ease;
 }
 
 .info-item:hover {
   transform: translateY(-2px);
   box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
 }
 
 .info-label {
   color: #64748b;
   font-size: 0.875rem;
   margin-bottom: 0.25rem;
 }
 
 .info-value {
   font-size: 1.375rem;
   font-weight: 700;
   color: #1e40af;
 }
 
 .actions {
   display: flex;
   justify-content: flex-end;
   gap: 1rem;
   margin-top: 1rem;
 }
 
 .btn-reset {
   padding: 0.75rem 1.5rem;
   border-radius: 8px;
   font-weight: 500;
   cursor: pointer;
   background-color: white;
   color: #475569;
   border: 1px solid #cbd5e1;
   transition: all 0.2s ease;
 }
 
 .btn-reset:hover {
   background-color: #f1f5f9;
   border-color: #94a3b8;
 }
 
 .btn-save {
   padding: 0.75rem 1.5rem;
   border-radius: 8px;
   font-weight: 500;
   cursor: pointer;
   background-color: #1e40af;
   color: white;
   border: none;
   transition: all 0.2s ease;
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
 }
 
 .btn-save:hover {
   background-color: #1e3a8a;
   box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 }
 
 .btn-save:disabled {
   background-color: #bfdbfe;
   cursor: not-allowed;
   box-shadow: none;
 }
 
 /* Score image styles */
 .score-image {
   max-width: 100px;
   max-height: 80px;
   margin-bottom: 0.5rem;
   display: block;
   margin: 0 auto;
 }
 
 /* Table footer styles */
 .table-footer {
   background-color: #dbeafe;
   font-weight: 600;
 }
 
 .table-total-label {
   text-align: right !important;
   padding: 0.75rem 1rem;
   color: #1e40af;
 }
 
 .table-total-value {
   background-color: #1e40af;
   color: white;
   font-weight: 700;
   font-size: 1.125rem;
   padding: 0.75rem;
 }
 
 
 @media (max-width: 768px) {
   .totals-container {
     flex-direction: column;
   }
   
   .total-score {
     width: 100%;
   }
 }
 </style>