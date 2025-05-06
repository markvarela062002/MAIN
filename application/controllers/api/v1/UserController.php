<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UserController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model'); // Load the model
    }

    public function index()
    {
        $patients = $this->UserModel->getSelectedColumns(); // Fetch all patient data
        
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($patients)); // Return JSON response
    }
}
