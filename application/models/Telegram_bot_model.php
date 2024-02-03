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
        return $this->db->SELECT('altt_uid')
           ->WHERE('status', 'active')
           ->GET('telegram_bot_tbl ')->result_array();
    }
    public function notifyUser($data)
    {
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

    # MENTION WHEN KARMA IS RECEIVED
    public function notifyKarmaTransaction($data){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";
        $current_karma = $data['current_karma'];
        $prev_karma = $data['prev_karma'];

        if((int)$current_karma > (int)$prev_karma){ 
            $recv_karma_count = $current_karma - $prev_karma;
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
            $recv_karma_count = $current_karma - $prev_karma;
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
        // $this->output->set_content_type('application/json')->set_output(json_encode($response));

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
                    $message_text = "💬 There is a new reply by <b>$username</b> in the tracked topic <a href='$subject_url'>$subject</a> <blockquote>$text</blockquote>";
    
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
    public function saveScrapedData($data){
        $data_arr['msg_id'] = $data['msg_id'];
        $data_arr['username'] = $data['poster_username'];
        $data_arr['subject_url'] = $data['subject_url'];
        $data_arr['subject'] = $data['subject'];
        $data_arr['post_content'] = $data['post'];
        $data_arr['created_at'] = date('Y-m-d H:i:s');
        $this->db->INSERT('altt_scraped_data_tbl', $data_arr);
    }
    public function checkScrapedPost(){
        return $this->db->SELECT('msg_id, post_content')->GET('altt_scraped_data_tbl')->result_array();
    }
    public function deleteScrapedPost($msg_id){
        $date_range = array('created_at >'=>date('Y-m-d H:i:s'), 'created_at <'=> date('Y-m-d 11:59:59'));
        $this->db->WHERE('msg_id', $msg_id)
            ->DELETE('altt_scraped_data_tbl');
    }
    public function getAllUsersData(){
        return $this->db->SELECT('chat_id, altt_username')->WHERE('status','active')->GET('telegram_bot_tbl')->result_array();
    }

    # FOR TESTING
    public function mentionInTelegramTest()
    {
        $bot_token = BOT_TOKEN;
        # test data
        $data = array(
            'subject_url'=>'https://www.altcoinstalks.com/index.php?topic=315268.msg1468512#msg1468512',
            'subject'=>'Re: Test Subject',
            'post'=>"This post is mentioning you admin! With a content phrase of bitcoin Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sit alias dignissimos porro, praesentium",
            'poster_username'=>'theymos',
            'tg_post'=>"This post is mentioning you admin! With a content phrase of bitcoin Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sit alias dignissimos porro, praesentium",

        );


        $to_scan = $data['subject'].' '.$data['post'];
        $poster_username = $data['poster_username'];

        $mention_usernames = $this->db->SELECT('chat_id, altt_username')->WHERE('status', 'active')->GET('telegram_bot_tbl')->result_array();
        # MENTION USING ALTT USERNAMES 
        foreach ($mention_usernames as $q) 
        {
            $altt_username = $q['altt_username'];
            if (stripos($to_scan, $altt_username) !== FALSE) {  // TRACK MENTIONS BY ALTT USERNAME
                $scanned_data = $this->db->SELECT('chat_id')->WHERE('chat_id', $q['chat_id'])->WHERE('altt_username', $q['altt_username'])->WHERE('status', 'active')->GET('telegram_bot_tbl')->row_array();
                if($poster_username !== $altt_username){ // altt username is not equal to the notification subscriber/user so the user wont notify h
                    $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";
                        
                    $text = (strlen($data['tg_post']) >= 150) ? substr($data['tg_post'], 0, 120).'...' : $data['tg_post'];
                    // $text = (strlen($data['textContent']) >= 150) ? substr($data['textContent'], 0, 120).'...' : $data['textContent'];
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
        $this->mentionTrackPhrases($bot_token, $api_endpoint);
        $this->mentionTrackUsers($bot_token, $api_endpoint);
    }
	
}