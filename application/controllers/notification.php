<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/third_party/baidu-bcs/bcs.class.php';

class notification extends REST_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('ion_auth');
    $this->load->model('notification_model');

    $host = 'bcs.duapp.com'; //online
    $ak = 'D2ab2f4dfc84ab584ad06781bd13df85';
    $sk = '12c328b686aabd7a2bc06b18b7efe188';
    $this->bucket = 'karang-guni-photos';
    $this->upload_dir = "../";
    $this->object = '/a.txt';
    $this->fileUpload = './a.txt';
    $this->fileWriteTo = './a.' . time() . '.txt';
    $this->baidu_bcs = new BaiduBCS($ak, $sk, $host);

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
      if (!$this->ion_auth->logged_in()) {
        $response_error['data'] = 'Not logged in.';
        $this->response($response_error, 403);
        exit();
      }

      $fields = $this->_all_request_parameters;

      $user_id = $this->session->userdata('user_id');
      $fields['sender_id'] = $user_id;

      if (!isset($fields['receiver_id']) || !$fields['receiver_id']) {
        throw new Exception('Receiver not specified.');
      }

      if (!isset($fields['photo']) || !$fields['photo']) {
        throw new Exception('Photo not specified.');
      }

      if (!isset($fields['postal_code']) || !$fields['postal_code']) {
        throw new Exception('Address not specified.');
      }

      if (!isset($fields['unit_no']) || !$fields['unit_no']) {
        throw new Exception('Unit no# not specified.');
      }

      if (!isset($fields['latitude']) || !$fields['latitude']) {
        throw new Exception('Latitude not specified.');
      }

      if (!isset($fields['longitude']) || !$fields['longitude']) {
        throw new Exception('Longitude not specified.');
      }

      if (!isset($fields['postal_code']) || !$fields['postal_code']) {
        throw new Exception('Postal Code not specified.');
      }

      // Photo (Base64) conversion
      try {
        $filename = $fields['sender_id'] . '_' . $fields['receiver_id'] . '_' . time();
        $filepath = APPPATH . '/images/' . $filename . '.jpg';
        file_put_contents($filepath, base64_decode(str_replace('data:image/jpeg;base64,', '', $fields['photo'])));
        $opt = array();
        $opt['acl'] = BaiduBCS::BCS_SDK_ACL_TYPE_PUBLIC_WRITE;
        $opt[BaiduBCS::IMPORT_BCS_LOG_METHOD] = "bs_log";
        $opt['curlopts'] = array(CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 1800);
        $response = $this->baidu_bcs->create_object($this->bucket, '/' . $filename . '.jpg', $filepath, $opt);
        $url = $this->baidu_bcs->generate_get_object_url($this->bucket, '/' . $filename . '.jpg');
      } catch (Exception $e) {
        
      }

      $data['sender_id'] = $fields['sender_id'];
      $data['receiver_id'] = $fields['receiver_id'];
      $data['photo'] = isset($url) ? $url : '';
      $data['postal_code'] = $fields['postal_code'];
      $data['unit_no'] = $fields['unit_no'];
      $data['latitude'] = $fields['latitude'];
      $data['longitude'] = $fields['longitude'];
      $data['item_type'] = $fields['item_type'];
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
    } catch (Exception $e) {
      $error_response = array();
      $error_response['error'] = '[Error] ' . $e->getMessage();
      $error_response['code'] = 500;
      $this->response($error_response, 500);
    }
  }

  public function update_post() {
    try {
      if (!$this->ion_auth->logged_in()) {
        $response_error['data'] = false;
        $this->response($response_error, 403);
      }
      
      $fields = $this->_all_request_parameters;

      $results = $this->notification_model->update_notification($fields);

      if ($results) {
        $response_data['data'] = $results;
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

}

?>
