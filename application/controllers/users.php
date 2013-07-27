<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller {

	public function __construct() {
		parent::__construct();
		$this->lang->load('main', 'english');
		$this->load->library('ion_auth');
		$this->load->library('user_library');

        if ($this->input->get()) {
            $input_array = $this->input->get();
        }
        else {
            $input_array = array();
        }
        $this->_all_request_parameters = array_merge($input_array, $this->args());
	}

	public function index_get() {

		if (isset($this->_all_request_parameters['user_ids'])) {
			if (is_array($this->_all_request_parameters['user_ids'])) {
				// Do nothing
			} elseif (strstr($this->_all_request_parameters['user_ids'], '[')) {
				//is an JSON array
				$this->_all_request_parameters['user_ids'] = json_decode($this->_all_request_parameters['user_ids']);
			} elseif (strstr($this->_all_request_parameters['user_ids'], ',')) {
				// might be comma separated
				$this->_all_request_parameters['user_ids'] = explode(',', $this->_all_request_parameters['user_ids']);
			}
		}

		if (isset($this->_all_request_parameters['fb_uids'])) {
			if (is_array($this->_all_request_parameters['fb_uids'])) {
				// Do nothing
			} elseif (strstr($this->_all_request_parameters['fb_uids'], '[')) {
				//is an JSON array
				$this->_all_request_parameters['fb_uids'] = json_decode($this->_all_request_parameters['fb_uids']);
			} elseif (strstr($this->_all_request_parameters['user_ids'], ',')) {
				// might be comma separated
				$this->_all_request_parameters['fb_uids'] = explode(',', $this->_all_request_parameters['fb_uids']);
			}
		}

		// Get users based on user_id
		if (isset($this->_all_request_parameters['user_ids']) && is_array($this->_all_request_parameters['user_ids'])) {
			$_users_based_on_user_ids = $this->user_library->get_users($this->_all_request_parameters);
		}

		// Get users based on fb_uid
		if (isset($this->_all_request_parameters['fb_uids']) && is_array($this->_all_request_parameters['fb_uids'])) {
			foreach ($this->_all_request_parameters['fb_uids'] as $fb_uid) {
				if (is_numeric($fb_uid)) {
					$_fields['fb_uid'] = $fb_uid;
					$_user_from_fb = $this->user_library->get_user_by_fb_uid($_fields);
					if ($_user_from_fb) {
						$_users_based_on_fb_uids[] = $_user_from_fb;
					}
				}
			}
		}

		// Merge the results
		$users = array();
		if (isset($_users_based_on_user_ids) && is_array($_users_based_on_user_ids)) {
			$users = array_merge($users, $_users_based_on_user_ids);
		}
		if (isset($_users_based_on_fb_uids) && is_array($_users_based_on_fb_uids)) {
			$users = array_merge($users, $_users_based_on_fb_uids);
		}



		if ($users) {
			$this->response($users, 200); // 200 being the HTTP response code
		} else {
			$this->response(array('error' => 'User(s) could not be found'), 404);
		}
	}

    public function type_get() {
        try {
            $fields = $this->_all_request_parameters;
            $results = $this->user_library->get_user_by_type($fields);

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

/* End of file users.php */
/* Location: ./application/controllers/api/users.php */