<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Job_logs_controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load the model
        $this->load->model('Job_logs_model');
        $this->load->library('form_validation');
    }

    // API to Insert Job Log
    public function store()
    {
        $json = json_decode(file_get_contents('php://input'), true);

        // Check if 'queue' or 'payload' is missing
        if (empty($json['queue']) || empty($json['payload'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'Missing queue or payload']));
        }

        try {
            // Insert the job log
            $jobId = $this->Job_logs_model->insert([
                'queue'   => $json['queue'],
                'payload' => json_encode($json['payload']),
                'status'  => 'queued',  // Default status
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'Job log created',
                    'job_id'  => $jobId
                ]));
        } catch (Exception $e) {
            // Log the error message
            log_message('error', 'Error creating job log: ' . $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'Failed to create job log', 'error' => $e->getMessage()]));
        }
    }
}
