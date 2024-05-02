<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private function hash_password($password){
       return password_hash($password, PASSWORD_BCRYPT);
    }
    public function getCsrfData() {
        $data = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        return $data;
    }
    public function insertActivityLog ($message) {
        if (isset($this->session->user_id)) {
            $activity_log = array(
                'user_id'=>$this->session->user_id, 
                'message_log'=>$message, 
                'ip_address'=>$this->input->ip_address(), 
                'platform'=>$this->agent->platform(), 
                'browser'=>$this->agent->browser(), 
                'created_at'=>date('Y-m-d H:i:s')
            ); 

            $this->db->INSERT('activity_logs_tbl', $activity_log);
        }
    }
    public function getUserData () {
        if (isset($this->session->user_id)) {
            return $this->db->WHERE('user_id', $this->session->user_id)->GET('users_tbl')->row_array();
        }
    }
    public function newWebsiteVisits() {
		if (!isset($this->session->website_views) && !empty($this->agent->browser())) {
		    $view_id = $this->generateWebsiteVisitorID();
			$this->session->set_tempdata('website_views', $view_id, 86400); /* set session visitor views for 24 hours then reset after */
			$data = array(
				'views_id'=>$view_id,
				'ip_address'=>$this->input->ip_address(),
				'platform'=>$this->agent->platform(), 
				'browser'=>$this->agent->browser(),
				'created_at'=>date('Y-m-d H:i:s'),
			);
			$this->db->INSERT('website_visits_tbl',$data);
		}
		return true;
	}
    public function generateWebsiteVisitorID($length = 15) {
		$characters = '0123456789abcdef';
	   $charactersLength = strlen($characters);
	   $views_id = '';
	   for ($i = 0; $i < $length; $i++) {
	      $views_id .= $characters[rand(0, $charactersLength - 1)];
	   }
    	return $views_id;
   }
   public function checkCookie($cookie){
    if ($this->agent->is_mobile()) {
        return $this->db->WHERE('mobile_rem_token', $cookie)
        ->GET('users_tbl')->row_array();
    }
    else{
        return $this->db->WHERE('web_rem_token', $cookie)
        ->GET('users_tbl')->row_array();
    }
}










    public function insertActivityLogResetPass ($message) {
        if (isset($this->session->recovery_user_id)) {
            $activity_log = array(
                'user_id'=>$this->session->recovery_user_id, 
                'message_log'=>$message, 
                'ip_address'=>$this->input->ip_address(), 
                'platform'=>$this->agent->platform(), 
                'browser'=>$this->agent->browser(), 
                'created_at'=>date('Y-m-d H:i:s')
            ); 

            $this->db->INSERT('activity_logs_tbl', $activity_log);
        }
    }
    public function generateUserID ($id, $length = 9) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $temp_id = '';
        for ($i = 0; $i < $length; $i++) {
            $temp_id .= $characters[rand(0, $charactersLength - 1)];
        }
        $rand = rand(1, 100);
        $user_id = '100'.$rand.$id.$temp_id;
        $dataArr = array('user_id'=>$user_id);
        $this->db->WHERE('id',$id)->UPDATE('users_tbl',$dataArr);
    }
    
    public function getData() {
        if (isset($this->session->admin) || isset($this->session->staff) || isset($this->session->sys_admin)) {
            $user_data = $this->db->SELECT('user_id, fname, lname, email_address, user_type, mobile_number, username')->WHERE('user_id', $this->input->get('id'))->GET('users_tbl')->row_array();
            return $user_data;
        }
    }
}

