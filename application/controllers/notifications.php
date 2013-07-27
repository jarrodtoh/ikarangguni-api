<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class notifications extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('notification_model');

        $this->_all_request_parameters = array_merge($this->input->get()? : array(), $this->args());
    }

    public function index_get() {
        try {
            $fields = $this->_all_request_parameters;
            $results = $this->notification_model->get_notifications($fields);

            if ($results) {
                $response_data['data'] = $results;
                $this->response($response_data, 200);
            }
            else {
                $response_error['data'] = false;
                $this->response($response_error, 500);
            }

        }
        catch (Exception $e) {
            $error_response = array();
            $error_response['error'] = '[Error] ' . $e->getMessage();
            $error_response['code'] = 404;
            $this->response($error_response, 404);
        }
    }
}

?>