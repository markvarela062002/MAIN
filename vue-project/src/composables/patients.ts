import { ref, Ref } from "vue";
import axios from "axios";

interface Patient {
    id: number;
    hospital_number: number;
    name: string;
    sex: string;
    race: string;
    birth_date: string;
    exam_date: string;
    examiner: string;
    age_when_exam: string;
}

export function Patients(page: string = "all") {
    const patients: Ref<Patient[]> = ref([]); // Initialize as empty array

    const get_all_patients = async () => {
        try {
            const response = await axios.get(
                `/api/v1/get/patients`,
                {
                    params: {
                        perPage: page,
                    },
                }
            );
            patients.value = response.data.data || response.data;
        } catch (error) {
            console.error("Error fetching data:", error);
            patients.value = []; // Set to empty array on error
        }
    }

    const delete_patient = async ($id: number) => {
        try {
            const response = await axios.delete(
                `/api/v1/delete/patient/${$id}`,
                {
                    params: {
                        perPage: page,
                    },
                }
            );
            patients.value = response.data;
        } catch (error) {
            console.error("Error deleting patient:", error);
        }
    }

    const generate_pdf = async (id: number) => {
        try {
            const response = await axios.get(`/api/v1/patient/pdf/${id}`, { responseType: 'blob' });
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        } catch (error) {
            console.error("Error generating PDF:", error);
        }
    }

    return { patients, get_all_patients, delete_patient, generate_pdf };
}
