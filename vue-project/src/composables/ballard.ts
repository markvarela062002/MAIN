import { ref } from "vue";
import axios from "axios";

// Define the Ballard assessment type for better typing and structure
export interface BallardAssessment {
  name: string;
  sex: string;
  hospitalNumber: string;
  birthWeight: number | null;
  race: string;
  length: number | null;
  birthDateTime: string;
  headCircumference: number | null;
  examDateTime: string;
  examiner: string;
  ageWhenExamined: number | null;
  apgarOneMinute: number | null;
  apgarFiveMinutes: number | null;
  apgarTenMinutes: number | null;
  posture: number | null;
  squareWindow: number | null;
  armRecoil: number | null;
  poplitealAngle: number | null;
  scarfSign: number | null;
  heelToEar: number | null;
  skin: number | null;
  lanugo: number | null;
  plantarSurface: number | null;
  breast: number | null;
  eyeEar: number | null;
  genitals: number | null;
  totalNeuromuscular: number | null;
  totalPhysical: number | null;
  combinedScore: number | null;
  estimatedGestationalAge: number | null;
}

export function useBallard() {
  // Declare reactive state for ballard assessment
  const patientInfo = ref<BallardAssessment>({
    name: "",
    sex: "",
    hospitalNumber: "",
    birthWeight: null,
    race: "",
    length: null,
    birthDateTime: "",
    headCircumference: null,
    examDateTime: "",
    examiner: "",
    ageWhenExamined: null,
    apgarOneMinute: null,
    apgarFiveMinutes: null,
    apgarTenMinutes: null,
    posture: null,
    squareWindow: null,
    armRecoil: null,
    poplitealAngle: null,
    scarfSign: null,
    heelToEar: null,
    skin: null,
    lanugo: null,
    plantarSurface: null,
    breast: null,
    eyeEar: null,
    genitals: null,
    totalNeuromuscular: null,
    totalPhysical: null,
    combinedScore: null,
    estimatedGestationalAge: null
  });

  const isSaving = ref(false);

  const saveAssessment = async () => {
    try {
      if (!patientInfo.value.hospitalNumber || !patientInfo.value.examDateTime) {
        throw new Error("Hospital number and exam date are required");
      }

      isSaving.value = true;

      const payload = {
        hospital_number: patientInfo.value.hospitalNumber,
        name: patientInfo.value.name,
        sex: patientInfo.value.sex,
        race: patientInfo.value.race,
        birth_date_time: patientInfo.value.birthDateTime,
        birth_weight: patientInfo.value.birthWeight,
        length: patientInfo.value.length,
        head_circumference: patientInfo.value.headCircumference,
        exam_date_time: patientInfo.value.examDateTime,
        examiner: patientInfo.value.examiner,
        age_when_examined: patientInfo.value.ageWhenExamined,
        apgar_one_minute: patientInfo.value.apgarOneMinute,
        apgar_five_minutes: patientInfo.value.apgarFiveMinutes,
        apgar_ten_minutes: patientInfo.value.apgarTenMinutes,
        posture: patientInfo.value.posture,
        square_window: patientInfo.value.squareWindow,
        arm_recoil: patientInfo.value.armRecoil,
        popliteal_angle: patientInfo.value.poplitealAngle,
        scarf_sign: patientInfo.value.scarfSign,
        heel_to_ear: patientInfo.value.heelToEar,
        skin: patientInfo.value.skin,
        lanugo: patientInfo.value.lanugo,
        plantar_surface: patientInfo.value.plantarSurface,
        breast: patientInfo.value.breast,
        eye_ear: patientInfo.value.eyeEar,
        genitals: patientInfo.value.genitals,
        total_neuromuscular: patientInfo.value.totalNeuromuscular,
        total_physical: patientInfo.value.totalPhysical,
        combined_score: patientInfo.value.combinedScore,
        estimated_gestational_age: patientInfo.value.estimatedGestationalAge
      };

      await axios.post("api/v1/post/patient", payload, {
        headers: {
          "Content-Type": "application/json"
        }
      });

      alert("Assessment saved successfully!");
      resetForm();
    } catch (error) {
      console.error("Save error:", error);
    } finally {
      isSaving.value = false;
    }
  };

  const resetForm = () => {
    // Reset the form after successful save
    patientInfo.value = {
      name: "",
      sex: "",
      hospitalNumber: "",
      birthWeight: null,
      race: "",
      length: null,
      birthDateTime: "",
      headCircumference: null,
      examDateTime: "",
      examiner: "",
      ageWhenExamined: null,
      apgarOneMinute: null,
      apgarFiveMinutes: null,
      apgarTenMinutes: null,
      posture: null,
      squareWindow: null,
      armRecoil: null,
      poplitealAngle: null,
      scarfSign: null,
      heelToEar: null,
      skin: null,
      lanugo: null,
      plantarSurface: null,
      breast: null,
      eyeEar: null,
      genitals: null,
      totalNeuromuscular: null,
      totalPhysical: null,
      combinedScore: null,
      estimatedGestationalAge: null
    };
  };

  return { patientInfo, saveAssessment, isSaving, resetForm };
}

