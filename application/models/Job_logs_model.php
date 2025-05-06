<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Job_logs_model extends CI_Model {

    private $table = 'job_logs';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Insert new job log
    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        try {
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            // Catch duplicate entry (job_id should be unique)
            log_message('error', 'Error inserting job log: ' . $e->getMessage());
            return false;
        }
    }

    // Update status by internal ID
    public function update_status($id, $status, $exception = null)
    {
        $update_data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($exception) {
            $update_data['exception'] = $exception;
        }

        try {
            $this->db->where('id', $id);
            $this->db->update($this->table, $update_data);
            return $this->db->affected_rows();
        } catch (Exception $e) {
            log_message('error', 'Error updating job log status: ' . $e->getMessage());
            return false;
        }
    }

    // Update status by job_id (public ID)
    public function update_status_by_job_id($job_id, $status, $exception = null)
    {
        $update_data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($exception) {
            $update_data['exception'] = $exception;
        }

        try {
            $this->db->where('job_id', $job_id);
            $this->db->update($this->table, $update_data);
            return $this->db->affected_rows();
        } catch (Exception $e) {
            log_message('error', 'Error updating job log status by job_id: ' . $e->getMessage());
            return false;
        }
    }

    // Get by internal numeric ID
    public function get_by_id($id)
    {
        try {
            $query = $this->db->get_where($this->table, ['id' => $id]);
            return $query->row_array();
        } catch (Exception $e) {
            log_message('error', 'Error fetching job log by ID: ' . $e->getMessage());
            return null;
        }
    }

    // Get by public job_id
    public function get_log_by_job_id($job_id)
    {
        try {
            $query = $this->db->get_where($this->table, ['job_id' => $job_id]);
            return $query->row_array();
        } catch (Exception $e) {
            log_message('error', 'Error fetching job log by job_id: ' . $e->getMessage());
            return null;
        }
    }
}
