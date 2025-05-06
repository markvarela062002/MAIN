<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Patient_list_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // GET ALL Patients
    public function get_all_patients()
    {
        return $this->db->get('patient_information')->result_array();
    }

    // GET SINGLE Patient by ID
    public function get_patient($id)
    {
        return $this->db->get_where('patient_information', ['id' => $id])->row();
    }

    // INSERT Patient
    public function insert_patient($data)
    {
        return $this->db->insert('patient_information', $data);
    }

    // DELETE Patient by ID
    public function delete_patient($id)
    {
        return $this->db->delete('patient_information', ['id' => $id]);
    }

    public function get_patient_by_id($id)
    {
    $query = $this->db->get_where('patient_information', array('id' => $id));
    return $query->row();  // Make sure this fetches a single row, not an array
    }
}
