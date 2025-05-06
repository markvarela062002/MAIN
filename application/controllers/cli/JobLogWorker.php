<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JobLogWorker extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Load Redis and models
        $redis_config = array(
            'host' => $this->config->item('redis_host'),
            'port' => $this->config->item('redis_port'),
        );
        $this->load->library('redis', $redis_config);
        $this->load->model('Job_logs_model');
        $this->load->model('Ballard_form_model');
    }

    public function processJobLogs() {
        while (true) {
            // Check for jobs in the queue
            $job = $this->redis->lpop('job_logs');
    
            if ($job === null) {
                sleep(1);
                continue;
            }
    
            // Process job (this part won't echo "Listening..." repeatedly)
    
            $jobData = json_decode($job, true);
    
            if (!$jobData || !isset($jobData['job_id'])) {
                echo "Listening...\n";
                sleep(1);
                continue;
            }
    
            $jobId = $jobData['job_id'];
    
            // Check if the job already exists
            $existingLog = $this->Job_logs_model->get_log_by_job_id($jobId);
    
            if ($existingLog) {
                // Update status to processing
                $this->Job_logs_model->update_status_by_job_id($jobId, 'processing');
                echo "Updated job [$jobId] to 'processing'.\n";
            } else {
                // Fallback: insert new job log if it wasn't created
                $this->Job_logs_model->insert([
                    'queue'      => 'save_assessment',
                    'job_id'     => $jobId,
                    'payload'    => json_encode($jobData),
                    'status'     => 'processing',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "Inserted fallback job log for [$jobId] with 'processing' status.\n";
            }
    
            // Process the job
            if ($jobData['type'] === 'save_assessment') {
                $cacheKey = $jobData['cache_key'] ?? null;
    
                if ($cacheKey) {
                    $payloadJson = $this->redis->get($cacheKey);
                    $formData = json_decode($payloadJson, true);
    
                    if ($formData) {
                        $this->Ballard_form_model->insert($formData);
                        echo "Saved assessment for {$formData['name']}.\n";
                    } else {
                        echo "Failed to decode cached form data.\n";
                    }
                } else {
                    echo "Missing cache key in job data.\n";
                }
    
                // After successful processing, update status to completed
                $this->Job_logs_model->update_status_by_job_id($jobId, 'completed');
                echo "Job [$jobId] marked as completed.\n";
            }
    
            // Small delay to prevent CPU spike
            sleep(1);
        }
    }
}