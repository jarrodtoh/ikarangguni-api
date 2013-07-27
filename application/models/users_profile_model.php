<?php

/**
 * Description of users_oauth_model
 *
 * @author stiucsib86
 */
class users_profile_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->TAG = "users_profile_model";

		$this->load->database();
		$this->load->config('tables/users', TRUE);
		$this->load->library('session');

		//initialize db tables data
		$this->tables = array_merge($this->config->item('tables', 'tables/users'));
	}

	public function get_profile($fields = FALSE, $options = FALSE) {

		if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
			return false;
		}

		$this->_set_filters($fields);

                if ($fields['user_id']) {
                        $this->db->where('user_id', $fields['user_id']);
                }
                
                
		$query = $this->db->get($this->tables['users']['profile']);
		$row = $query->row_array();

		return $this->_format_user($row, $options);
	}

	public function update_profile($fields = FALSE, $options = FALSE) {

		if (!is_numeric($this->session->userdata('user_id'))) {
			throw new Exception("Error. User is not authorized.");
		}

		$fields['user_id'] = $this->session->userdata('user_id');

		if (isset($fields['gender'])) {
			switch (strtolower($fields['gender'])) {
				case 'male':
					$data['gender'] = 'male';
					break;
				case 'female':
					$data['gender'] = 'female';
					break;
				default:
					break;
			}
		}

		if (isset($fields['dob'])) {
			$data['dob'] = date_format(date_create($fields['dob']), "Y-m-d H:i:s");
		}

		if (isset($fields['interest']) && is_array($fields['interest'])) {
			$data['interest'] = json_encode($fields['interest']);
		}

		if (isset($fields['about_me'])) {
			$data['about_me'] = $fields['about_me'];
		}

		if (!isset($data) || empty($data)) {
			return false;
		}

		$_user_profile = $this->get_profile($fields, $options);

		if ($_user_profile) {
			// Exisiting entry exist.
			$this->db->where('user_id', $this->session->userdata('user_id'));
			return $this->db->update($this->tables['users']['profile'], $data);
		} else {
			$this->db->set('user_id', $this->session->userdata('user_id'));
			return $this->db->insert($this->tables['users']['profile'], $data);
		}

		return TRUE;
	}

	private function _set_filters($fields = FALSE, $options = FALSE) {
		if (isset($fields['user_id'])) {
			if (is_numeric($fields['user_id'])) {
				$this->db->where('user_id', $fields['user_id']);
			}
		}
	}

	private function _format_user($fields = FALSE, $options = FALSE) {

		if (!$fields) {
			return false;
		}

		if (isset($fields['interest'])) {
			$fields['interest'] = json_decode($fields['interest']);
			if (is_array($fields['interest'])) {
				$fields['interest_text'] = implode(',', $fields['interest']);
			} else {
				$fields['interest_text'] = '';
			}
		}

		return $fields;
	}

}

?>