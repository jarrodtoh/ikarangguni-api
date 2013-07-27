<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class notification extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('notification_model');

        $this->_all_request_parameters = array_merge($this->input->get()? : array(), $this->args());
    }

    public function index_get() {
        try {
            $notification = $this->notification_model->get_notification($this->_all_request_parameters);

            if ($notification) {
                $response_data['data'] = $notification;
                $this->response($response_data, 200);
            } else {
                $response_error['data'] = false;
                $this->response($response_error, 500);
            }
        } catch (Exception $e) {
            $error_response = array();
            $error_response['error'] = '[Error] ' . $e->getMessage();
            $error_response['code'] = 500;
            $this->response($error_response, 500);
        }
    }

    public function index_post() {
        try {
            if ($this->ion_auth->logged_in()) {
                $fields = $this->_all_request_parameters;

                if (!isset($fields['sender_id'])) {
                    $error_response = array();
                    $error_response['error'] = 'Sender not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                if (!isset($fields['receiver_id'])) {
                    $error_response = array();
                    $error_response['error'] = 'Receiver not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                if (!isset($fields['photo'])) {
                    $error_response = array();
                    $error_response['error'] = 'Photo not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                if (!isset($fields['location'])) {
                    $error_response = array();
                    $error_response['error'] = 'Location not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                $data['sender_id'] = $fields['sender_id'];
                $data['receiver_id'] = $fields['receiver_id'];
                $data['photo'] = $fields['photo'];
                $data['location'] = $fields['location'];
                $data['remarks'] = isset($fields['remarks']) ? $fields['remarks'] : '';
                $data['status'] = 0;

                $results = $this->notification_model->create_notification($data);

                if ($results) {
                    $response_data['data'] = $results;
                    $this->response($response_data, 200);
                } else {
                    $response_error['data'] = false;
                    $this->response($response_error, 500);
                }
            }
            else {
                $response_error['data'] = false;
                $this->response($response_error, 403);
            }
        } catch (Exception $e) {
            $error_response = array();
            $error_response['error'] = '[Error] ' . $e->getMessage();
            $error_response['code'] = 500;
            $this->response($error_response, 500);
        }
    }

    public function edit_post() {
        try {
            if ($this->ion_auth->logged_in()) {
                $fields = $this->_all_request_parameters;

                $results = $this->notification_model->update_notification($fields);

                if ($results) {
                    $response_data['data'] = $results;
                    $this->response($response_data, 200);
                } else {
                    $response_error['data'] = false;
                    $this->response($response_error, 500);
                }
            }
            else {
                $response_error['data'] = false;
                $this->response($response_error, 403);
            }
        } catch (Exception $e) {
            $error_response = array();
            $error_response['error'] = '[Error] ' . $e->getMessage();
            $error_response['code'] = 500;
            $this->response($error_response, 500);
        }
    }
}

?>
