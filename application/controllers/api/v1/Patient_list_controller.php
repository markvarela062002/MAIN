<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Patient_list_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('tcpdf');
        $this->load->model('Patient_list_model');
    }

    // GET all patients
    public function get_all_patients()
    {
        $patients = $this->Patient_list_model->get_all_patients();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($patients));
    }

    // DELETE patient
    public function delete_patient($id)
    {
        $this->Patient_list_model->delete_patient($id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }

    // GENERATE PDF
    public function generate_patient_pdf($id)
    {
        $this->load->library('tcpdf');
        $patient = $this->Patient_list_model->get_patient_by_id($id);
    
        if (!$patient) {
            return $this->output->set_status_header(404)->set_output(json_encode(['status' => 'error', 'message' => 'Patient not found']));
        }
    
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Medical Evaluation System');
        $pdf->SetTitle('Patient Assessment Report');
        $pdf->SetSubject('Patient Assessment Data');
        $pdf->SetKeywords('TCPDF, PDF, Medical, Assessment');
    
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
    
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
    
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
        // Add a page
        $pdf->AddPage();
    
        // Title with blue background
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetFillColor(30, 144, 255); // Dodger Blue
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->Cell(0, 10, 'Patient Assessment Report', 0, 1, 'C', true);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->Ln(5);
    
        // Patient personal information section
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(30, 144, 255); // Dodger Blue
        $pdf->Cell(0, 10, 'Personal Information', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Black text
    
        // Create a table for personal info with blue accents
        $html = '<table border="0" cellpadding="5">
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Hospital Number</b></td>
                <td width="70%">' . htmlspecialchars($patient->hospital_number) . '</td>
            </tr>
            <tr>
                <td width="30%"><b>Name</b></td>
                <td width="70%">' . htmlspecialchars($patient->name) . '</td>
            </tr>
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Sex</b></td>
                <td width="70%">' . htmlspecialchars($patient->sex) . '</td>
            </tr>
            <tr>
                <td width="30%"><b>Race</b></td>
                <td width="70%">' . htmlspecialchars($patient->race) . '</td>
            </tr>
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Birth Date/Time</b></td>
                <td width="70%">' . htmlspecialchars($patient->birth_date_time) . '</td>
            </tr>
            <tr>
                <td width="30%"><b>Birth Weight (grams)</b></td>
                <td width="70%">' . htmlspecialchars($patient->birth_weight) . '</td>
            </tr>
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Length (cm)</b></td>
                <td width="70%">' . htmlspecialchars($patient->length) . '</td>
            </tr>
            <tr>
                <td width="30%"><b>Head Circumference (cm)</b></td>
                <td width="70%">' . htmlspecialchars($patient->head_circumference) . '</td>
            </tr>
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Exam Date/Time</b></td>
                <td width="70%">' . htmlspecialchars($patient->exam_date_time) . '</td>
            </tr>
            <tr>
                <td width="30%"><b>Examiner</b></td>
                <td width="70%">' . htmlspecialchars($patient->examiner) . '</td>
            </tr>
            <tr bgcolor="#E6F0FA">
                <td width="30%"><b>Age When Examined</b></td>
                <td width="70%">' . htmlspecialchars($patient->age_when_examined) . ' days</td>
            </tr>
        </table>';
    
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(5);
    
        // APGAR scores
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(30, 144, 255); // Dodger Blue
        $pdf->Cell(0, 10, 'APGAR Scores', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Black text
    
        $html_apgar = '<table border="0" cellpadding="5">
            <tr bgcolor="#E6F0FA">
                <td width="33%"><b>One Minute</b></td>
                <td width="33%"><b>Five Minutes</b></td>
                <td width="34%"><b>Ten Minutes</b></td>
            </tr>
            <tr>
                <td>' . htmlspecialchars($patient->apgar_one_minute) . '</td>
                <td>' . htmlspecialchars($patient->apgar_five_minutes) . '</td>
                <td>' . htmlspecialchars($patient->apgar_ten_minutes) . '</td>
            </tr>
        </table>';
    
        $pdf->writeHTML($html_apgar, true, false, true, false, '');
        $pdf->Ln(5);
    
        // Neuromuscular scores
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(30, 144, 255); // Dodger Blue
        $pdf->Cell(0, 10, 'Neuromuscular Assessment', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Black text
    
        $html_neuro = '<table border="0" cellpadding="5">
            <tr bgcolor="#E6F0FA">
                <td><b>Posture</b></td>
                <td><b>Square Window</b></td>
                <td><b>Arm Recoil</b></td>
                <td><b>Popliteal Angle</b></td>
                <td><b>Scarf Sign</b></td>
                <td><b>Heel to Ear</b></td>
                <td><b>Total</b></td>
            </tr>
            <tr>
                <td>' . htmlspecialchars($patient->posture) . '</td>
                <td>' . htmlspecialchars($patient->square_window) . '</td>
                <td>' . htmlspecialchars($patient->arm_recoil) . '</td>
                <td>' . htmlspecialchars($patient->popliteal_angle) . '</td>
                <td>' . htmlspecialchars($patient->scarf_sign) . '</td>
                <td>' . htmlspecialchars($patient->heel_to_ear) . '</td>
                <td>' . htmlspecialchars($patient->total_neuromuscular) . '</td>
            </tr>
        </table>';
    
        $pdf->writeHTML($html_neuro, true, false, true, false, '');
        $pdf->Ln(5);
    
        // Physical scores
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(30, 144, 255); // Dodger Blue
        $pdf->Cell(0, 10, 'Physical Assessment', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Black text
    
        $html_physical = '<table border="0" cellpadding="5">
            <tr bgcolor="#E6F0FA">
                <td><b>Skin</b></td>
                <td><b>Lanugo</b></td>
                <td><b>Plantar Surface</b></td>
                <td><b>Breast</b></td>
                <td><b>Eye/Ear</b></td>
                <td><b>Genitals</b></td>
                <td><b>Total</b></td>
            </tr>
            <tr>
                <td>' . htmlspecialchars($patient->skin) . '</td>
                <td>' . htmlspecialchars($patient->lanugo) . '</td>
                <td>' . htmlspecialchars($patient->plantar_surface) . '</td>
                <td>' . htmlspecialchars($patient->breast) . '</td>
                <td>' . htmlspecialchars($patient->eye_ear) . '</td>
                <td>' . htmlspecialchars($patient->genitals) . '</td>
                <td>' . htmlspecialchars($patient->total_physical) . '</td>
            </tr>
        </table>';
    
        $pdf->writeHTML($html_physical, true, false, true, false, '');
        $pdf->Ln(5);
    
        // Summary scores
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(30, 144, 255); // Dodger Blue
        $pdf->Cell(0, 10, 'Assessment Summary', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0); // Black text
    
        $html_summary = '<table border="0" cellpadding="5">
            <tr bgcolor="#E6F0FA">
                <td width="33%"><b>Combined Score</b></td>
                <td width="33%"><b>Estimated Gestational Age</b></td>
                <td width="34%"><b>Assessment Date</b></td>
            </tr>
            <tr>
                <td>' . htmlspecialchars($patient->combined_score) . '</td>
                <td>' . htmlspecialchars($patient->estimated_gestational_age) . ' weeks</td>
                <td>' . htmlspecialchars($patient->updated_at) . '</td>
            </tr>
        </table>';
    
        $pdf->writeHTML($html_summary, true, false, true, false, '');
    
        // Output the PDF to the browser
        $pdf->Output('patient_' . $patient->hospital_number . '_assessment_report.pdf', 'I');
    }
}    