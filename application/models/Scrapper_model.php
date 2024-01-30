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
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            exit;
        }
        curl_close($ch);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
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
    public function loginForum($login_page_data){
        $loginUrl = 'https://www.altcoinstalks.com/index.php?action=login2';
        $username = 'pxzbot';
        $password = '^QPQ3nPzMp"CBxk';
        $ch = curl_init($loginUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error during request: ' . curl_error($ch);
            exit;
        }
        curl_close($ch);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        # GET TOPIC SUBJECT
        $elements = $xpath->query('//h3[@class="catbg"]')->item(0);
        // $target_div = $xpath->query('//*[@id="msg_'.$msg_id.'"]')->item(0);
        $text_content = '';
        if($elements){
            $img_to_remove = $xpath->query('.//img', $elements);
            foreach ($img_to_remove as $img) {
                $img->nodeValue = '';
            }
            $span_to_remove = $xpath->query('.//span', $elements);
            foreach ($span_to_remove as $span) {
                $span->nodeValue = '';
            }
            $title = $elements->textContent;
        }
        $title = trim($title);
        $scrape_title = substr($title , 6);
        $str_count = strripos($scrape_title, "(Read");
        $topic_title = substr($scrape_title, 0, $str_count);

        return $topic_title;
    }
}