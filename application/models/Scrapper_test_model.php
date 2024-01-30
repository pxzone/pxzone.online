<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scrapper_test_model extends CI_Model {
    public function getAlttMsgID(){
        $query = $this->db->SELECT('msg_id, attempt')->LIMIT(1)->ORDER_BY('id','desc')->GET('altt_msg_id_tbl')->row_array();
        return $query;
    }
    public function insertNewMsgID($msg_id){
        $new_msg_id = $msg_id + 1;
        $data_arr = array(
            'msg_id' => $new_msg_id, 
            'created_at'=>date('Y-m-d H:i:s')
        );
        $this->db->INSERT('altt_msg_id_tbl', $data_arr);
    }
    public function updateMsgIdAttempt($msg_id, $attempt){
        $data_arr = array(
            'attempt' =>  $attempt+1, 
            'updated_at'=>date('Y-m-d H:i:s')
        );
        $this->db->WHERE('msg_id', $msg_id)->UPDATE('altt_msg_id_tbl', $data_arr);
    }
    public function insertSystemActivityLog ($message) {
        $msg_log = array(
            'msg_log'=>$message, 
            'ip_address'=>$this->input->ip_address(), 
            'created_at'=>date('Y-m-d H:i:s')
        ); 
        $this->db->INSERT('altt_syslog_tbl', $msg_log);
    }
}