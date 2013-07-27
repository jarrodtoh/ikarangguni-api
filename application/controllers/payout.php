<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class payout extends REST_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->model('notification_model');

    $this->_all_request_parameters = array_merge($this->input->get()? : array(), $this->args());
  }

  public function index_get() {
    try {
      $fields = $this->_all_request_parameters;

      if (!isset($fields['sender_id'])) {
        $fields['sender_id'] = $this->session->userdata('user_id');
      }

      $results['balance'] = $this->notification_model->get_balance($fields);
      $results['history'] = $this->notification_model->get_payout_history($fields);

      $response_data['data'] = $results;
      $this->response($response_data, 200);
    } catch (Exception $e) {
      $error_response = array();
      $error_response['error'] = '[Error] ' . $e->getMessage();
      $error_response['code'] = 404;
      $this->response($error_response, 404);
    }
  }

}

?>
