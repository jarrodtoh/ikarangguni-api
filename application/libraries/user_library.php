<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 *
 * Description: Class for obtaining user
 *
 * Requirements: CodeIgniter Ion Auth http://github.com/benedmunds/CodeIgniter-Ion-Auth
 *
 */
class User_library {

	/**
	 * __construct
	 *
	 * */
	public function __construct() {
		$this->load->config('ion_auth', TRUE);
		$this->load->config('tables/users', TRUE);
		$this->load->helper('cookie');
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->model('users_profile_model');
		$this->load->model('users_oauth_model');

		//initialize db tables data
		//$this->tables = $this->config->item('tables', 'ion_auth');
		$this->tables = array_merge($this->config->item('tables', 'tables/users'));
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 */
	public function __get($var) {
		return get_instance()->$var;
	}

	public function get_user($fields = null) {

		if (isset($fields['user_id']) && is_numeric($fields['user_id'])) {
			$identity = $fields['user_id'];
		}

		if (isset($fields['id']) && is_numeric($fields['id'])) {
			$identity = $fields['user_id'];
		}

		if (!isset($identity) || !is_numeric($identity)) {
			$identity = null;
		}

		$user = $this->ion_auth->user($identity)->row_array();

		if ($user) {
			$user = $this->_format_user($user);
		}

		return $user;
	}

	public function get_user_by_fb_uid($fields = false) {

		if (!isset($fields['fb_uid']) || !is_numeric($fields['fb_uid'])) {
			throw new Exception("Invalid Facebook ID.");
		}

		$this->_set_filters($fields);

		$query = $this->db->get($this->tables['users']['oauth_fb']);
		$row = $query->row_array();
		if (isset($row['user_id']) && is_numeric($row['user_id'])) {
			$_fields['user_id'] = $row['user_id'];
			$row = $this->get_user($_fields);
		}
		return $row;
	}

	public function get_users($fields = false) {

		if (isset($fields['keywords'])) {
			if (!is_array($fields['keywords'])) {
				$fields['keywords'] = array($fields['keywords']);
			}

			foreach ($fields['keywords'] as $keyword) {
				$this->ion_auth->like('username', $keyword);
				$this->ion_auth->like('email', $keyword);
				$this->ion_auth->like('first_name', $keyword);
				$this->ion_auth->like('last_name', $keyword);
				$this->ion_auth->like('company', $keyword);
				$this->ion_auth->like('phone', $keyword);
			}
		}

		if (isset($fields['order_by'])) {
			$this->ion_auth->order_by($fields['order_by']);
		} else {
			$this->ion_auth->order_by('created_on');
		}

		$groups = array();
		if (isset($fields['groups'])) {
			if (!is_array($fields['groups'])) {
				$fields['groups'] = array($fields['groups']);
			}
			$groups = ($fields['groups']);
		}

		$temp = $this->ion_auth->users()->result_array($groups);

		if (!$temp) {
			return array();
		}

		foreach ($temp as $value) {
			$result[] = $this->_format_user($value);
		}
		return $result;
	}

	public function delete_user($fields = false) {

		if (!$this->ion_auth->is_admin()) {
			throw new Exception('You have no permission to execute this operation.');
		}

		if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
			throw new Exception('Invalid User ID.');
		}

		return $this->ion_auth->delete_user($fields['user_id']);
	}

	/**
	 * This is a function to login user by-passwing
	 *
	 * @param type $email
	 */
	public function facebook_login($email) {

		$query = $this->db->select('username, email, id, password, active, last_login')
				->where('email', $this->db->escape_str($email))
				->limit(1)
				->get($this->tables['users']['users']);


		if ($query->num_rows() === 1) {
			$user = $query->row();


			$session_data = array(
				'identity' => $user->email,
				'username' => $user->username,
				'email' => $user->email,
				'user_id' => $user->id, //everyone likes to overwrite id so we'll use user_id
				'old_last_login' => $user->last_login
			);

			$this->ion_auth->update_last_login($user->id);

			// Need to change this if email is not login identity.
			$this->ion_auth->clear_login_attempts($user->email);

			$this->session->set_userdata($session_data);

			$this->ion_auth->remember_user($user->id);

			return TRUE;
		}

		return FALSE;
	}

	public function _set_filters($fields = false) {

		if (isset($fields['fb_uid']) && is_numeric($fields['fb_uid'])) {
			$this->db->where('fb_uid', $fields['fb_uid']);
		}
	}

	public function _format_user($fields = FALSE, $options = FALSE) {

		if (!$fields) {
			return false;
		}

		$permission = array('id', 'user_id', 'username', 'email', 'first_name', 'last_name', 'company', 'phone', 'created_on', 'user_type');

		foreach ($permission as $value) {
			$user[$value] = isset($fields[$value]) ? $fields[$value] : '';
		}

		if (!isset($user['user_id']) || !is_numeric($user['user_id'])) {
			if (isset($fields['id']) && is_numeric($fields['id'])) {
				$user['user_id'] = $fields['id'];
			}
		}

		// Computed info
		$user['display_name'] = $fields['first_name'];
		if (isset($fields['last_name'])) {
			if (strlen($user['display_name']) > 0) {
				$user['display_name'] .= ', ' . $fields['last_name'];
			} else {
				$user['display_name'] .= $fields['last_name'];
			}
		}

		$fields['user_id'] = $fields['id'];

		// Get User Profile
		$_user_profile = $this->users_profile_model->get_profile($fields, $options);
		if (is_array($_user_profile)) {
			$user = array_merge($user, $_user_profile);
		}

		// Get User FB Id
		$_user_fb_oauth = $this->users_oauth_model->get_oauth_fb_by_user_id($fields, $options);
		if (is_array($_user_fb_oauth)) {
			if (isset($_user_fb_oauth['fb_uid'])) {
				// We return the FB_uid only
				$_temp['fb_uid'] = $_user_fb_oauth['fb_uid'];
				$user = array_merge($user, $_temp);
			}
		}

		return $user;
	}

}

?>
