<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_trail_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('tcpdf');
        $this->load->library('DataTableService');
        $this->load->model('Audit_trail_model');
    }

    public function get_all_audit_trails()
    {
        try {
            // Call the model method to fetch the audit data
            $query = $this->Audit_trail_model->get_all_audit_trails();
    
            // Initialize the DataTableService with the correct column mappings
            $this->datatableservice->initialize($query, [
                'ID' => 'id',
                'User ID' => 'user_id',
                'Table Name' => 'table_name',
                'Event' => 'event',
                'Old Values' => 'old_values',
                'New Values' => 'new_values',
                'URL' => 'url',
                'IP Address' => 'ip_address',
                'User Agent' => 'user_agent',
                'Created At' => 'created_at',
                'Conditions' => 'conditions',
            ]);
            
            $filters = json_decode($this->input->get('filters', true), true);
$createdAtFrom = $filters['created_at_from'] ?? null;
$createdAtTo = $filters['created_at_to'] ?? null;

if ($createdAtFrom) {
    $from = date('Y-m-d H:i:s', strtotime($createdAtFrom));
    $this->db->where('created_at >=', $from);
}

if ($createdAtTo) {
    $to = date('Y-m-d H:i:s', strtotime($createdAtTo));
    $this->db->where('created_at <=', $to);
}

            
            

            // Handle export parameters
            $export = $this->input->get('export');
            $exportType = $this->input->get('exportType') ?? 'csv';
    
            // Get the response from the DataTableService
            $response = $this->datatableservice->getResponse($export, $exportType);
    
            // Return the response, or export if requested
            if (!$export) {
                echo json_encode($response);
            }
        } catch (Exception $e) {
            // Log any errors for debugging purposes
            log_message('error', 'Error in get_all_audit_trails: ' . $e->getMessage());
            show_error('An error occurred while fetching the audit data. Please check the logs for more details.');
        }
    }

    public function get_all_dictionary()
    {
        try {
            // Call the model method to fetch the audit data
            $query = $this->Audit_trail_model->get_all_dictionary();
    
            // Initialize the DataTableService with the correct column mappings
            $this->datatableservice->initialize($query, [
                'Column Name' => 'column_name',
                'Column Description' => 'column_description',
                'Data Type' => 'data_type',
                'Data Width' => 'data_width',
                'Is Prikey' => 'is_prikey',
                'Is Nullable' => 'is_nullable',
                'Numeric Precision' => 'numeric_precision',
                'Numeric Scale' => 'numeric_scale',
                'Table Name' => 'table_name',
            ]);
    
            // Handle export parameters
            $export = $this->input->get('export');
            $exportType = $this->input->get('exportType') ?? 'csv';
    
            // Get the response from the DataTableService
            $response = $this->datatableservice->getResponse($export, $exportType);
    
            // Return the response, or export if requested
            if (!$export) {
                echo json_encode($response);
            }
        } catch (Exception $e) {
            // Log any errors for debugging purposes
            log_message('error', 'Error in get_all_audits: ' . $e->getMessage());
            show_error('An error occurred while fetching the audit data. Please check the logs for more details.');
        }
    }

    public function get_user_audits_by_id()
    {
        $id = $this->input->get('id');
        if (empty($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'ID parameter is required']));
            return;
        }

        try {
            $data = $this->Audit_trail_model->get_user_audits_by_id($id);

            log_message('debug', 'ID: ' . $id);
            log_message('debug', 'Records found: ' . count($data));

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['data' => $data]));
        } catch (Exception $e) {
            log_message('error', 'Error in get_user_audits_by_id: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['error' => 'An error occurred while fetching audit data']));
        }
    }
}
?>