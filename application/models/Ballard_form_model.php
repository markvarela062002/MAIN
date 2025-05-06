<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ballard_form_model extends CI_Model {

    private $table = 'patient_information';

    public function save_assessment($data) {
        $this->db->where('name', $data['name']);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            // Update existing
            $this->db->where('name', $data['name']);
            return $this->db->update($this->table, $data);
        } else {
            // Insert new
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->db->insert($this->table, $data);
        }
    }

    public function insert($data) {
        // Insert assessment into the database
        $this->db->insert('patient_information', $data);  // replace with your table name
        return $this->db->insert_id();  // return the inserted record ID
    }
    
}