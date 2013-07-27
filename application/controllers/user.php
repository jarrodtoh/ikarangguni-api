<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->lang->load('main', 'english');
		$this->load->library('ion_auth');
		$this->load->library('user_library');
		$this->load->model('users_profile_model');

        if ($this->input->get()) {
            $input_array = $this->input->get();
        }
        else {
            $input_array = array();
        }
		$this->_all_request_parameters = array_merge($input_array, $this->args());
	}

	public function index_get() {
		try {

			if (isset($this->_all_request_parameters['fb_uid'])) {
				$user = $this->user_library->get_user_by_fb_uid($this->_all_request_parameters);
			} else {
				$user = $this->user_library->get_user($this->_all_request_parameters);
			}

			if ($user) {
				$this->response($user, 200); // 200 being the HTTP response code
			} else {
				$this->response(array('error' => 'User could not be found.'), 404);
			}
		} catch (Exception $e) {
			$error_response = array();
			$error_response['error'] = '[Error] ' . $e->getMessage();
			$error_response['code'] = 404;
			$this->response($error_response, 404);
		}
	}

	public function index_post() {
		try {

			// Format interest
			if (isset($this->_all_request_parameters['interest'])) {
				if (!is_array($this->_all_request_parameters['interest'])) {
					$this->_all_request_parameters['interest'] = explode(',', $this->_all_request_parameters['interest']);
				}
			}

			$results = $this->users_profile_model->update_profile($this->_all_request_parameters);

			if ($results) {
				$this->response($results, 200); // 200 being the HTTP response code
			} else {
				$this->response(array('error' => 'Error updating user info.'), 404);
			}
		} catch (Exception $e) {
			$error_response = array();
			$error_response['error'] = '[Error] ' . $e->getMessage();
			$error_response['code'] = 404;
			$this->response($error_response, 404);
		}
	}

	public function index_delete() {
		try {

			$user = $this->user_library->delete_user($this->_all_request_parameters);

			if ($user) {
				$this->response($user, 200); // 200 being the HTTP response code
			} else {
				$this->response(array('error' => 'User could not be deleted.'), 404);
			}
		} catch (Exception $e) {
			$error_response = array();
			$error_response['error'] = '[Error] ' . $e->getMessage();
			$error_response['code'] = 404;
			$this->response($error_response, 404);
		}
	}

	public function update_user_get() {
		$this->index_post();
	}

}

/* End of file user.php */
/* Location: ./application/controllers/api/user.php */