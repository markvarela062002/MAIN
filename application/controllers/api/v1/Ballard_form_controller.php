<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ballard_form_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Load database, Redis configuration, and the Ballard form model
        $this->load->database();
        $this->load->config('redis');  // Load Redis configuration
        
        // Access Redis configuration values
        $redis_host = $this->config->item('redis_host');
        $redis_port = $this->config->item('redis_port');
        $redis_timeout = $this->config->item('redis_timeout');
        
        // Load Redis library with configuration
        $this->load->library('redis', [
            'host' => $redis_host,
            'port' => $redis_port,
            'timeout' => $redis_timeout
        ]);
        
        $this->load->model('Ballard_form_model');
        header('Content-Type: application/json');
    }
    
    public function save_assessment() {
        // Get the JSON data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check for required fields
        $required = [
            'hospital_number', 'name', 'sex', 'birth_date_time',
            'exam_date_time', 'examiner', 'age_when_examined'
        ];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Format the dates correctly
        $data['birth_date_time'] = date('Y-m-d H:i:s', strtotime($data['birth_date_time']));
        $data['exam_date_time'] = date('Y-m-d H:i:s', strtotime($data['exam_date_time']));
        
        // Create a unique job ID
        $jobId = 'assessment_' . time();
        
        $cacheKey = 'assessment_data_' . $jobId;
        $this->redis->set($cacheKey, json_encode($data));
        
        // Create a simplified job payload with only essential information
        $jobData = [
            'job_id' => $jobId,
            'type' => 'save_assessment',
            'patient_name' => $data['name'],
            'hospital_number' => $data['hospital_number'],
            'cache_key' => $cacheKey,
            'timestamp' => time(),
        ];

        // Push the job data to Redis queue
        if ($this->redis->rpush('job_logs', json_encode($jobData))) {
            echo json_encode([
                'message' => 'Assessment queued for processing',
                'job_id' => $jobId
            ]);

            $this->load->model('Job_logs_model'); // Ensure model is loaded

            try {
                $this->Job_logs_model->insert([
                    'queue'      => 'save_assessment',
                    'job_id'     => $jobId,
                    'payload'    => json_encode($jobData),
                    'status'     => 'processing',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Inserted fallback job log for [$jobId] with 'processing' status.\n";
            } catch (Exception $e) {
                echo "⚠️ Failed to insert fallback job log for [$jobId]: " . $e->getMessage() . "\n";
                // optionally just skip and continue
            }
            
            
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to queue assessment']);
        }


    }
}