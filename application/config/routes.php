<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['/'] = 'vue2';
$route['test'] = 'vue2';
$route['audit-trail'] = 'vue2';


$route['api/v1/get/dictionary'] = 'api/v1/Audit_trail_controller/get_all_dictionary';
$route['api/v1/get/audit_trails'] = 'api/v1/Audit_trail_controller/get_all_audit_trails';
$route['api/v1/get/user_id'] = 'api/v1/Audit_trail_controller/get_user_audits_by_id';








