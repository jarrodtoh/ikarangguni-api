<?php

/**
 * Description of users_oauth_model
 *
 * @author stiucsib86
 */
class company_model extends CI_Model {

  public function __construct() {
    parent::__construct();

    $this->TAG = "company_model";

    $this->load->database();
    $this->load->config('tables/users', TRUE);
    $this->load->library('session');
    $this->load->model('users_location_model');

    //initialize db tables data
    $this->tables = array_merge($this->config->item('tables', 'tables/users'));
  }

  public function get_companies($fields = FALSE, $options = FALSE) {

    $this->_filters($fields, $options);

    $this->db->select('*');
    $this->db->from($this->tables['users']['users']);
    $this->db->join($this->tables['users']['locations'], $this->tables['users']['users'] . '.id = ' . $this->tables['users']['locations'] . '.user_id', 'left');
    $this->db->group_by("id");

    $query = $this->db->get();
    $result = $query->result_array();

    foreach ($result as $key => $row) {
      $result[$key] = $this->_format_company($row, $options);
    }

    return $result;
  }

  private function _filters($fields = FALSE, $options = FALSE) {

    if (isset($fields['user_id']) && is_numeric($fields['user_id'])) {
      $this->db->where('id', $fields['user_id']);
    }
    
    if (isset($fields['latitude'])) {
      $this->db->where('latitude > ', $fields['latitude'] - 1);
      $this->db->where('latitude < ', $fields['latitude'] + 1);
    }

    if (isset($fields['longitude'])) {
      $this->db->where('longitude > ', $fields['longitude'] - 1);
      $this->db->where('longitude < ', $fields['longitude'] + 1);
    }

    $this->db->where('user_type', 1);
  }

  private function _format_company($fields = FALSE, $options = FALSE) {
    
    $data['id'] = $fields['id'];
    $data['email'] = $fields['email'];
    $data['first_name'] = $fields['first_name'];
    $data['last_name'] = $fields['last_name'];
    $data['phone'] = $fields['phone'];
    
    return $data;
  }

}

?>