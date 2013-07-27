<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

/**
 * Description of Fb_auth_library
 *
 * @author stiucsib86
 */
class Fb_Auth_library {

	public function __construct() {
		$this->load->config('facebook', TRUE);
		$this->load->helper('cookie');
		$this->load->library('ion_auth');
		$this->load->library('user_library');
		$this->load->library('session');
		$this->load->model('users_oauth_model');
		$this->load->model('users_profile_model');

		$this->facebook_config = $this->config->item('facebook_config', 'facebook');

		$this->facebook = new Facebook(array(
			'appId' => $this->facebook_config['app_id'],
			'secret' => $this->facebook_config['app_secret']
		));

		if (isset($_REQUEST['accessToken'])) {
			$this->facebook->setAccessToken($_REQUEST['accessToken']);
		}
		$this->facebook->getUser();
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

	public function try_fb_login() {

		if (!isset($_REQUEST['accessToken'])) {
			return FALSE;
		}

		$user_profile = $this->get_user_fb_details();

		if (!$user_profile || !$user_profile['email']) {
			throw new Exception('Error retrieving user Facebook profile.');
		}

		if (!$this->ion_auth->email_check($user_profile['email'])) {
			// Register and login user

			$generated_pwd = sha1($user_profile['email'] . date('Y-m-d'));

			$additional_data['first_name'] = $user_profile['first_name'];
			$additional_data['last_name'] = $user_profile['last_name'];

			$this->ion_auth->register($user_profile['username'], $generated_pwd, $user_profile['email'], $additional_data);

			$_new_user_flag = true;
		}

		if (!$this->user_library->facebook_login($user_profile['email'])) {
			throw new Exception('Error while logging in with Facebook');
		}

		try {
			// Update profile data if its a new user.
			if (isset($_new_user_flag) && $_new_user_flag) {
				if (isset($user_profile['gender'])) {
					$_profile_data['gender'] = $user_profile['gender'];
				}
				if (isset($user_profile['bio'])) {
					$_profile_data['about_me'] = $user_profile['bio'];
				}
				if (isset($user_profile['dob'])) {
					$_profile_data['dob'] = $user_profile['dob'];
				}
				$this->users_profile_model->update_profile($_profile_data);
			}
		} catch (Exception $e) {
			// Do nothing. We don't care if the updating fails. The user can
			// update this later.
		}

		// Finaly set user fb accessToken
		$this->session->set_userdata('fb_uid', $user_profile['fb_id']);
		$this->session->set_userdata('fb_accessToken', $_REQUEST['accessToken']);

		// @TODO Store user FB oauth details to DB
		$fb_oauth['accessToken'] = $_REQUEST['accessToken'];
		$fb_oauth['fb_uid'] = $user_profile['fb_id'];
		$this->users_oauth_model->update_oauth_fb($fb_oauth);

		return $this->facebook->getUser();
	}

	public function get_user_fb_details($fields = null) {

		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $this->facebook->api('/me');
		} catch (FacebookApiException $e) {
			$user_profile = FALSE;
		}

		if (!$user_profile) {
			return FALSE;
		}

		$user_profile['fb_id'] = $user_profile['id'];
		unset($user_profile['id']);

		return $user_profile;
	}

}

?>
