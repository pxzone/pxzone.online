<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Scrapper extends CI_Controller {
    function __construct (){
        parent::__construct();
        $this->load->model('Scrapper_model');
        $this->load->model('Telegram_bot_model');
        $this->load->model('Telegram_bot_test_model');
        $this->load->library('telegram_api');
    }

    # ACCESS USING CRON JOB EVERY 1 MINUTE
    public function scrapeAlttForum() {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $msg_id_data = $this->Scrapper_model->getAlttMsgID();
            $this->scrapeRecentPosts($login_page_data, $msg_id_data['msg_id'], $msg_id_data['attempt']);
        }
        else{
            echo 'error';
        }
	}
    public function scrapeRecentPosts($login_page_data, $msg_id, $attempt)
    {
        $user = "";
        $subjectContent = "";
        $postContent = "";
        $subject_url = "";

        $forum_url = $this->getRedirectedURL($msg_id);
        $ch = curl_init($forum_url);
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

        # GET TOPIC ID
        $long_url = explode(".msg", $forum_url);
        $topic = explode("=", $long_url[0]);
        if(stripos($forum_url,"PHPSESSID") !== false){
            $topic_id = $topic[2];
        }
        else{
            $topic_id = $topic[1];
        }

        # GET USERNAME
        $aWith_id = $xpath->query('//a[@id="msg'.$msg_id.'"]')->item(0);
        if ($aWith_id) {
            $posterAnchor = $xpath->query('following::div[@class="poster"][1]//a', $aWith_id)->item(0);
            if ($posterAnchor) {
                $user = $posterAnchor->textContent;
            }
        } 

        # GET POST SUBJECT
        $subjectContentElements = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a');
        foreach ($subjectContentElements as $subjectContentElements) {
            $subject_content = $subjectContentElements->textContent;
        }

        # GET POST URL
        $aTag = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a')->item(0);
        if ($aTag) {
            $subject_url = $aTag->getAttribute('href');
        }

        # GET POST WHOLE CONTENT INCLUDING DIVS/BLOCKQUOTE
        $postContentElements = $xpath->query('//div[@id="msg_'.$msg_id.'"][position() = 1]')->item(0);
        if($postContentElements){
            $post_content = $postContentElements->textContent;
        }

        # GET POST CONTENT WITHOUT OTHER DIVS/BLOCKQUOTE
        $target_div = $xpath->query('//*[@id="msg_'.$msg_id.'"]')->item(0);
        $text_content = '';
        if($target_div){
            $divs_to_remove = $xpath->query('.//div', $target_div);
            foreach ($divs_to_remove as $div) {
                $div->nodeValue = '';
            }
            $blockquotes_to_remove = $xpath->query('.//blockquote', $target_div);
            foreach ($blockquotes_to_remove as $blockquote) {
                $blockquote->nodeValue = '';
            }
            $text_content = $target_div->textContent;
        }
        
        # CHECK IF POST IS EDITED
        $edited_post = $xpath->query('//*[@id="modified_$msg_id"]')->item(0);
        if ($edited_post) {
            // Check if there is an <em> tag inside the specific div
            $emTags = $xpath->query('.//em', $edited_post);
    
            if ($emTags->length > 0) {
                $edited_post = true;
            } else {
                $edited_post = false;
            }
        }

        if($user){
            $data = array(
                'response'=>true,
                'msg_id'=>$msg_id,
                'topic_id'=>$topic_id,
                'poster_username'=>$user, // post author
                'subject_url'=>$subject_url,
                'subject'=>$subject_content,
                'post'=>$post_content,
                'tg_post'=>$text_content,
                'edited_post'=>$edited_post,
            );
            $this->Telegram_bot_model->notifyUser($data);
            // $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
            $this->Scrapper_model->insertNewMsgID($msg_id);
            $message = "Scrapper Status: Okay [msg_id $msg_id]";
            $this->Scrapper_model->insertSystemActivityLog($message);
            $data_res = array(
                'status'=>true,
                'msg_id'=>$msg_id,
            );
        }
        else{
            # CHECK POST CONTENT IF OFF LIMITs / DELETED POST
            $fatal_error_page = $xpath->query('//div[@id="fatal_error"]')->item(0);
            $expression = "//*[@id='fatal_error']//div[contains(concat(' ', normalize-space(@class), ' '), ' padding ')]";
            $fatal_error_message = $xpath->query($expression);

            if ($fatal_error_page) {
                foreach ($fatal_error_message as $fatal_error_message) {
                    $fatal_error_message = $fatal_error_message->textContent . PHP_EOL;
                }

                if(stripos($fatal_error_message, "topic doesn't exist on this board" ) !== false){
                    $this->Scrapper_model->insertNewMsgID($msg_id);
                    $message = "Scrapper Status: Error. Topic doesn't exist on this board. [msg_id ".$msg_id."]";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                }
                else if(stripos($fatal_error_message, "missing or off limits to you" ) !== false){
                    $message = "Scrapper Status: Error. Page doesn't exist yet. [msg_id ".$msg_id."]";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    $fatal_error_page = $fatal_error_page->textContent;
                }
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                    'error_message'=>$fatal_error_message,
                );
            }
            # EXCLUDE POST AFTER XX FAILED ATTEMPT
            else if($attempt >= 3){
                $this->Scrapper_model->insertNewMsgID($msg_id);
                $message = "Scrapper Status: Error. Insert new msg_id due to frequent error";
                $this->Scrapper_model->insertSystemActivityLog($message);
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                    'error_message'=>"Frequent error",
                );
            }
            # ADD XX ATTEMPT AND SCRAPE AGAIN
            else{
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                    'error_message'=>$fatal_error_message,
                );
                $this->Scrapper_model->updateMsgIdAttempt($msg_id, $attempt);
                $message = "Scrapper Status: Error. Can't scrap data. Last msg_id ".$msg_id;
                $this->Scrapper_model->insertSystemActivityLog($message);
            }
            
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }

    # ACCESS USING CRON JOB EVERY 5 MINUTE
    public function scrapeAlttForumUserData() {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $this->scrapeUserProfile($login_page_data);
        }
        else{
            echo 'error';
        }
	}
    # GET KARMA COUNTS
    public function scrapeUserProfile($login_page_data)
    {
        $user_data = $this->Telegram_bot_model->getUserData();
        foreach($user_data as $ud){
            if(!empty($user['altt_uid'])){
                $user = $this->Telegram_bot_model->getUserDatabyAlttID($ud['altt_uid']);
                $altt_uid = $user['altt_uid'];
                // $altt_uid =  "97172";
                $forum_url = "https://www.altcoinstalks.com/index.php?action=profile;u=$altt_uid";
                $ch = curl_init($forum_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
                $html = curl_exec($ch);
                if (curl_errno($ch)) {
                    $message = 'Curl error during request: ' . curl_error($ch);
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    exit;
                }
                curl_close($ch);
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $xpath = new DOMXPath($dom);
        
                # GET USERNAME
                $username_element = $xpath->query('//div[@class="username"]/h4/text()')->item(0);
                if ($username_element) {
                    $username = trim($username_element->nodeValue);
                }

                # GET KARMA
                $karma_element = $xpath->query('//dt[text()="Karma: "]/following-sibling::dd')->item(0);
                if ($karma_element) {
                    $karma = (int)trim($karma_element->nodeValue);
                }
                else{$karma = 0;}

                if(!empty($username) && $username == $user['altt_username']){
                    $data = array(
                        'status'=>true,
                        'chat_id'=>$user['chat_id'],
                        'username'=>$username,
                        'current_karma'=>$karma,
                        'prev_karma'=>$user['karma'],
                    );
                    
                    if((int)$karma !== (int)$user['karma']){
                        $send_status = $this->Telegram_bot_model->notifyKarmaTransaction($data);
                        if($send_status){
                            $message = "Scrapper Status: Okay. Scraped username [$username]";
                            $this->Scrapper_model->insertSystemActivityLog($message);
                        }
                    }
                    $data_res = array(
                        'status'=>true,
                        'scrape'=>count($user_data),
                    );
                }
            }
            sleep(5);
        }
        $data_res = array(
            'status'=>false,
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }

    # ACCESS USING CRON JOB EVERY 5 MINUTE
    public function scrapeAlttForumForEditedPosts() {
        $scraped_post = $this->Telegram_bot_model->checkScrapedPost();

        if(!empty($scraped_post)){
            foreach($scraped_post as $sp){
                $login_page_data = $this->Scrapper_model->scrapeLoginPage();
                $login_forum = $this->Scrapper_model->loginForum($login_page_data);
                if($login_forum){
                    $this->scrapeEditedPosts($login_page_data, $sp['msg_id'], $sp['post_content']);
                }
                else{
                    echo 'error';
                }
                sleep(3); // sleep 5 seconds then scrape again
                $this->Telegram_bot_model->deleteScrapedPost($sp['msg_id']);
            }
            $data_res = array(
                'status'=>true,
                'count'=>count($scraped_post)
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
        }
        
	}
    public function scrapeEditedPosts($login_page_data, $msg_id, $initial_scraped_post)
    {
        $user = "";
        $subjectContent = "";
        $postContent = "";
        $subject_url = "";

        $forum_url = $this->getRedirectedURL($msg_id);
        $ch = curl_init($forum_url);
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
        
        # GET USERNAME
        $aWith_id = $xpath->query('//a[@id="msg'.$msg_id.'"]')->item(0);
        if ($aWith_id) {
            $posterAnchor = $xpath->query('following::div[@class="poster"][1]//a', $aWith_id)->item(0);
            if ($posterAnchor) {
                $user = $posterAnchor->textContent;
            }
        } 

        # GET POST SUBJECT
        $subjectContentElements = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a');
        foreach ($subjectContentElements as $subjectContentElements) {
            $subject_content = $subjectContentElements->textContent;
        }

        # GET POST URL
        $aTag = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a')->item(0);
        if ($aTag) {
            $subject_url = $aTag->getAttribute('href');
        }

        # GET POST WHOLE CONTENT INCLUDING DIVS/BLOCKQUOTE
        $postContentElements = $xpath->query('//div[@id="msg_'.$msg_id.'"][position() = 1]')->item(0);
        if($postContentElements){
            $post_content = $postContentElements->textContent;
        }

        # GET POST CONTENT WITHOUT OTHER DIVS/BLOCKQUOTE
        $target_div = $xpath->query('//*[@id="msg_'.$msg_id.'"]')->item(0);
        $text_content = '';
        if($target_div){
            $divs_to_remove = $xpath->query('.//div', $target_div);
            foreach ($divs_to_remove as $div) {
                $div->nodeValue = '';
            }
            $blockquotes_to_remove = $xpath->query('.//blockquote', $target_div);
            foreach ($blockquotes_to_remove as $blockquote) {
                $blockquote->nodeValue = '';
            }
            $text_content = $target_div->textContent;
        }
        
        # CHECK IF POST IS EDITED
        $edited_post_status = $xpath->query('//*[@id="modified_$msg_id"]')->item(0);
        if ($edited_post_status) {
            // Check if there is an <em> tag inside the specific div
            $emTags = $xpath->query('.//em', $edited_post_status);
    
            if ($emTags->length > 0) {
                $edited_post_status = true;
            } else {
                $edited_post_status = false;
            }
        }

        # IF POST IS EDITED
        if($user && $edited_post_status == true){
            $initial_scraped_post = str_word_count($initial_scraped_post, 1);
            $edited_post = str_word_count($post_content, 1);

            // Find differences using array_diff
            $added = array_diff($edited_post, $initial_scraped_post);
            $removed = array_diff($initial_scraped_post, $edited_post);

            // Display differences
            if (!empty($added) || !empty($removed)) {
                # CHANGES FOUND
                $new_edited_post = implode(' ', $added).' '.implode(' ', $removed);
                
                $data = array(
                    'response'=>true,
                    'msg_id'=>$msg_id,
                    'poster_username'=>$user, // post author
                    'subject_url'=>$subject_url,
                    'subject'=>$subject_content,
                    'post'=>$new_edited_post,
                    'tg_post'=>$text_content,
                    'edited_post'=>$edited_post,
                );
    
                $this->Telegram_bot_model->notifyUser($data);
                $message = "Scrapper Status: Okay. Scraped again [msg_id $msg_id]";
                $this->Scrapper_model->insertSystemActivityLog($message);
                $data_res = array(
                    'status'=>true,
                    'msg_id'=>$msg_id,
                );
            } else {
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                );
            }
        }
        // $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }
    
    public function getRedirectedURL($msg_id){
        $url = "https://www.altcoinstalks.com/index.php?msg=".$msg_id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            exit;
        }
        $topic_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $topic_url;

    }
    
    public function scrapeAlttForumOption2($msg_id) {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $this->scrapePostsOption2($login_page_data, $msg_id);
        }
        else{
            echo 'error';
        }
	}
    public function scrapePostsOption2($login_page_data, $msg_id){
        {
            $user = "";
            $subjectContent = "";
            $postContent = "";
            $subject_url = "";
    
            $forum_url = $this->getRedirectedURL($msg_id);
            $ch = curl_init($forum_url);
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
            
            # GET TOPIC ID
            $long_url = explode(".msg", $forum_url);
            $topic = explode("=", $long_url[0]);
            if(stripos($forum_url,"PHPSESSID") !== false){
                $topic_id = $topic[2];
            }
            else{
                $topic_id = $topic[1];
            }


            # GET USERNAME
            $aWith_id = $xpath->query('//a[@id="msg'.$msg_id.'"]')->item(0);
            if ($aWith_id) {
                $posterAnchor = $xpath->query('following::div[@class="poster"][1]//a', $aWith_id)->item(0);
                if ($posterAnchor) {
                    $user = $posterAnchor->textContent;
                }
            } 
    
            # GET POST SUBJECT
            $subjectContentElements = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a');
            foreach ($subjectContentElements as $subjectContentElements) {
                $subject_content = $subjectContentElements->textContent;
            }
    
            # GET POST URL
            $aTag = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a')->item(0);
            if ($aTag) {
                $subject_url = $aTag->getAttribute('href');
            }
    
            # GET POST WHOLE CONTENT INCLUDING DIVS/BLOCKQUOTE
            $postContentElements = $xpath->query('//div[@id="msg_'.$msg_id.'"][position() = 1]')->item(0);
            if($postContentElements){
                $post_content = $postContentElements->textContent;
            }
    
            # GET POST CONTENT WITHOUT OTHER DIVS/BLOCKQUOTE
            $target_div = $xpath->query('//*[@id="msg_'.$msg_id.'"]')->item(0);
            $text_content = '';
            if($target_div){
                $divs_to_remove = $xpath->query('.//div', $target_div);
                foreach ($divs_to_remove as $div) {
                    $div->nodeValue = '';
                }
                $blockquotes_to_remove = $xpath->query('.//blockquote', $target_div);
                foreach ($blockquotes_to_remove as $blockquote) {
                    $blockquote->nodeValue = '';
                }
                $text_content = $target_div->textContent;
            }
            
            # CHECK IF POST IS EDITED
            $edited_post = $xpath->query('//*[@id="modified_$msg_id"]')->item(0);
            if ($edited_post) {
                // Check if there is an <em> tag inside the specific div
                $emTags = $xpath->query('.//em', $edited_post);
        
                if ($emTags->length > 0) {
                    $edited_post = true;
                } else {
                    $edited_post = false;
                }
            }
            
            $data = array(
                'status'=>false,
                'msg_id'=>$msg_id,
            );
            if($user){
                $data = array(
                    'response'=>true,
                    'msg_id'=>$msg_id,
                    'topic_id'=>$topic_id,
                    'poster_username'=>$user, // post author
                    'subject_url'=>$subject_url,
                    'subject'=>$subject_content,
                    'post'=>$post_content,
                    'tg_post'=>$text_content,
                    'edited_post'=>$edited_post,
                );
                $this->Telegram_bot_test_model->notifyUser($data);
                // $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
                // $this->Scrapper_model->insertNewMsgID($msg_id);
                $message = "Scrapper Status: Okay [msg_id $msg_id][Option]";
                $this->Scrapper_model->insertSystemActivityLog($message);
                $data_res = array(
                    'status'=>true,
                    'msg_id'=>$msg_id,
                );
            }
            // else{
            //     # CHECK POST CONTENT IF OFF LIMITs / DELETED POST
            //     $fatal_error_page = $xpath->query('//div[@id="fatal_error"]')->item(0);
            //     $expression = "//*[@id='fatal_error']//div[contains(concat(' ', normalize-space(@class), ' '), ' padding ')]";
            //     $fatal_error_message = $xpath->query($expression);
    
            //     if ($fatal_error_page) {
            //         foreach ($fatal_error_message as $fatal_error_message) {
            //             $fatal_error_message = $fatal_error_message->textContent . PHP_EOL;
            //         }
    
            //         if(stripos($fatal_error_message, "topic doesn't exist on this board" ) !== false){
            //             $this->Scrapper_model->insertNewMsgID($msg_id);
            //             $message = "Scrapper Status: Error. Topic doesn't exist on this board. [msg_id ".$msg_id."]";
            //             $this->Scrapper_model->insertSystemActivityLog($message);
            //         }
            //         else if(stripos($fatal_error_message, "missing or off limits to you" ) !== false){
            //             $message = "Scrapper Status: Error. Page doesn't exist yet. [msg_id ".$msg_id."]";
            //             $this->Scrapper_model->insertSystemActivityLog($message);
            //             $fatal_error_page = $fatal_error_page->textContent;
            //         }
            //         $data_res = array(
            //             'status'=>false,
            //             'msg_id'=>$msg_id,
            //             'error_message'=>$fatal_error_message,
            //         );
            //     }
            //     # EXCLUDE POST AFTER XX FAILED ATTEMPT
            //     else if($attempt >= 3){
            //         $this->Scrapper_model->insertNewMsgID($msg_id);
            //         $message = "Scrapper Status: Error. Insert new msg_id due to frequent error";
            //         $this->Scrapper_model->insertSystemActivityLog($message);
            //         $data_res = array(
            //             'status'=>false,
            //             'msg_id'=>$msg_id,
            //             'error_message'=>"Frequent error",
            //         );
            //     }
            //     # ADD XX ATTEMPT AND SCRAPE AGAIN
            //     else{
            //         $data_res = array(
            //             'status'=>false,
            //             'msg_id'=>$msg_id,
            //             'error_message'=>$fatal_error_message,
            //         );
            //         $this->Scrapper_model->updateMsgIdAttempt($msg_id, $attempt);
            //         $message = "Scrapper Status: Error. Can't scrap data. Last msg_id ".$msg_id;
            //         $this->Scrapper_model->insertSystemActivityLog($message);
            //     }
                
            // }
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }
    
}
