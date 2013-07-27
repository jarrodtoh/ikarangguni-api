<?php

/**
 * Description of notification_model
 *
 * @author Jarrod
 */
class settings_model extends CI_Model {

  public function __construct() {
    parent::__construct();

    $this->load->database();
    $this->load->config('tables/users', TRUE);
    $this->load->model('users_location_model');

    //initialize db tables data
    $this->tables = array_merge($this->config->item('tables', 'tables/users'));
  }

  /*
   * get_notification
   *
   * @return query result
   */

  public function get_settings($fields = FALSE, $options = FALSE) {

    if (!isset($fields['user_id']) || !is_numeric($fields['user_id'])) {
      $fields['user_id'] = $this->session->userdata('user_id');
    }

    $settings['locations'] = $this->users_location_model->get_locations($fields);

    return $settings;
  }

  private function _set_filter($fields = FALSE, $options = FALSE) {

    if (isset($fields['user_id']) && is_numeric($fields['user_id'])) {
      $this->db->where('user_id', $fields['user_id']);
    }
    
  }

}

?>
