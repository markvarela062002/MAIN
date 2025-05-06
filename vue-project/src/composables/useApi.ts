import { ref } from 'vue';
import type { Patient, AssessmentData } from '@/types/assessment';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api';

export function useApi() {
    const error = ref<string | null>(null);
    const isLoading = ref(false);

    async function fetchPatient(hospitalNumber: string): Promise<Patient | null> {
        isLoading.value = true;
        error.value = null;
        
        try {
            const response = await fetch(`${API_BASE_URL}/patient/${hospitalNumber}`);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Failed to fetch patient');
            }
            
            return data.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Unknown error occurred';
            return null;
        } finally {
            isLoading.value = false;
        }
    }

    async function saveAssessment(assessmentData: AssessmentData): Promise<boolean> {
        isLoading.value = true;
        error.value = null;
        
        try {
            const response = await fetch(`${API_BASE_URL}/assessment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ...assessmentData.patient,
                    ...assessmentData.assessment,
                    ...assessmentData.neuromuscular,
                    ...assessmentData.physical
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Failed to save assessment');
            }
            
            return true;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Unknown error occurred';
            return false;
        } finally {
            isLoading.value = false;
        }
    }

    return {
        error,
        isLoading,
        fetchPatient,
        saveAssessment
    };
}