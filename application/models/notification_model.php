<?php
/**
 * Description of notification_model
 *
 * @author Jarrod
 */
class notification_model extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->config('tables/notifications', TRUE);

        //initialize db tables data
        $this->tables = array_merge($this->config->item('tables', 'tables/notifications'));
    }

    /*
     * get_notification
     *
     * @return query result
     */
    public function get_notification($fields = FALSE, $options = FALSE) {
        if (!isset($fields['id'])) {
            return false;
        }

        $this->db->where('id', $fields['id']);

        $query = $this->db->get($this->tables['notifications']['notifications']);
        $row = $query->row_array();

        return $this->_format_notification($row, $options);
    }

    /*
         * get_notifications
         * 
         * @return query result
         */
    public function get_notifications($fields = FALSE, $options = FALSE) {
        if ($fields['sender_id']) {
            $this->db->where('sender_id', $fields['sender_id']);
        }

        if ($fields['receiver_id']) {
            $this->db->where('receiver_id', $fields['receiver_id']);
        }

        if ($fields['status']) {
            $this->db->where('status', $fields['status']);
        }

        $query = $this->db->get($this->tables['notifications']['notifications']);
        $_notification_ids = array();

        foreach ($query->result() as $row) {
            $_notification_ids[] = $row->id;
        }

        $results = array();

        foreach ($_notification_ids as $_notification_id) {
            $_data['id'] = $_notification_id;
            $_notification = $this->get_notification($_data);
            $results[] = $this->_format_notification($_notification);
        }

        return $results;
    }


    /*
     * create_notification
     *
     * @return bool
     */
    public function create_notification($fields = FALSE) {
        $this->db->set('created_on', 'NOW()', FALSE);
        return $this->db->insert($this->tables['notifications']['notifications'], $fields);
    }

    /*
     * update_notification
     *
     * @return bool
     */
    public function update_notification($fields = FALSE) {
        if (!isset($fields['id'])) {
            return false;
        }

        $this->db->where('id', $fields['id']);

        // Exisiting entry exist.
        return $this->db->update($this->tables['notifications']['notifications'], $fields);
    }

    /*
         * _format_notification
         * 
         * Returns array of fields needed
         */
    private function _format_notification($fields = FALSE, $options = FALSE) {
        if (!$fields) {
            return false;
        }

        $permission = array('id', 'sender_id', 'receiver_id', 'photo', 'location', 'remarks', 'status', 'created_on');

        $notification = null;
        foreach ($permission as $value) {
            $notification[$value] = isset($fields[$value]) ? $fields[$value] : '';
        }

        return $notification;
    }
}

?>
