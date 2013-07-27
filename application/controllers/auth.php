<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Auth extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('user_library');
		$this->load->library('fb_auth_library');
	}

	public function index_get() {

		if (!$this->ion_auth->logged_in()) {
			if (isset($_REQUEST['accessToken'])) {
				$this->fb_auth_library->try_fb_login();
			}
		}

		if (!$this->ion_auth->logged_in()) {
			$this->response(array('error' => 'User not logged in.'), 500);
			exit();
		}

		$response['status'] = true;
		$response['user'] = $this->user_library->get_user();
		$response['is_admin'] = $this->ion_auth->is_admin();

		if (!$response['user']) {
			// Invalid user, so logout.
			$this->ion_auth->logout();
			throw new Exception('Invalid user.');
		}

		if ($response) {
			$this->response($response, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => 'User could not be found'), 500);
		}
	}

	public function login_post() {

		// @TODO Correct this flow.
		if (!$this->ion_auth->logged_in()) {
			if (isset($_REQUEST['accessToken'])) {
				$this->fb_auth_library->try_fb_login();
			}
		}

		$remember = (bool) $this->input->post('remember');

		if ($this->ion_auth->logged_in()) {
			$response_data['data'] = $this->user_library->get_user();
			$response_data['message'] = "Already logged in.";
			$this->response($response_data, 200);
		}

		if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
			$response_data['data'] = $this->user_library->get_user();
			$response_data['message'] = $this->ion_auth->messages();
			$this->response($response_data, 200);
		} else {
			$response_error['data'] = false;
			$response_error['message'] = $this->ion_auth->errors();
			$this->response($response_error, 500);
		}
	}

	public function logout_get() {
		$this->logout_post();
	}

	public function logout_post() {

		$logout = $this->ion_auth->logout();

		if ($logout) {
			$response_data['data'] = $logout;
			$response_data['message'] = $this->ion_auth->messages();
			$this->response($response_data, 200);
		} else {
			$response_error['data'] = false;
			$response_error['message'] = $this->ion_auth->errors();
			$this->response($response_error, 500);
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */