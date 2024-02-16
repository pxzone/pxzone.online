<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Telegram_bot_model extends CI_Model {
    function __construct (){
        parent::__construct();
    }
    public function registerTelegramData($chat_id){
        $check_tgdata = $this->db->WHERE('chat_id', $chat_id)->GET('telegram_bot_tbl')->row_array(); 
        if(!empty($check_tgdata)){
            $this->db->WHERE('chat_id', $chat_id)->DELETE('telegram_bot_tbl'); 
        }
        $tg_data = array(
            'chat_id'=> $chat_id,
            'created_at'=> date('Y-m-d H:i:s'),
        );
        $this->db->INSERT('telegram_bot_tbl', $tg_data);
        
    }
    public function getTelegramData($chat_id){
        return $this->db->SElECT('status')->WHERE('chat_id', $chat_id)->GET('telegram_bot_tbl')->row_array(); 
    }
    public function deleteTelegramData($chat_id){
        $this->db->WHERE('chat_id', $chat_id)->DELETE('telegram_bot_tbl');
    }
    public function updateAlttUsername($chat_id, $altt_username){
        $tg_update_data = array(
            // 'status'=>'active',
            'altt_username'=>$altt_username,
            'updated_at'=>date('Y-m-d H:i:s')
        );
        $this->db->WHERE('chat_id', $chat_id)->UPDATE('telegram_bot_tbl', $tg_update_data);  
    }
    public function updateAlttUIDStatus($chat_id, $altt_uid){
        $tg_update_data = array(
            'status'=>'active',
            'altt_uid'=>$altt_uid,
            'updated_at'=>date('Y-m-d H:i:s')
        );
        $this->db->WHERE('chat_id', $chat_id)->UPDATE('telegram_bot_tbl', $tg_update_data);  
    }
    public function updateTelegramDataStatus($chat_id, $altt_username){
        $tg_update_data = array(
            'status'=>'active',
            'altt_username'=>$altt_username,
            'updated_at'=>date('Y-m-d H:i:s')
        );
        $this->db->WHERE('chat_id', $chat_id)->UPDATE('telegram_bot_tbl', $tg_update_data);  
    }
    public function insertTelegramMsg(){
        $chat_id = $this->input->post('chat_id');
        $message_id = $this->input->post('message_id');
        $message_text = $this->input->post('message_text');
        $data_arr = array(
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'message_text'=>$message_text,
            'created_at'=>date('Y-m-d H:i:s'),
        );
        $this->db->INSERT('telegram_bot_mgs_tbl', $data_arr);
        return true;
    }
    public function insertNewPhrase($chat_id, $phrase){
       $tg_data = array(
            'chat_id'=> $chat_id,
            'phrase'=> trim($phrase),
            'created_at'=> date('Y-m-d H:i:s'),
        );
        $this->db->INSERT('tracked_phrase_tbl', $tg_data);  
    }
    public function getTrackPhraseData($chat_id){
         return $this->db->SELECT('chat_id, phrase')
            ->ORDER_BY('id','desc')
            ->WHERE('chat_id', $chat_id)
            ->GET('tracked_phrase_tbl')->result_array();
     }
     public function deleteTrackedPhrase($chat_id, $phrase){
        return $this->db->WHERE('chat_id', $chat_id)
           ->WHERE('phrase', $phrase)
           ->DELETE('tracked_phrase_tbl');
    }
    public function insertNewTrackedUser($chat_id, $user){
        $tg_data = array(
             'chat_id'=> $chat_id,
             'username'=> trim($user),
             'type'=>'track',
             'created_at'=> date('Y-m-d H:i:s'),
         );
         $this->db->INSERT('tracked_users_tbl', $tg_data);  
     }
     public function getTrackUsersData($chat_id){
        return $this->db->SELECT('chat_id, username')
           ->ORDER_BY('id','desc')
           ->WHERE('chat_id', $chat_id)
           ->WHERE('type', 'track')
           ->GET('tracked_users_tbl')->result_array();
    }
    public function getCurrentNumberOfUsers(){
        return $this->db->WHERE('status', 'active')
           ->GET(' telegram_bot_tbl')->num_rows();
    }
    public function deleteTrackedUsername($chat_id, $username){
        return $this->db->WHERE('chat_id', $chat_id)
           ->WHERE('username', $username)
           ->WHERE('type', 'track')
           ->DELETE('tracked_users_tbl');
    }
    public function insertNewIgnoredUser($chat_id, $user){
        $tg_data = array(
             'chat_id'=> $chat_id,
             'username'=> trim($user),
             'type'=> 'ignore',
             'created_at'=> date('Y-m-d H:i:s'),
         );
         $this->db->INSERT('tracked_users_tbl', $tg_data);  
    }
    public function insertNewTrackURL($chat_id, $topic_url, $topic_title){
        $short_url = explode(".msg", $topic_url);
        $topic = explode("=", $short_url[0]);
        $topic_id = $topic[1];

        if(stripos( $topic_id , ".") !== false){
            $topic_id = explode(".",$topic_id);
            $topic_id = $topic_id[0];
        }

        $check_topic = $this->db->WHERE('topic_id', $topic_id)->GET('tracked_topics_tbl')->num_rows();

        if($check_topic > 0){
            return true;
        }
        else{
            $tg_data = array(
                'chat_id'=> $chat_id,
                'topic_id'=> $topic_id,
                'url'=> trim($topic_url),
                'title'=> trim($topic_title),
                'created_at'=> date('Y-m-d H:i:s'),
            );
            $this->db->INSERT('tracked_topics_tbl', $tg_data); 
        }
         
     }
    public function getIgnoredUsersData($chat_id){
        return $this->db->SELECT('chat_id, username')
           ->ORDER_BY('id','desc')
           ->WHERE('chat_id', $chat_id)
           ->WHERE('type', 'ignore')
           ->GET('tracked_users_tbl')->result_array();
    }
    public function deleteIgnoredUsername($chat_id, $username){
        return $this->db->WHERE('chat_id', $chat_id)
           ->WHERE('username', $username)
           ->WHERE('type', 'ignore')
           ->DELETE('tracked_users_tbl');
    }
    public function getTrackTopicsData($chat_id){
        return $this->db->SELECT('chat_id, topic_id, title')
           ->ORDER_BY('id','desc')
           ->WHERE('chat_id', $chat_id)
           ->GET('tracked_topics_tbl')->result_array();
    }
    public function deleteTrackedTopic($chat_id, $topic_id){
       return $this->db->WHERE('chat_id', $chat_id)
          ->WHERE('topic_id', $topic_id)
          ->DELETE('tracked_topics_tbl');
    }
    public function getTrackTopicDataByID($chat_id, $topic_id){
        return $this->db->SELECT('chat_id, title, url')
            ->WHERE('chat_id', $chat_id)
            ->WHERE('topic_id', $topic_id)
            ->GET('tracked_topics_tbl')->row_array();
    }
    public function getUserData(){
        return $this->db->SELECT('altt_uid, altt_username, chat_id, karma')
           ->WHERE('status', 'active')
           ->GET('telegram_bot_tbl ')->result_array();
    }
    public function getUserDatabyAlttID($altt_uid){
        return $this->db->SELECT('altt_username, chat_id, karma, altt_uid')
            ->WHERE('altt_uid', $altt_uid)
            ->WHERE('status', 'active')
           ->GET('telegram_bot_tbl ')->row_array();
    }
    public function checkPostMsgID($msg_id){
        $saved_pst = $this->db->WHERE('msg_id', $msg_id)
           ->GET('altt_scraped_data_tbl ')->num_rows();
        
        $archive_pst = $this->db->WHERE('msg_id', $msg_id)
           ->GET('altt_scraped_archive_data_tbl ')->num_rows();
        if($saved_pst <= 0 && $archive_pst <= 0){
            return false;
        }
        else{
            return true;
        }
    }
    public function notifyUserEditedPost($data) {
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";
        $poster_username = $data['poster_username'];

        $this->notifyEditedPost($data, $api_endpoint, $poster_username);
        $this->notifyTrackPhrases($data, $api_endpoint, $poster_username);
    }
    public function notifyUser($data) {
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";
        $poster_username = $data['poster_username'];

        $this->notifySubscribedUser($data, $api_endpoint, $poster_username);
        $this->notifyTrackPhrases($data, $api_endpoint, $poster_username);
        $this->notifyTrackUsers($data, $api_endpoint, $poster_username);
        $this->notifyTrackTopicReplies($data, $api_endpoint, $poster_username);
        // $this->checkEditedPost($data, $api_endpoint, $poster_username);
    }
    public function notifyTrackedBoard($msg_data, $topic_data){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";

        $board_id = $msg_data['board_id'];
        $date_posted = $topic_data['date_posted'];
        $poster_username = $topic_data['username'];

        $user_data = $this->db->SELECT('tbt.chat_id, tbt.board_id, abt.board_name, tb.altt_username')
            ->WHERE('tbt.board_id', $board_id)
            ->FROM('tracked_board_tbl as tbt')
            ->JOIN('telegram_bot_tbl as tb','tb.chat_id=tbt.chat_id')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=tbt.board_id', 'left')
            ->GET()->result_array();

        date_default_timezone_set('Europe/Rome');
        $current_time = time();
        $scrape_time = strtotime($date_posted);
        $time_difference = $current_time - $scrape_time;

        if ($time_difference <= 120) { // if the time difference is equal or below 2 mins
            foreach($user_data as $user){
                $altt_username = $user['altt_username'];
                $board_name = $user['board_name'];

                # start - IF IGNORE USERS EXISTS
                $ignored_users = $this->db->SELECT('username')
                    ->WHERE('type', 'ignore')
                    ->WHERE('chat_id', $user['chat_id'])
                    ->GET('tracked_users_tbl')->result_array();
                $ignore_user_arr = array();
                foreach($ignored_users as $iu){
                    array_push($ignore_user_arr, $iu['username']);
                }
                # end - IF IGNORE USERS EXISTS

                if($poster_username !== $altt_username && !in_array($poster_username, $ignore_user_arr)){ // altt username is not equal to the notification subscriber/user so the user wont notify h
                      
                    $text = (strlen($msg_data['tg_post']) >= 150) ? substr($msg_data['tg_post'], 0, 120).'...' : $msg_data['tg_post'];
                    $subject_url = $msg_data['subject_url'];
                    $subject = $msg_data['subject'];
                    $message_text = "📦 There is a new topic by <b>$poster_username</b> in the tracked board <b>$board_name</b>: <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
                    $post_data = array( 
                        'chat_id' => $user['chat_id'],
                        'text' => $message_text,
                        'parse_mode'=> 'html'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
        }

    }
    public function saveKarmaPoint($data){
        $karma_point = (int)$data['current_karma'] - (int)$data['prev_karma'];
        $data_arr = array(
            'altt_username'=> $data['username'],
            'karma_point'=> $karma_point,
            'total_karma'=> $data['current_karma'],
            'created_at'=> date('Y-m-d H:i:s'),
        );
        $this->db->INSERT(' altt_karma_log_tbl', $data_arr);
    }
    # MENTION WHEN KARMA IS RECEIVED
    public function notifyKarmaTransaction($data){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";
        $current_karma = $data['current_karma'];
        $prev_karma = $data['prev_karma'];
        $recv_karma_count = $current_karma - $prev_karma;

        if((int)$current_karma > (int)$prev_karma){ 
            $message_text = "✨ You received <b>$recv_karma_count</b> Positive Karma. Total received Karma <b>$current_karma</b>. \nKeep posting good and helpful topic to get more Positive Karma. ";
            $post_data = array( 
                'chat_id' => $data['chat_id'],
                'text' => $message_text,
                'parse_mode'=> 'html'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $data_arr1 = array(
                'karma'=>$current_karma,
                'updated_at'=>date('Y-m-d H:i:s'),
            );
            $this->db->WHERE('chat_id', $data['chat_id'])->UPDATE('telegram_bot_tbl', $data_arr1);
            return true;
        }
        else if((int)$current_karma < (int)$prev_karma){
            $message_text = "✨ You received <b>$recv_karma_count</b> Negative Karma. Total received Karma <b>$current_karma</b>.\nFollow the rules, and keep posting good, helpful topic to avoid getting Negative Karma.";

            $post_data = array( 
                'chat_id' => $data['chat_id'],
                'text' => $message_text,
                'parse_mode'=> 'html'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $data_arr2 = array(
                'karma'=>$current_karma,
                'updated_at'=>date('Y-m-d H:i:s')
            );
            $this->db->WHERE('chat_id', $data['chat_id'])->UPDATE('telegram_bot_tbl', $data_arr2);
            return true;
        }
        return false;
    }
    # MENTION USING ALTT USERNAMES (EDITED POST)
    public function notifyEditedPost($data, $api_endpoint, $poster_username){
        $mention_usernames = $this->db->SELECT('chat_id, altt_username')->WHERE('status', 'active')->GET('telegram_bot_tbl')->result_array();
        foreach ($mention_usernames as $q) 
        {
            $to_scan = $data['subject'].' '.$data['post'];
            $altt_username = $q['altt_username'];
            if (stripos($to_scan, $altt_username) !== FALSE) {  // TRACK MENTIONS BY ALTT USERNAME

                # start - IF IGNORE USERS EXISTS
                $ignored_users = $this->db->SELECT('username')
                    ->WHERE('type', 'ignore')
                    ->WHERE('chat_id', $q['chat_id'])
                    ->GET('tracked_users_tbl')->result_array();
                $ignore_user_arr = array();
                foreach($ignored_users as $iu){
                    array_push($ignore_user_arr, $iu['username']);
                }
                # end - IF IGNORE USERS EXISTS

                if($poster_username !== $altt_username && !in_array($poster_username, $ignore_user_arr)){ // altt username is not equal to the notification subscriber/user so the user wont notify h
                    $scanned_data = $this->db->SELECT('chat_id')->WHERE('chat_id', $q['chat_id'])->WHERE('altt_username', $q['altt_username'])->WHERE('status', 'active')->GET('telegram_bot_tbl')->row_array();
                      
                    $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                    $username = $data['poster_username'];
                    $subject_url = $data['subject_url'];
                    $subject = $data['subject'];
                    $message_text = "💬 You have been mentioned in an edited post by <b>$username</b> in <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
                    $post_data = array( 
                        'chat_id' => $scanned_data['chat_id'],
                        'text' => $message_text,
                        'parse_mode'=> 'html'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
        }
    }
    # MENTION USING ALTT USERNAMES (DEFAULT)
    public function notifySubscribedUser($data, $api_endpoint, $poster_username){
        $mention_usernames = $this->db->SELECT('chat_id, altt_username')->WHERE('status', 'active')->GET('telegram_bot_tbl')->result_array();
        foreach ($mention_usernames as $q) 
        {
            $to_scan = $data['subject'].' '.$data['post'];
            $altt_username = $q['altt_username'];
            if (stripos($to_scan, $altt_username) !== FALSE) {  // TRACK MENTIONS BY ALTT USERNAME
                # start - IF IGNORE USERS EXISTS
                $ignored_users = $this->db->SELECT('username')
                    ->WHERE('type', 'ignore')
                    ->WHERE('chat_id', $q['chat_id'])
                    ->GET('tracked_users_tbl')->result_array();
                $ignore_user_arr = array();
                foreach($ignored_users as $iu){
                    array_push($ignore_user_arr, $iu['username']);
                }
                # end - IF IGNORE USERS EXISTS

                if($poster_username !== $altt_username && !in_array($poster_username, $ignore_user_arr)){ // altt username is not equal to the notification subscriber/user so the user wont notify h
                    $scanned_data = $this->db->SELECT('chat_id')->WHERE('chat_id', $q['chat_id'])->WHERE('altt_username', $q['altt_username'])->WHERE('status', 'active')->GET('telegram_bot_tbl')->row_array();
                      
                    $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                    $username = $data['poster_username'];
                    $subject_url = $data['subject_url'];
                    $subject = $data['subject'];
                    $message_text = "💬 You have been mentioned by <b>$username</b> in <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
                    $post_data = array( 
                        'chat_id' => $scanned_data['chat_id'],
                        'text' => $message_text,
                        'parse_mode'=> 'html'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
        }
    }
    # MENTION USING PHRASES 
    public function notifyTrackPhrases($data, $api_endpoint, $poster_username){
        $to_scan = $data['subject'].' '.$data['post'];
        $track_phrases = $this->db->SELECT('tpt.chat_id, tpt.phrase, altt_username')
            ->FROM('tracked_phrase_tbl as tpt')
            ->WHERE('tbt.status','active')
            ->JOIN('telegram_bot_tbl as tbt', 'tbt.chat_id=tpt.chat_id','left')
            ->GET()->result_array();
        foreach ($track_phrases as $tp) 
        {
            if (stripos($to_scan, $tp['phrase']) !== FALSE) {  // TRACK MENTIONS BY ALTT USERNAME

                # start - IF IGNORE USERS EXISTS
                $ignored_users = $this->db->SELECT('username')
                    ->WHERE('type', 'ignore')
                    ->WHERE('chat_id', $tp['chat_id'])
                    ->GET('tracked_users_tbl')->result_array();
                $ignore_user_arr = array();
                foreach($ignored_users as $iu){
                    array_push($ignore_user_arr, $iu['username']);
                }
                # end - IF IGNORE USERS EXISTS

                # Don't notify when poster_username is equal to the subscriber/user. && If the poster_username is ignored 
                if($poster_username !== $tp['altt_username'] && !in_array($poster_username, $ignore_user_arr)){ 
                        
                    $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                    $username = $data['poster_username'];
                    $subject_url = $data['subject_url'];
                    $subject = $data['subject'];
                    $phrase = $tp['phrase'];
                    $message_text = "📃 New post with matched phrase <b>$phrase</b> by <b>$username</b> in <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
                    $post_data = array( 
                        'chat_id' => $tp['chat_id'],
                        'text' => $message_text,
                        'parse_mode'=> 'html'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
        }
    }
    # MENTION USING TRACK USERS 
    public function notifyTrackUsers($data, $api_endpoint, $poster_username){
        $tracked_users = $this->db->SELECT('tut.chat_id, tut.username, altt_username')
            ->FROM('tracked_users_tbl as tut')
            ->WHERE('tut.type', 'track')
            ->WHERE('tbt.status','active')
            ->JOIN('telegram_bot_tbl  as tbt', 'tbt.chat_id=tut.chat_id','left')
            ->GET()->result_array();

        foreach ($tracked_users as $tu) 
        {
            if ($tu['username'] == $data['poster_username']) {  // TRACK MENTIONS BY 'POSTER' USERNAME
                
                $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                $username = $data['poster_username'];
                $subject_url = $data['subject_url'];
                $subject = $data['subject'];
                $message_text = "👤 There is a new post by the tracked user <b>$username</b> in <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";

                $post_data = array( 
                    'chat_id' => $tu['chat_id'],
                    'text' => $message_text,
                    'parse_mode'=> 'html'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
        }
    }

    # MENTION NEW TOPIC REPLY
    public function notifyTrackTopicReplies($data, $api_endpoint, $poster_username){
        $track_topic = $this->db->SELECT('tt.chat_id, tt.topic_id, tbt.altt_username')
            ->FROM('tracked_topics_tbl as tt')
            ->JOIN('telegram_bot_tbl as tbt','tbt.chat_id=tt.chat_id','left')
            ->GET()->result_array();
        foreach ($track_topic as $tt) 
        {
            $to_scan = $data['subject'].' '.$data['post'];
            $altt_username = $tt['altt_username'];

            if ($data['topic_id'] == $tt['topic_id']) {  // TRACK MENTIONS BY TOPIC

                # start - IF IGNORE USERS EXISTS
                $ignored_users = $this->db->SELECT('username')
                    ->WHERE('type', 'ignore')
                    ->WHERE('chat_id', $tt['chat_id'])
                    ->GET('tracked_users_tbl')->result_array();
                $ignore_user_arr = array();
                foreach($ignored_users as $iu){
                    array_push($ignore_user_arr, $iu['username']);
                }
                # end - IF IGNORE USERS EXISTS

                if($poster_username !== $altt_username && !in_array($poster_username, $ignore_user_arr)){ // altt username is not equal to the notification subscriber/user so the user wont notify h
                    $scanned_data = $this->db->SELECT('chat_id')->WHERE('chat_id', $tt['chat_id'])->WHERE('altt_username', $tt['altt_username'])->WHERE('status', 'active')->GET('telegram_bot_tbl')->row_array();
                      
                    $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                    $username = $data['poster_username'];
                    $subject_url = $data['subject_url'];
                    $subject = $data['subject'];
                    $message_text = "📙 There is a new reply by <b>$username</b> in the tracked topic <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
                    $post_data = array( 
                        'chat_id' => $scanned_data['chat_id'],
                        'text' => $message_text,
                        'parse_mode'=> 'html'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
        }
    }
    public function saveScrapedData($data, $topic_data){
        $data_arr['msg_id'] = $data['msg_id'];
        $data_arr['topic_id'] = $data['topic_id'];
        $data_arr['board_id'] = $data['board_id'];
        $data_arr['username'] = $data['poster_username'];
        $data_arr['subject_url'] = $data['subject_url'];
        $data_arr['subject'] = $data['subject'];
        $data_arr['post_content'] = $data['post'];
        // $data_arr['html_post'] = $data['html_post'];
        $data_arr['date_posted'] = $data['date_posted'];
        $data_arr['created_at'] = date('Y-m-d H:i:s');
        $this->db->INSERT('altt_scraped_data_tbl', $data_arr);

        $new_topic =  $this->insertNewTopics($data, $topic_data);
        if($new_topic == true){
            return true;
        }
        else{
            return false;
        }
        
    }
    public function insertNewTopics($data, $topic_data){
        $check = $this->db->WHERE('board_id', $data['board_id'])
                ->WHERE('topic_id', $data['topic_id'])
                ->GET('altt_topics_tbl')->num_rows();

        if($check <= 0){
            $topic_title = $data['subject'];
            if(stripos($data['subject'], "Re:") !== false){
                $topic_title = substr($topic_title, 3);
            }
            $data_arr = array(
                'board_id'=> $data['board_id'],
                'topic_id'=> $data['topic_id'],
                'username'=> $topic_data['username'],
                'post'=> $topic_data['post'],
                'date_posted'=> $topic_data['date_posted'],
                'topic_name'=> $topic_title,
                'created_at'=> date('Y-m-d H:i:s'),
            );
            $this->db->INSERT('altt_topics_tbl ', $data_arr);
            return true;
        }

    }
    public function checkScrapedPost(){
        return $this->db->SELECT('msg_id, post_content, created_at')->LIMIT(15)->GET('altt_scraped_data_tbl')->result_array();
    }
  
    public function insertArchiveScrapedPost($edited_data, $saved_data, $status){
        $check_data = $this->db->WHERE('msg_id', $saved_data['msg_id'])->GET('altt_scraped_archive_data_tbl')->num_rows();
        $date_posted = $this->changeTimezone($edited_data['date_posted']);
        $data_arr = array(
            'msg_id'=>$edited_data['msg_id'],
            'username'=>$edited_data['poster_username'],
            'topic_id'=>$edited_data['topic_id'],
            'board_id'=>$edited_data['board_id'],
            // 'board_name'=>$edited_data['board_name'],
            'subject_url'=>$edited_data['subject_url'],
            'subject'=>$edited_data['subject'],
            'post_content'=>$edited_data['tg_post'],
            'html_post'=>$edited_data['html_post'],
            'date_posted' => $edited_data['date_posted'],
            'status'=>$status,
            'created_at'=>date('Y-m-d H:i:s'),
        );

        if($check_data <= 0){
            $this->db->INSERT('altt_scraped_archive_data_tbl', $data_arr);
        }
    }
   
    public function deleteScrapedPost($msg_id){
        $this->db->WHERE('msg_id', $msg_id)
        ->DELETE('altt_scraped_data_tbl');
    }
    public function getAllUsersData(){
        return $this->db->SELECT('chat_id, altt_username')->WHERE('status','active')->GET('telegram_bot_tbl')->result_array();
    }
    public function getTopicData(){
        return $this->db->SELECT('topic_id')->WHERE('date_posted', NULL)->GET('altt_topics_tbl')->result_array();
    }
    public function updateAuthorDate($topic_id, $data_arr){
        $this->db->WHERE('topic_id', $topic_id)->UPDATE('altt_topics_tbl', $data_arr);
    }
    public function getBoardNameById($board_id){
        $query = $this->db->SELECT('board_name')->WHERE('board_id', $board_id)->GET('altt_boards_tbl')->row_array();
        return $query['board_name'];
    }
    public function getTrackBoardsData($chat_id){
        return $this->db->SELECT('chat_id, board_id, board_name')
        ->WHERE('chat_id', $chat_id)
        ->ORDER_BY('id','desc')
        ->GET('tracked_board_tbl')->result_array();
    }
    public function insertNewTrackedBoard($chat_id, $board_id, $board_name){
        $check = $this->db->WHERE('chat_id', $chat_id)->WHERE('board_id', $board_id)->GET('tracked_board_tbl')->num_rows();
        if($check > 0){
            return $check;
        }
        else{
            $data_arr = array(
                'board_id'=>$board_id,
                'chat_id'=>$chat_id,
                'board_name'=>$board_name,
                'created_at'=>date('Y-m-d H:i:s')
            );
            $this->db->INSERT('tracked_board_tbl', $data_arr);
        }
    }
    public function getTrackBoardDataByID($chat_id, $board_id){
        return $this->db->SELECT('chat_id, board_id, board_name')
        ->WHERE('chat_id', $chat_id)
        ->WHERE('board_id', $board_id)
        ->GET('tracked_board_tbl')->row_array();
    }
    public function deleteTrackedBoard($chat_id, $board_id){
        return $this->db->WHERE('chat_id', $chat_id)
           ->WHERE('board_id', $board_id)
           ->DELETE('tracked_board_tbl');
    }
}