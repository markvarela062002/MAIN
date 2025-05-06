export interface Patient {
    id?: number;
    hospital_number: string;
    name: string;
    sex: 'Male' | 'Female' | 'Other';
    race?: string;
    birth_date: string;
    birth_weight?: number;
    length?: number;
    head_circumference?: number;
}

export interface Assessment {
    id?: number;
    patient_id?: number;
    exam_date: string;
    examiner?: string;
    age_when_examined?: number;
    apgar_one_minute?: number;
    apgar_five_minutes?: number;
    apgar_ten_minutes?: number;
    neuromuscular_total?: number;
    physical_total?: number;
    combined_score?: number;
    gestational_age?: number;
}

export interface NeuromuscularScores {
    posture?: number;
    square_window?: number;
    arm_recoil?: number;
    popliteal_angle?: number;
    scarf_sign?: number;
    heel_to_ear?: number;
}

export interface PhysicalScores {
    skin?: number;
    lanugo?: number;
    plantar_surface?: number;
    breast?: number;
    eye_ear?: number;
    genitals?: number;
}

export interface AssessmentData {
    patient: Patient;
    assessment: Assessment;
    neuromuscular: NeuromuscularScores;
    physical: PhysicalScores;
}