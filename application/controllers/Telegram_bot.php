<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Telegram_bot extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->library('user_agent');
        $this->load->library('telegram_api');
        $this->load->model('Telegram_bot_model');
        $this->load->model('Scrapper_model');
        $this->load->library('Api_auth');
    }
    public function currentSubscribersToImg() {
        $count = $this->Telegram_bot_model->getCurrentNumberOfUsers();
        $str = "Current active users: $count";
        $width = strlen($str) * 10.4;
        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 17);
        $text_color = imagecolorallocate($im, 0,0,0);
        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 500 ; $i++) {  
            for ($j=0; $j < 500 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);

        imagestring($im, 5, 5, 0, $str, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function callback() {
        $update = json_decode(file_get_contents("php://input"), TRUE);
        $post_data = array();
        $response = "";
        $command = '/menu';
        $delete_message = "no";
        
        if(isset($update['message'])){
            $message_id = $update['message']['message_id'];
            $chat_id = $update['message']['chat']['id'];
            $name = $update['message']['chat']['first_name'];
            // $username = ($update['message']['chat']['username']) ? $update['message']['chat']['username'] : '';
            $message_text = $update['message']['text'];
    	    $telegram_data = $this->Telegram_bot_model->getTelegramData($chat_id);
            $ask_notify = "no";

            if (isset($message_text) && $message_text == '/start') {
                $welcome_message = "Hello $name! Welcome to AltcoinsTalks Notifier!";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $welcome_message,
                );
                $this->sendMessage($post_data);

                $response_text = "Would you like to receive notifications when someone quote your posts or mentions you?";
                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => 'Yes', 'callback_data' => 'yes_notify_btn'),
                            array('text' => 'No', 'callback_data' => 'no_notify_btn')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $post_data = array(
                    // 'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => $encoded_keyboard,
                );
                $this->sendMessage($post_data);
            }
            else if (stripos($update['message']['text'], 'https://www.altcoinstalks.com/index.php?action=profile') !== FALSE ) {
                $profile_url = $update['message']['text'];
                $profile_uid_url = explode("u=", $profile_url);
                $uid = $profile_uid_url[1];

                $response_text = "Is your AltcoinsTalks' user ID is $uid?";
                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => 'Yes', 'callback_data' => 'yes_userid_btn:'.$uid),
                            array('text' => 'No', 'callback_data' => 'no_userid_btn')
                        )
                    )
                );
                $encoded_keyboard = json_encode($keyboard);
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => $encoded_keyboard,
                );
                $this->sendMessage($post_data);
            }
            elseif($telegram_data['status'] == 'inactive' && $message_text !== '/start'){
                $response_text = 'Is your AltcoinsTalks\' username is ' . $message_text . '?';
                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => 'Yes', 'callback_data' => 'yes_username_btn:'.$message_text),
                            array('text' => 'No', 'callback_data' => 'no_username_btn')
                        )
                    )
                );
                $encoded_keyboard = json_encode($keyboard);
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => $encoded_keyboard,
                );
                $this->sendMessage($post_data);
            }
            else if ($update['message']['text'] == '/menu' &&  $telegram_data['status'] == 'active') {
                $name = $update['message']['chat']['first_name'];
                $message_text = $update['message']['text'];
                $chat_id = $update['message']['chat']['id'];
                $message_id = $update['message']['message_id'];
                
                $response_text = "Hello $name, \n\nCheck what you can do.";
                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '💬 Track Phrase', 'callback_data' => 'track_phrase'),
                            array('text' => '📙 Track Topics', 'callback_data' => 'track_topic'),
                        ),
                        array(
                            array('text' => '📦 Track Boards', 'callback_data' => 'track_board'),
                            array('text' => '👤 Track Users', 'callback_data' => 'track_users'),
                        ),
                        array(
                            array('text' => '🚫 Ignore Users', 'callback_data' => 'ignore_user'),
                            array('text' => '🚫 Stop Notifying Me', 'callback_data' => 'stop_notify_btn'),
                            // array('text' => '✖️ Close Menu', 'callback_data' => 'close_menu'),
                        )
                    )
                );
                $encoded_keyboard = json_encode($keyboard);
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => $encoded_keyboard,
                );
                $this->sendMessage($post_data);
                $this->editMessageText($post_data);
                $this->deleteMessage($chat_id, $message_id);
            }
            else if (stripos($update['message']['text'], 'Phrase:') !== FALSE ) {
                $phrase = substr($update['message']['text'], 7);
                $response_text = "You are now tracking the phrase: \n\n$phrase";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
    	        $this->Telegram_bot_model->insertNewPhrase($chat_id, $phrase);
                $this->sendMessage($post_data);

                $this->trackPhrase($chat_id, $message_id, 'send');
            }
            else if (stripos($update['message']['text'], 'User:') !== FALSE ) {
                $str_part = explode(":",$update['message']['text']);
                $user = $str_part[1];
                $response_text = "You are now tracking the user: \n\n$user";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
    	        $this->Telegram_bot_model->insertNewTrackedUser($chat_id, $user);
                $this->sendMessage($post_data);

                $this->trackUsers($chat_id, $message_id, 'send');
            }
            else if (stripos($update['message']['text'], 'Ignore:') !== FALSE ) {
                $str_part = explode(":",$update['message']['text']);
                $user = $str_part[1];
                $response_text = "You are now ignoring the user: \n\n$user";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
    	        $this->Telegram_bot_model->insertNewIgnoredUser($chat_id, $user);
                $this->sendMessage($post_data);

                $this->ignoredUsers($chat_id, $message_id, 'send');
            }
            
            else if (stripos($update['message']['text'], 'https://www.altcoinstalks.com/index.php?topic=') !== FALSE ) {
                $topic_url = $update['message']['text'];
                $topic_title = $this->Scrapper_model->scrapeTopicData($topic_url);
                $response_text = "You are now tracking the topic: \n\n<a href='$topic_url'>$topic_title</a>";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
               
    	        $topic = $this->Telegram_bot_model->insertNewTrackURL($chat_id, $topic_url, trim($topic_title));
                if($topic > 0){
                    $post_data = array(
                        'chat_id' => $chat_id,
                        'text' => "You already tracking this topic!",
                    );
                }
                $this->sendMessage($post_data);
                $this->trackTopics($chat_id, $message_id, 'send');
            }
            else if (stripos($update['message']['text'], 'https://www.altcoinstalks.com/index.php?board=') !== FALSE ) {
                $board_url = $update['message']['text'];
                $board_data = explode("board=", $board_url);
                $board_id = $board_data[1];
                $board_name = $this->Telegram_bot_model->getBoardNameById($board_id);
                $response_text = "You are now tracking the board: \n\n<a href='$board_url'>$board_name</a>";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
               
    	        $topic = $this->Telegram_bot_model->insertNewTrackedBoard($chat_id, $board_id, $board_name);
                if($topic > 0){
                    $post_data = array(
                        'chat_id' => $chat_id,
                        'text' => "You already tracking this board!",
                    );
                }
                else{
                    $post_data = array(
                        'chat_id' => $chat_id,
                        'text' => "Cannot get Board data!",
                    );
                }
                $this->sendMessage($post_data);
                $this->trackBoards($chat_id, $message_id, 'send');
            }
            else if ($update['message']['text'] == 'Thank you' || $update['message']['text'] == 'thanks' || stripos($update['message']['text'], 'thank') !== FALSE ) {
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => "You are welcome! 🙂",
                );
                $this->sendMessage($post_data);
            }
            else {
                $response_text = "Sorry! I can't understand you!";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->sendMessage($post_data);
            }
        }
        
        // Callback using buttons
        else if (isset($update['callback_query'])) 
        {
            $callback_data = $update['callback_query']['data'];
            $message_id = $update['callback_query']['message']['message_id'];
            $message_text = $update['callback_query']['message']['text'];
            $chat_id = $update['callback_query']['message']['chat']['id'];
            $name = $update['callback_query']['message']['chat']['first_name'];

            if ($callback_data === 'yes_notify_btn') {
                $response_text = 'Great! What is your AltcoinsTalks\' Username?';
                $this->Telegram_bot_model->registerTelegramData($chat_id);
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->editMessageText($post_data);
            } 
            else if ($callback_data === 'no_notify_btn') {
                $response_text = "That's unfortunate ☹️";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->editMessageText($post_data);
            } 
            else if (strpos($callback_data, 'yes_username_btn') !== false) {
                $str = explode(":", $callback_data);
                $altt_username = $str[1];
                $response_text = "What is your AltcoinsTalks' Profile URL? This is to get your user ID.";
                $this->Telegram_bot_model->updateAlttUsername($chat_id, $altt_username);

                // $response_text = "Great! You will get notified when someone mentions you. \n\nFor more options, click the /menu button.";
                // $this->Telegram_bot_model->updateTelegramDataStatus($chat_id, $altt_username);
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->editMessageText($post_data);
            } 
            else if ($callback_data === 'no_username_btn') {
                $response_text = 'So what is your AltcoinsTalks\' Username?';
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => json_encode(['inline_keyboard' => []])
                );
                $this->editMessageText($post_data);
            } 
            else if ($callback_data === 'track_topic' ) {
                $this->trackTopics($chat_id, $message_id, 'edit');
             }
             else if ($callback_data === 'track_board' ) {
                $this->trackBoards($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'stop_notify_btn') {
                $this->Telegram_bot_model->deleteTelegramData($chat_id);
                $response_text = "Thanks! You will not receive any more notification! \n\nBut if you feel like coming back, feel free to start again using /start command!";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->editMessageText($post_data);
            } 
            else if ($callback_data === 'close_menu') {
                $this->deleteMessage($chat_id, $message_id);
            } 
            else if ($callback_data === 'track_phrase' ) {
               $this->trackPhrase($chat_id, $message_id, 'edit');
            }
            
            
            else if ($callback_data === 'add_new_phrase') {
                $response_text = "What is the phrase that you want to track? \n\nUse this format to add. \nEx. \nPhrase: bitcoin";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
                $this->sendMessage($post_data);
            }
            else if ($callback_data === 'add_new_users') {
                $response_text = "What is the username of the user you want to track?\n\nUse this format to add.\nEx.\nUser: admin";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
                $this->sendMessage($post_data);
            }
            else if ($callback_data === 'add_ignore_users') {
                $response_text = "What is the username of the user you want to ignore?\n\nUse this format to add.\nEx.\nIgnore: admin";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
                $this->sendMessage($post_data);
            }
            else if ($callback_data === 'add_new_topic') {
                $response_text = "What is the URL of the topic you want to track?";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
                $this->sendMessage($post_data);
            }
            else if ($callback_data === 'add_new_board') {
                $response_text = "What is the URL of the board you want to track?";
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                );
                $this->sendMessage($post_data);
            }
            else if ($callback_data === 'track_users') {
                $this->trackUsers($chat_id, $message_id, 'edit');
            } 
            else if ($callback_data === 'ignore_user') {
                $this->ignoredUsers($chat_id, $message_id, 'edit');
            } 
            else if ($callback_data === 'go_back') {
                $response_text = "Hello $name, \n\nCheck what you can do.";
                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '💬 Track Phrase', 'callback_data' => 'track_phrase'),
                            array('text' => '📙 Track Topics', 'callback_data' => 'track_topic'),
                        ),
                        array(
                            array('text' => '📦 Track Boards', 'callback_data' => 'track_board'),
                            array('text' => '👤 Track Users', 'callback_data' => 'track_users'),
                        ),
                        array(
                            array('text' => '🚫 Ignore Users', 'callback_data' => 'ignore_user'),
                            array('text' => '🚫 Stop Notifying Me', 'callback_data' => 'stop_notify_btn'),
                            // array('text' => '✖️ Close Menu', 'callback_data' => 'close_menu'),
                        )
                    )
                );
                $encoded_keyboard = json_encode($keyboard);
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            } 
            else if (stripos($callback_data, 'phrase_command:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $phrase = $str_parts[2];

                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗑 Remove Phrase', 'callback_data' => "yes_delete_phrase:$chat_id:$phrase"),
                            array('text' => '↩️ Go Back', 'callback_data' => 'go_back_to_phrases')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $response_text = "<b>Selected Phrase</b>\n\n$phrase";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            } 
            else if (stripos($callback_data, 'user_command:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $username = $str_parts[2];

                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗑 Remove User', 'callback_data' => "yes_delete_user:$chat_id:$username"),
                            array('text' => '↩️ Go Back', 'callback_data' => 'go_back_to_users')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $response_text = "<b>Selected Tracked User</b>\n\n$username";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            } 
            else if (stripos($callback_data, 'user_ignore_command:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $username = $str_parts[2];

                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🚫 Stop ignoring', 'callback_data' => "yes_delete_ignored_user:$chat_id:$username"),
                            array('text' => '↩️ Go Back', 'callback_data' => 'go_back_to_ignore_users')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $response_text = "<b>Selected Ignored User</b>\n\n$username";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            }
            else if (stripos($callback_data, 'topic_command:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $topic_id = $str_parts[2];

                $topic_data = $this->Telegram_bot_model->getTrackTopicDataByID($chat_id, $topic_id);
                $topic_title = $topic_data['title'];
                $topic_url = $topic_data['url'];

                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗑 Remove Topic', 'callback_data' => "yes_delete_topic:$chat_id:$topic_id"),
                            array('text' => '↩️ Go Back', 'callback_data' => 'go_back_to_track_topic')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $response_text = "<b>Selected Topic</b>\n\n<a href='$topic_url'>$topic_title</a>";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            }
            else if (stripos($callback_data, 'board_command:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $board_id = $str_parts[2];

                $board_data = $this->Telegram_bot_model->getTrackBoardDataByID($chat_id, $board_id);
                $board_name = $board_data['board_name'];
                $board_url = "https://www.altcoinstalks.com/index.php?board=$board_id";

                $keyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array('text' => '🗑 Remove Board', 'callback_data' => "yes_delete_board:$chat_id:$board_id"),
                            array('text' => '↩️ Go Back', 'callback_data' => 'go_back_to_track_board')
                        )
                    )  
                );
                $encoded_keyboard = json_encode($keyboard);
                $response_text = "<b>Selected Board</b>\n\n<a href='$board_url'>$board_name</a>";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'parse_mode'=> 'html',
                    'reply_markup' => $encoded_keyboard,
                );
                $this->editMessageText($post_data);
            }
            else if (stripos($callback_data, 'yes_delete_phrase:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $phrase = $str_parts[2];
                $this->Telegram_bot_model->deleteTrackedPhrase($chat_id, $phrase);
                $this->trackPhrase($chat_id, $message_id, 'edit');
            }
            else if (stripos($callback_data, 'yes_delete_user:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $username = $str_parts[2];
                $this->Telegram_bot_model->deleteTrackedUsername($chat_id, $username);
                $this->trackUsers($chat_id, $message_id, 'edit');
            } 
            else if (stripos($callback_data, 'yes_delete_ignored_user:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $username = $str_parts[2];
                $this->Telegram_bot_model->deleteIgnoredUsername($chat_id, $username);
                $this->ignoredUsers($chat_id, $message_id, 'edit');
            } 
            else if (stripos($callback_data, 'yes_delete_topic:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $topic_id = $str_parts[2];
                $this->Telegram_bot_model->deleteTrackedTopic($chat_id, $topic_id);
                $this->trackTopics($chat_id, $message_id, 'edit');
            }
            else if (stripos($callback_data, 'yes_delete_board:') !== FALSE ) {
                $str_parts = explode(':', $callback_data);
                $chat_id = $str_parts[1];
                $board_id = $str_parts[2];
                $this->Telegram_bot_model->deleteTrackedBoard($chat_id, $board_id);
                $this->trackBoards($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'go_back_to_phrases') {
                $this->trackPhrase($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'go_back_to_users') {
                $this->trackUsers($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'go_back_to_ignore_users') {
                $this->ignoredUsers($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'go_back_to_track_topic') {
                $this->trackTopics($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'go_back_to_track_board') {
                $this->trackBoards($chat_id, $message_id, 'edit');
            }
            else if ($callback_data === 'no_userid_btn') {
                $response_text = "So what is your AltcoinsTalks' Profile URL? This is to get your user ID.";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                    'reply_markup' => json_encode(['inline_keyboard' => []])
                );
                $this->editMessageText($post_data);
            } 
            else if (stripos($callback_data, 'yes_userid_btn:') !== FALSE ) {
                $str = explode(":",$callback_data);
                $altt_uid = $str[1];
                
                $this->Telegram_bot_model->updateAlttUIDStatus($chat_id, $altt_uid);
                $this->Scrapper_model->getUserKarmaCount($altt_uid, $chat_id);

                $response_text = "Great! You will get notified when someone mentions you and when you receive Karma. \n\nFor more options, click the /menu button.";
                $post_data = array(
                    'message_id' => $message_id,
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->editMessageText($post_data);
            }
            else {
                $response_text = 'Sorry, I can\'t understand you!';
                $post_data = array(
                    'chat_id' => $chat_id,
                    'text' => $response_text,
                );
                $this->sendMessage($post_data);
            }
        }
	}
    public function sendMessage($post_data){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    public function editMessageText($post_data){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint = "https://api.telegram.org/bot$bot_token/editMessageText";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    public function deleteMessage($chat_id, $message_id){
        $telegram_api = $this->telegram_api->authKeys();
        $bot_token = $telegram_api['api_key'];
        $api_endpoint_delete = "https://api.telegram.org/bot$bot_token/deleteMessage";
        $post_data_delete = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_endpoint_delete);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_delete);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    public function trackPhrase($chat_id, $message_id, $type){
        $phrase_data = $this->Telegram_bot_model->getTrackPhraseData($chat_id);
        $phrases = array();
        foreach ($phrase_data as $pd) {      
        $row_array = array(
                'text' => $pd['phrase'], 'callback_data' => 'phrase_command:'.$pd['chat_id'].':'.$pd['phrase']
            );
            array_push($phrases, $row_array);
        }

        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($phrases as $phrase) {
            $keyboard['inline_keyboard'][] = array($phrase);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_new_phrase'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;
        $encoded_keyboard = json_encode($keyboard);

        $response_text = "<b>Tracked Phrases</b> \n\nAdd or remove phrases so you get notified when they are mentioned.";
        
        if($type == 'edit'){
            $post_data = array(
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->editMessageText($post_data);
        }
        else{
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }
    }
    
    public function trackUsers($chat_id, $message_id, $type){
        $user_data = $this->Telegram_bot_model->getTrackUsersData($chat_id);
        $users = array();
        foreach ($user_data as $ud) {      
        $row_array = array(
                'text' => $ud['username'], 'callback_data' => 'user_command:'.$ud['chat_id'].':'.$ud['username']
            );
            array_push($users, $row_array);
        }
        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($users as $user) {
            $keyboard['inline_keyboard'][] = array($user);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_new_users'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;
        $encoded_keyboard = json_encode($keyboard);

        $response_text = "<b>Tracked Users</b> \n\nGet notified for new posts from other users.";
        if ($type == 'edit'){
            $post_data = array(
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->editMessageText($post_data);
        }
        else{
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }
    }
    public function ignoredUsers($chat_id, $message_id, $type){
        $user_data = $this->Telegram_bot_model->getIgnoredUsersData($chat_id);
        $users = array();
        foreach ($user_data as $ud) {      
        $row_array = array(
                'text' => $ud['username'], 'callback_data' => 'user_ignore_command:'.$ud['chat_id'].':'.$ud['username']
            );
            array_push($users, $row_array);
        }

        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($users as $user) {
            $keyboard['inline_keyboard'][] = array($user);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_ignore_users'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;
        $encoded_keyboard = json_encode($keyboard);

        $response_text = "<b>Ignored Users</b> \n\nAdd or remove ignored users so you don't get notifications from them.";
        if($type == 'edit'){
            $post_data = array(
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->editMessageText($post_data);
        }
        else{
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }
        
    }
    public function trackTopics($chat_id, $message_id, $type){
        $topic_data = $this->Telegram_bot_model->getTrackTopicsData($chat_id);
        $topics = array();
        foreach ($topic_data as $td) {      
            $row_array = array(
                'text' => $td['title'], 'callback_data' => 'topic_command:'.$td['chat_id'].':'.$td['topic_id']
            );
            array_push($topics, $row_array);
        }

        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($topics as $phrase) {
            $keyboard['inline_keyboard'][] = array($phrase);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_new_topic'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;
        $encoded_keyboard = json_encode($keyboard);

        $response_text = "<b>Tracked Topics</b> \n\nAdd or remove topic threads so you get notified of new replies.";
        if($type == 'edit'){
            $post_data = array(
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->editMessageText($post_data);
        }
        else{
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }
    }
    public function trackBoards($chat_id, $message_id, $type){
        $board_data = $this->Telegram_bot_model->getTrackBoardsData($chat_id);
        $boards = array();
        foreach ($board_data as $bd) {      
            $row_array = array(
                'text' => $bd['board_name'], 'callback_data' => 'board_command:'.$bd['chat_id'].':'.$bd['board_id']
            );
            array_push($boards, $row_array);
        }

        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($boards as $board) {
            $keyboard['inline_keyboard'][] = array($board);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_new_board'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;
        $encoded_keyboard = json_encode($keyboard);

        $response_text = "<b>Tracked Boards</b> \n\nAdd or remove Boards and get notified for every new topics.";
        if($type == 'edit'){
            $post_data = array(
                'message_id' => $message_id,
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->editMessageText($post_data);
        }
        else{
            $post_data = array(
                'chat_id' => $chat_id,
                'text' => $response_text,
                'reply_markup' => $encoded_keyboard,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }
    }


    # MANUAL MESSAGING SUBSCRIBERS
    public function sendMessageToAllSubsribers(){
        $users_data = $this->Telegram_bot_model->getAllUsersData();
        $altt_username = $users_data['altt_username'];
        $user_count = count($users_data);
$response_text = 
"Hello <b>$altt_username</b>,

Thank you for using the AltcoinsTalks Telegram Notifier bot. Here's a little summary of the bot as of January 31 since it was published.

<b>Features</b>
- Mention/quote notification
- Stop notification/unsubscribe
- Track phrase
- Track user posts
- Ignore users
- Track replies in a topic thread

<b>Current active users</b>
$user_count

These features can be accessed and used using the /menu command. If you encounter bugs and issues, feel free to post them on this <a href='https://www.altcoinstalks.com/index.php?topic=315728.0'>thread</a>.
Have a nice day a head!

Cheers!!! 🥂🥂🥂
";
        foreach($users_data as $users){
            $post_data = array(
                'chat_id' => $users_data['chat_id'],
                'text' => $response_text,
                'parse_mode'=> 'html',
            );
            $this->sendMessage($post_data);
        }

        // $post_data = array(
        //     'chat_id' => '625982027',
        //     'text' => $response_text,
        //     'parse_mode'=> 'html',
        // );
        // $this->sendMessage($post_data);
        $response = array(
            'status'=>true,
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    # FOR TESTING ONLY
    public function showTestData(){
        $chat_id = "625982027";
        $phrase_data = $this->Telegram_bot_model->getTrackPhraseData($chat_id);
        $phrases = array();
        foreach ($phrase_data as $pd) {      
            $row_array = array(
                'text' => $pd['phrase'], 'callback_data' => $pd['chat_id'].':'.$pd['phrase']
            );
            array_push($phrases, $row_array);
        }

        $keyboard = array(
            'inline_keyboard' => array()
        );
        foreach ($phrases as $phrase) {
            $keyboard['inline_keyboard'][] = array($phrase);
        }
        $additionalCommands = array(
            array('text' => '➕ Add new', 'callback_data' => 'add_new_phrase'),
            array('text' => '↩️ Go Back', 'callback_data' => 'go_back')
        );
        $keyboard['inline_keyboard'][] = $additionalCommands;

        $this->output->set_content_type('application/json')->set_output(json_encode($keyboard));
    }
 
}