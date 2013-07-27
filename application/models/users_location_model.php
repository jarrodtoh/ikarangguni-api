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

    return $row;
  }

  public function get_location($fields = FALSE, $options = FALSE) {
    
    $this->_filters($fields, $options);
                
		$query = $this->db->get($this->tables['users']['locations']);
		$row = $query->row_array();
    
    return $this->_format_location($row, $options);
  }

  public function create_location($fields = FALSE, $options = FALSE) {
    
  }

  public function update_location($fields = FALSE, $options = FALSE) {

    if (!isset($fields['location_id'])) {
      
    } else {
      
    }
  }

  private function _filters($fields = FALSE, $options = FALSE) {

    if (isset($fields['user_id']) && is_numeric($fields['user_id'])) {
      $this->db->where('id', $fields['user_id']);
    }

    if (isset($fields['latitude'])) {
      $this->db->where('latittude > ', $fields['latitude'] - 1);
      $this->db->where('latittude < ', $fields['latitude'] + 1);
    }

    if (isset($fields['longitude'])) {
      $this->db->where('longitude > ', $fields['longitude'] - 1);
      $this->db->where('longitude < ', $fields['longitude'] + 1);
    }
  }

  private function _format_location($fields = FALSE, $options = FALSE) {
    return $fields;
  }

}

?>