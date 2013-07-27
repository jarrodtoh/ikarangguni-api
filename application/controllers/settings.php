<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/third_party/baidu-bcs/bcs.class.php';

class settings extends REST_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('ion_auth');
    $this->load->model('settings_model');
    $this->load->model('users_location_model');

    $this->_all_request_parameters = array_merge($this->input->get()? : array(), $this->args());
  }

  public function index_get() {
    try {
      $notification = $this->settings_model->get_settings($this->_all_request_parameters);
      $response_data['data'] = $notification;
      $this->response($response_data, 200);
    } catch (Exception $e) {
      $error_response = array();
      $error_response['error'] = '[Error] ' . $e->getMessage();
      $error_response['code'] = 500;
      $this->response($error_response, 500);
    }
  }

  public function location_post() {
    if (isset($this->_all_request_parameters['location_id']) && is_numeric($this->_all_request_parameters['location_id'])) {
      $response_data['data'] = $this->users_location_model->update_location($this->_all_request_parameters);
    } else {
      $response_data['data'] = $this->users_location_model->create_location($this->_all_request_parameters);
    }

    $this->response($response_data, 200);
  }

  public function remove_location_post() {
    $response_data['data'] = $this->users_location_model->delete_location($this->_all_request_parameters);
    $this->response($response_data, 200);
  }

}

?>
