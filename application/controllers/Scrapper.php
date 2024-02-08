<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Scrapper extends CI_Controller {
    function __construct (){
        parent::__construct();
        $this->load->model('Scrapper_model');
        $this->load->model('Telegram_bot_model');
        $this->load->model('Telegram_bot_test_model');
        $this->load->library('Telegram_api');
        $this->load->library('Api_auth');
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

        # GET BOARD ID
        $board_id = "";
        $liNodes = $xpath->query('(//div[@class="navigate_section"]/ul/li)[last()-1]');
        if ($liNodes->length > 0) {
            $anchorNode = $xpath->query('.//a', $liNodes->item(0))->item(0);
            if ($anchorNode !== null) {
                $board_href = $anchorNode->getAttribute('href');
                $board_id = explode("=",$board_href);
                $board_id = substr($board_id[1], 0, -2);
             }
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

        if($user){
            $data = array(
                'response'=>true,
                'msg_id'=>$msg_id,
                'topic_id'=>$topic_id,
                'board_id'=>$board_id,
                'poster_username'=>$user,
                'subject_url'=>$subject_url,
                'subject'=>$subject_content,
                'post'=>$post_content,
                'tg_post'=>$text_content,
            );
            $this->Telegram_bot_model->notifyUser($data);
            $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
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

            # EXCLUDE POST AFTER XX FAILED ATTEMPT
            
            if ($fatal_error_page) {
                foreach ($fatal_error_message as $fatal_error_message) {
                    $fatal_error_message = $fatal_error_message->textContent . PHP_EOL;
                }

                if($attempt >= 15){
                    $this->Scrapper_model->insertNewMsgID($msg_id);
                    $message = "Scrapper Status: Error. Insert new msg_id due to frequent error";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    
                    $data_res = array(
                        'status'=>false,
                        'msg_id'=>$msg_id,
                        'error_message'=>"Frequent error",
                    );
                }
                if(stripos($fatal_error_message, "topic doesn't exist on this board" ) !== false){
                    $this->Scrapper_model->insertNewMsgID($msg_id);
                    $message = "Scrapper Status: Error. Topic doesn't exist on this board. [msg_id ".$msg_id."]";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                }
                else if(stripos($fatal_error_message, "missing or off limits to you" ) !== false){
                    $message = "Scrapper Status: Error. Page doesn't exist yet. [msg_id ".$msg_id."]";
                    $this->Scrapper_model->updateMsgIdAttempt($msg_id, $attempt);
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    $fatal_error_page = $fatal_error_page->textContent;
                }
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                    'error_message'=>$fatal_error_message,
                );
            }
            else if($attempt >= 15){
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
                // $this->scrapeAlttForum(); //scrape again
            }
            
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }

    public function alttScrapeForumRecentPosts(){
      
        // recent post page 2, start=10
        $forum_url_page_2 = "https://www.altcoinstalks.com/index.php?action=recent;start=10"; 
        $scrape_data_page_2 = $this->scrapeForumRecentPosts($forum_url_page_2);
        foreach($scrape_data_page_2 as $scrape2){
            $check_msg_id = $this->Telegram_bot_model->checkPostMsgID($scrape2['msg_id']);
            if($check_msg_id <= 0){
                $this->Telegram_bot_model->notifyUser($scrape2);
                $this->Telegram_bot_model->saveScrapedData($scrape2); // SAVED SCRAPED DATA
                $msg_id = $scrape2['msg_id'];
                $message = "Status: okay. [$msg_id]";
                $this->Scrapper_model->insertSystemActivityLog($message);
            }
        }

        $forum_url = "https://www.altcoinstalks.com/index.php?action=recent"; 
        $scrape_data = $this->scrapeForumRecentPosts($forum_url);
        foreach($scrape_data as $scrape){
            $check_msg_id = $this->Telegram_bot_model->checkPostMsgID($scrape['msg_id']);
            if($check_msg_id <= 0){
                $this->Telegram_bot_model->notifyUser($scrape);
                $this->Telegram_bot_model->saveScrapedData($scrape); // SAVED SCRAPED DATA
                $msg_id = $scrape['msg_id'];
                $message = "Status: okay. [$msg_id]";
                $this->Scrapper_model->insertSystemActivityLog($message);
            }
        }

        $data_res = array(
            'status'=>true,
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));

    }
    public function scrapeForumRecentPosts($forum_url) {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $session_id = $login_page_data['session_id'];
            $ch = curl_init($forum_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $html = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Curl error during request: ' . curl_error($ch);
                exit;
            }
            curl_close($ch);
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
                
            $windowbg_elements = $xpath->query('//div[contains(@class, "windowbg")]');
            $result = array();
            foreach ($windowbg_elements as $windowbg_element) {
                # GET BOARD DATA
                $first_anchor_link = $xpath->query('.//div[@class="topic_details"]/h5/a[1]', $windowbg_element)->item(0);
                $board_title = $first_anchor_link ? $first_anchor_link->nodeValue : "";
                $board_url = $first_anchor_link ? $first_anchor_link->getAttribute('href') : "";
                $board_data = explode("board=", $board_url);
                $board_id = substr($board_data[1], 0, -2);

                # GET TOPIC AND MSG DATA
                $second_anchor_link = $xpath->query('.//div[@class="topic_details"]/h5/a[2]', $windowbg_element)->item(0);
                $subject = $second_anchor_link ? $second_anchor_link->nodeValue : "";
                $subject_url = $second_anchor_link ? $second_anchor_link->getAttribute('href') : "";
                $msg_data = explode("#msg", $subject_url);
                $msg_id = $msg_data[1];

                $topic_data = explode("topic=", $subject_url);
                $topic_data2 = explode(".msg", $topic_data[1]);
                $topic_id = $topic_data2[0];

                # GET PROFILE DATA
                $span_anchor_link = $xpath->query('.//div[@class="topic_details"]/span[@class="smalltext"]/strong/a', $windowbg_element)->item(0);
                $username = $span_anchor_link ? $span_anchor_link->nodeValue : "";
                $profile_url = $span_anchor_link ? $span_anchor_link->getAttribute('href') : "";

                # GET POST DATA
                $post_content = $xpath->query('.//div[@class="list_posts"]', $windowbg_element)->item(0)->nodeValue ?? "";
                $list_posts_div = $xpath->query('.//div[@class="list_posts"]', $windowbg_element)->item(0);
                if ($list_posts_div) {
                    $divs_to_remove = $xpath->query('.//div|.//blockquote', $list_posts_div);
                    foreach ($divs_to_remove as $div_to_remove) {
                        $div_to_remove->parentNode->removeChild($div_to_remove);
                    }
                    $post_content_filtered = $list_posts_div->textContent;
                } 

                $row_data = array(
                    'board_id'=>$board_id,
                    'msg_id'=>$msg_id,
                    'topic_id'=>$topic_id,
                    'subject_url'=>$subject_url,
                    'subject'=>$subject,
                    // 'board_url'=>$board_url,
                    // 'board_title'=>$board_title,
                    'poster_username'=>$username,
                    'profile_url'=>$profile_url,
                    'post'=>$post_content,
                    'tg_post'=>$post_content_filtered,
                );
                array_push($result, $row_data);
            }
            return $result;
            // $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }
	}

    # ACCESS USING CRON JOB EVERY 5 MINUTE
    # GET KARMA COUNTS
    public function scrapeAlttForumUserData()
    {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $user_data = $this->Telegram_bot_model->getUserData();
            foreach($user_data as $ud){
                if(!empty($ud['altt_uid'])){
                    $user = $this->Telegram_bot_model->getUserDatabyAlttID($ud['altt_uid']); // validate

                    $altt_uid = $user['altt_uid'];
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
                                $message = "Status: okay. Scraped user [$username]";
                                $this->Scrapper_model->insertSystemActivityLog($message);
                            }
                        }
                        $data_res = array(
                            'status'=>true,
                            'scraped_data'=>count($user_data),
                        );
                    }
                }
                // sleep(1);
            }
            $data_res = array(
                'status'=>false,
            );
            $message = "System: Scraped user profiles [5 mins]";
            $this->Scrapper_model->insertSystemActivityLog($message);
            $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
        }
    }

    # ACCESS USING CRON JOB EVERY 5 MINUTE
    public function scrapeAlttForumForEditedPosts() {
        $scraped_post = $this->Telegram_bot_model->checkScrapedPost();
        // $result = array();
        if(!empty($scraped_post)){
            foreach($scraped_post as $sp){
                $login_page_data = $this->Scrapper_model->scrapeLoginPage();
                $login_forum = $this->Scrapper_model->loginForum($login_page_data);
                if($login_forum){
                    $data_res = $this->scrapeEditedPosts($login_page_data, $sp['msg_id'], $sp['post_content']);
                }
                $this->Telegram_bot_model->insertArchiveScrapedPost($data_res, $sp);
                $this->Telegram_bot_model->deleteScrapedPost($sp['msg_id']);
                // array_push($result, $data_res);
            }
            
        }
        $data_res = array(
            'status'=>true,
            'count'=>count($scraped_post)
        );
        $message = "System: Scraped edited posts [6 mins]";
        $this->Scrapper_model->insertSystemActivityLog($message);
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));

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
        
        
        # GET BOARD ID
        $board_id = "";
        $liNodes = $xpath->query('(//div[@class="navigate_section"]/ul/li)[last()-1]');
        if ($liNodes->length > 0) {
            $anchorNode = $xpath->query('.//a', $liNodes->item(0))->item(0);
            if ($anchorNode !== null) {
                $board_href = $anchorNode->getAttribute('href');
                $board_id = explode("=",$board_href);
                $board_id = substr($board_id[1], 0, -2);
             }
        }

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
        
        # IF POST IF EDITED 
        # Scraped post were saved and compared when the post is rescraped. The difference between the two scraped posts
        # will be scanned for possible mentions and notifications.
        if($user){
            $initial_scraped_post = str_word_count($initial_scraped_post, 1);
            $edited_post = str_word_count($post_content, 1); // from new scraped data on the msg_id

            $added = array_diff($edited_post, $initial_scraped_post);
            $removed = array_diff($initial_scraped_post, $edited_post);

            if (!empty($added) || !empty($removed)) { // check if there's difference between initial_scraped_post and edited_post
                $new_edited_post = implode(' ', $added).' '.implode(' ', $removed); 
                
                $data = array(
                    'response'=>true,
                    'msg_id'=>$msg_id,
                    'topic_id'=>$topic_id,
                    'board_id'=>$board_id,
                    'poster_username'=>$user,
                    'subject_url'=>$subject_url,
                    'subject'=>$subject_content,
                    'post'=>$new_edited_post, 
                    'tg_post'=>$text_content,
                    'edited_post'=>implode(" ",$edited_post),
                );
    
                $this->Telegram_bot_model->notifyUserEditedPost($data);
                $message = "Status: okay. Scraped again [$msg_id]";
                $this->Scrapper_model->insertSystemActivityLog($message);
                return $data;
            } 
            else {
                $data_res = array(
                    'response'=>false,
                    'edited_post_status' => false,
                    'msg_id'=>$msg_id,
                );
                return $data_res;
            }
        }
        else{
            $data_res = array(
                'response'=>false,
                'edited_post_status' => false,
                'msg_id'=>$msg_id,
            );
            return $data_res;
        }
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
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }
    public function scrapeAlttForumRecentPosts() {
        $login_page_data = $this->Scrapper_model->scrapeLoginPage();
        $login_forum = $this->Scrapper_model->loginForum($login_page_data);
        if($login_forum){
            $this->scrapeRecentPostsOption($login_page_data);
        }
        else{
            echo 'error';
        }
	}
    public function scrapeRecentPostsOption($login_page_data){
        {
            $user = "";
            $subjectContent = "";
            $postContent = "";
            $subject_url = "";
            $forum_url = "https://www.altcoinstalks.com/index.php?action=unread";
            // $forum_url = $this->getRedirectedURL();
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
                // $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
                $message = "Scrapper Status: Okay";
                $this->Scrapper_model->insertSystemActivityLog($message);
                $data_res = array(
                    'status'=>true,
                );
            }
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }
}
