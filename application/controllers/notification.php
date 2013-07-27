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
        $this->fileWriteTo = './a.' . time () . '.txt';
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

                if (!isset($fields['address'])) {
                    $error_response = array();
                    $error_response['error'] = 'Address not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                if (!isset($fields['coordinates'])) {
                    $error_response = array();
                    $error_response['error'] = 'Coordinates not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }

                if (!isset($fields['postal_code'])) {
                    $error_response = array();
                    $error_response['error'] = 'Postal Code not specified.';
                    $error_response['code'] = 500;
                    $this->response($error_response, 500);
                }



                $data['sender_id'] = $fields['sender_id'];
                $data['receiver_id'] = $fields['receiver_id'];

                // Photo (Base64) conversion
                $filename = $fields['sender_id'] . '_' . $fields['receiver_id'] . '_' . time();
                file_put_contents(APPPATH . '/images/'.$filename.'.jpg', base64_decode(
                    str_replace('data:image/jpeg;base64,', '', $fields['photo'])
                ));
                $opt = array();
                $opt['acl'] = BaiduBCS::BCS_SDK_ACL_TYPE_PUBLIC_WRITE;
                $opt[BaiduBCS::IMPORT_BCS_LOG_METHOD] = "bs_log";
                $opt['curlopts'] = array (
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => 1800 );
                $response = $this->baidu_bcs->create_object($this->bucket, '/'.$filename.'.jpg', APPPATH . '/images/'.$filename.'.jpg', $opt);
                $url = $this->baidu_bcs->generate_get_object_url($this->bucket, '/'.$filename.'.jpg');

                $data['photo'] = $url;
                $data['address'] = $fields['address'];
                $data['coordinates'] = $fields['coordinates'];
                $data['postal_code'] = $fields['postal_code'];
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
