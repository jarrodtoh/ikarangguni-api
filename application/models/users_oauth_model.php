<?php

/**
 * Description of users_oauth_model
 *
 * @author stiucsib86
 */
class users_oauth_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->TAG = "users_oauth_model";

		$this->load->database();
		$this->load->config('tables/users', TRUE);
		$this->load->library('session');

		//initialize db tables data
		$this->tables = array_merge($this->config->item('tables', 'tables/users'));
	}

	public function get_oauth_fb($fields = FALSE, $options = FALSE) {

		if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
			if (!is_numeric($this->session->userdata('user_id'))) {
				throw new Exception("Error. User is not authorized.");
			} else {
				$fields['user_id'] = $this->session->userdata('user_id');
			}
		}

		$this->_set_filters($fields);

		$query = $this->db->get($this->tables['users']['oauth_fb']);
		return $query->row_array();
	}

	public function get_oauth_fb_by_user_id($fields = FALSE, $options = FALSE) {
		if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
			return false;
		}
		return $this->get_oauth_fb($fields, $options);
	}

	public function get_oauth_fbs($fields = FALSE, $options = FALSE) {

		if (!isset($fields['fb_uids'])) {
			throw new Exception('Invalid Facebook User IDs');
		}

		$this->_set_filters($fields);

		$query = $this->db->get($this->tables['users']['oauth_fb']);
		return $query->result_array();
	}

	public function update_oauth_fb($fields = FALSE, $options = FALSE) {

		if (!is_numeric($this->session->userdata('user_id'))) {
			throw new Exception("Error. User is not authorized.");
		}

		$_user_oauth_fb = $this->get_oauth_fb($fields, $options);

		$data['accessToken'] = $fields['accessToken'];

		if ($_user_oauth_fb) {
			// Exisiting entry exist.
			$this->db->where('user_id', $this->session->userdata('user_id'));
			return $this->db->update($this->tables['users']['oauth_fb'], $data);
		} else {
			$this->db->set('user_id', $this->session->userdata('user_id'));
			$this->db->set('created_on', 'NOW()', FALSE);
			$this->db->set('fb_uid', $fields['fb_uid']);
			return $this->db->insert($this->tables['users']['oauth_fb'], $data);
		}

		return TRUE;
	}

	private function _set_filters($fields = FALSE, $options = FALSE) {

		if (isset($fields['user_id'])) {
			if (is_numeric($fields['user_id'])) {
				$this->db->where('user_id', $fields['user_id']);
			}
		}

		if (isset($fields['fb_uids'])) {
			if (!array($fields['fb_uids'])) {
				$fields['fb_uids'] = array($fields['fb_uids']);
			}
			foreach ($fields['fb_uids'] as $fb_uid) {
				if (is_numeric($fb_uid)) {
					$this->db->or_where('fb_uid', $fb_uid);
				}
			}
		}
	}

}

?>
