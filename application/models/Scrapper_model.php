<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scrapper_model extends CI_Model {
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
        
        // $check = $this->db->WHERE('msg_id', $new_msg_id)->GET('altt_msg_id_tbl')->num_rows();
        // if($check <= 0){
            
        // }
        // else{
            
        // }
        
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
    public function scrapeLoginPage(){
        $forumUrl = 'https://www.altcoinstalks.com/index.php?action=login';
        $ch = curl_init($forumUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            $message = 'Curl error: ' . curl_error($ch);
            $this->insertSystemActivityLog($message);
            exit;
        }
        curl_close($ch);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        if( !@$dom->loadHTML($html)){
            $message = libxml_get_last_error();
            $this->insertSystemActivityLog($message);
            libxml_clear_errors();
        }
        else{
            $xpath = new DOMXPath($dom);
            $hiddenInputs = $xpath->query('//input[@type="hidden"]');
            $loginFormAction = $xpath->query('//form[@id="frmLogin"]')->item(0)->getAttribute('action');
            $secondHiddenInput = $hiddenInputs->item(1);
            $response = array(
                'name'=>$secondHiddenInput->getAttribute('name'),
                'value'=>$secondHiddenInput->getAttribute('value'),
                'login_url'=>$loginFormAction,
                'session_id'=>substr($loginFormAction,50, -14),
            );
            return $response;

        }
    }
    public function loginForum($login_page_data){
        $auth = $this->api_auth->authKeys();
        $loginUrl = 'https://www.altcoinstalks.com/index.php?action=login2';
        $username = $auth['username'];
        $password = $auth['password'];
        $ch = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'user' => $username,
            'passwrd' => $password,
            'cookielength' => '600', 
            'hash_passwrd' => '',  
            $login_page_data['name'] => $login_page_data['value'],
            'PHPSESSID' => $login_page_data['session_id'],
            'login' => 'Login',
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // echo 'Curl error during login: ' . curl_error($ch);
            exit;
        }
        curl_close($ch);
        if (strpos($response, 'Welcome,') !== false) {
            // return $response;
            return true;
        } else {
            return false;
        }
    }
    public function scrapeTopicData ($topic_url) {
        $login_page_data = $this->scrapeLoginPage();
        $login_forum = $this->loginForum($login_page_data);
        if($login_forum){
            return $this->scrapeForumTopicData($login_page_data, $topic_url);
        }
        else{
            return false;
        }
    }
    public function scrapeForumTopicData($login_page_data, $topic_url)
    {
        $user = "";
        $subjectContent = "";
        $postContent = "";
        $subject_url = "";

        $ch = curl_init($topic_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error during request: ' . curl_error($ch);
            exit;
        }
        curl_close($ch);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $titleTag = $xpath->query('//title')->item(0);
        $title = $titleTag->nodeValue;
        return $title;
    }
    public function getUserKarmaCount($uid, $chat_id){
        $login_page_data = $this->scrapeLoginPage();
        $login_forum = $this->loginForum($login_page_data);
        if($login_forum){
            $this->scrapeUserProfile($login_page_data, $uid, $chat_id);
        }
        else{
            echo 'error';
        }
    }
    public function scrapeUserProfile($login_page_data, $uid, $chat_id)
    {
                
        $forum_url = "https://www.altcoinstalks.com/index.php?action=profile;u=$uid";
        $ch = curl_init($forum_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            $message = 'Curl error during request: ' . curl_error($ch);
            $this->insertSystemActivityLog($message);
            exit;
        }
        curl_close($ch);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        # GET KARMA
        $karma_element = $xpath->query('//dt[text()="Karma: "]/following-sibling::dd')->item(0);
        if ($karma_element) {
            $karma = (int)trim($karma_element->nodeValue);
        }
        else{$karma = 0;}
        $data_arr = array('karma'=> $karma);
        $this->db->WHERE('chat_id', $chat_id)->UPDATE('telegram_bot_tbl',$data_arr);
    }
    public function insertNewBoardData($board_id, $board_name) {
        $check = $this->db->WHERE('board_id', $board_id)->WHERE('board_name', $board_name)->GET('altt_boards_tbl')->num_rows();
        if($check  <= 0){
            $data_arr = array(
                'board_id'=>$board_id,
                'board_name'=>$board_name,
                'created_at'=>date('Y-m-d H:i:s'),
            );
            $this->db->INSERT('altt_boards_tbl', $data_arr);
        }
    }
    // public function scrapeBoardData($board_id) {
    //     $forum_url = "https://www.altcoinstalks.com/index.php?board=".$board_id;
    //     $ch = curl_init($forum_url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
    //     $html = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //         $message = 'Curl error during request: ' . curl_error($ch);
    //         $this->insertSystemActivityLog($message);
    //         exit;
    //     }
    //     curl_close($ch);
    //     $dom = new DOMDocument();
    //     @$dom->loadHTML($html);
    //     $xpath = new DOMXPath($dom);
            
    //     $title_tag = $xpath->query('//title')->item(0);
    //     $title = $title_tag->nodeValue;
    //     return $title;
    // }
    public function getUserDatabyUID($uid){
        return $this->db->SELECT('username, updated_at')
            ->WHERE('uid', $uid)
            ->GET('altt_users_tbl')->row_array();
    }
    public function insertNewProfile($data){
        $this->db->INSERT('altt_users_tbl', $data);
    }
    public function updateUserProfile($data, $uid){
        $this->db->WHERE('uid', $uid)->UPDATE('altt_users_tbl', $data);
    }
    public function getAlttUsersDatabyUsername($username){
        return $this->db->SELECT('karma, username')
        ->WHERE('username', $username)->GET('altt_users_tbl')->row_array();
    }
    public function getMultipleUserData(){
        $date_range = array('updated_at >'=>date('Y-m-d 00:00:00', strtotime('-2 days -7 hours')), 'updated_at <'=> date('Y-m-d H:i:s'));
        return $this->db->SELECT('uid, username, karma')
           ->WHERE($date_range)
           ->WHERE('status', 'active')
           ->ORDER_BY('updated_at', 'desc')
           ->GET('altt_users_tbl ')->result_array();
    }
    public function saveScrapedData($data){
        if(!empty($data['msg_id'])){
            $data_arr['msg_id'] = $data['msg_id'];
            $data_arr['topic_id'] = $data['topic_id'];
            $data_arr['board_id'] = $data['board_id'];
            $data_arr['username'] = $data['poster_username'];
            $data_arr['subject_url'] = $data['subject_url'];
            $data_arr['subject'] = $data['subject'];
            $data_arr['post_content'] = $data['post'];
            $data_arr['html_post'] = $data['html_post'];
            $data_arr['date_posted'] = $data['date_posted'];
            $data_arr['is_archive'] = "no";
            $data_arr['created_at'] = date('Y-m-d H:i:s');
            $this->db->WHERE('msg_id', $data_arr['msg_id'])->UPDATE('altt_scraped_archive_data_tbl ', $data_arr);
        }

      
        
    }
}