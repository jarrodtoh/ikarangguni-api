<?php

/**
 * Description of users_oauth_model
 *
 * @author stiucsib86
 */
class users_location_model extends CI_Model {

  public function __construct() {
    parent::__construct();

    $this->TAG = "users_location_model";

    $this->load->database();
    $this->load->config('tables/users', TRUE);
    $this->load->library('session');

    //initialize db tables data
    $this->tables = array_merge($this->config->item('tables', 'tables/users'));
  }

  public function get_locations($fields = FALSE, $options = FALSE) {

    $this->_filters($fields, $options);

    $query = $this->db->get($this->tables['users']['locations']);
    $result = $query->result_array();

    foreach ($result as $key => $row) {
      $result[$key] = $this->_format_location($row, $options);
    }

    return $result;
  }

  public function get_location($fields = FALSE, $options = FALSE) {

    if (!isset($fields['location_id']) || !is_numeric($fields['location_id'])) {
      return FALSE;
    }

    $this->_filters($fields, $options);

    $query = $this->db->get($this->tables['users']['locations']);
    $row = $query->row_array();

    return $this->_format_location($row, $options);
  }

  public function create_location($fields = FALSE, $options = FALSE) {

    if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
      $fields['user_id'] = $this->session->userdata('user_id');
    }

    $data['user_id'] = $fields['user_id'];

    if (isset($fields['latitude'])) {
      $data['latitude'] = $fields['latitude'];
    }

    if (isset($fields['longitude'])) {
      $data['longitude'] = $fields['longitude'];
    }

    $this->db->insert($this->tables['users']['locations'], $data);
    $_fields['location_id'] = $this->db->insert_id();

    return $this->get_location($_fields);
  }

  public function update_location($fields = FALSE, $options = FALSE) {

    if (!isset($fields['location_id']) || !is_numeric($fields['location_id'])) {
      throw new Exception('Invalid location ID.');
    }

    if (isset($fields['latitude'])) {
      $data['latitude'] = $fields['latitude'];
    }

    if (isset($fields['longitude'])) {
      $data['longitude'] = $fields['longitude'];
    }

    $this->db->where('location_id', $fields['location_id']);
    return $this->db->update($this->tables['users']['locations'], $data);
  }

  public function delete_location($fields = FALSE, $options = FALSE) {

    if (!isset($fields['location_id']) || !is_numeric($fields['location_id'])) {
      throw new Exception('Invalid location ID.');
    }

    $this->db->where('location_id', $fields['location_id']);
    return $this->db->delete($this->tables['users']['locations']);
  }

  private function _filters($fields = FALSE, $options = FALSE) {

    if (isset($fields['user_id']) && is_numeric($fields['user_id'])) {
      $this->db->where('user_id', $fields['user_id']);
    }

    if (isset($fields['location_id']) && is_numeric($fields['location_id'])) {
      $this->db->where('location_id', $fields['location_id']);
    }
  }

  private function _format_location($fields = FALSE, $options = FALSE) {
    return $fields;
  }

}

?>