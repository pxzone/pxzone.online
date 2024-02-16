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

    public function alttScrapeForumRecentPosts(){
        // recent post page 2, start=10
        $forum_url_page_2 = "https://www.altcoinstalks.com/index.php?action=recent;start=10"; 
        $scrape_data_page_2 = $this->scrapeForumRecentPosts($forum_url_page_2);
        foreach($scrape_data_page_2 as $scrape2){
            $check_msg_id = $this->Telegram_bot_model->checkPostMsgID($scrape2['msg_id']);
            if($check_msg_id <= 0){
                $this->Telegram_bot_model->notifyUser($scrape2);
                date_default_timezone_set('Asia/Manila');
                $topic_data = $this->scrapeTopicData($scrape2['topic_id']);
                $this->Scrapper_model->insertNewBoardData($scrape2['board_id'], $scrape2['board_name']);
                $topic_inserted = $this->Telegram_bot_model->saveScrapedData($scrape2, $topic_data); // SAVED SCRAPED DATA
                
                if($topic_inserted == true){
                    $this->Telegram_bot_model->notifyTrackedBoard($scrape, $topic_data); // NOTIFY TRACKED BOARDS
                }
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
                date_default_timezone_set('Asia/Manila');
                $topic_data = $this->scrapeTopicData($scrape['topic_id']);
                $this->Scrapper_model->insertNewBoardData($scrape['board_id'], $scrape['board_name']);
                $topic_inserted = $this->Telegram_bot_model->saveScrapedData($scrape, $topic_data); // SAVED SCRAPED DATA
                if($topic_inserted == true){
                    $this->Telegram_bot_model->notifyTrackedBoard($scrape, $topic_data); // NOTIFY TRACKED BOARDS
                }

                $msg_id = $scrape['msg_id'];
                $message = "Status: okay. [$msg_id]";
                $this->Scrapper_model->insertSystemActivityLog($message);
            }
        }
        // $this->insertTopicAuthor();
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
                $message = 'Curl error during request: ' . curl_error($ch);
                $this->Scrapper_model->insertSystemActivityLog($message);
                exit;
            }
            curl_close($ch);
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
                
            $windowbg_elements = $xpath->query('//div[contains(@class, "windowbg")]');
            $result = array();
            foreach ($windowbg_elements as $windowbg_element) {
                # GET DATE POSTED
                $date_posted_element = $xpath->query('.//div[@class="topic_details"]/span[@class="smalltext"]/em', $windowbg_element)->item(0);
                date_default_timezone_set('Europe/Rome');
                $date_element = date('Y-m-d').substr($date_posted_element->nodeValue, 10);
                $date_posted = date('Y-m-d H:i:s', strtotime($date_element));

                # GET BOARD DATA
                $first_anchor_link = $xpath->query('.//div[@class="topic_details"]/h5/a[1]', $windowbg_element)->item(0);
                $board_name = $first_anchor_link ? $first_anchor_link->nodeValue : "";
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
                
                # GET POST HTML CONTENT
                $html_content = $xpath->query('.//div[@class="list_posts"]', $windowbg_element);
                foreach ($html_content as $html_content) {
                    $html_post = $dom->saveHTML($html_content);
                }
                $row_data = array(
                    'date_posted'=>$date_posted,
                    'board_id'=>$board_id,
                    'msg_id'=>$msg_id,
                    'topic_id'=>$topic_id,
                    'subject_url'=>$subject_url,
                    'subject'=>$subject,
                    // 'board_url'=>$board_url,
                    'board_name'=>$board_name,
                    'poster_username'=>$username,
                    'html_post'=>$html_post,
                    'profile_url'=>$profile_url,
                    'post'=>$post_content,
                    'tg_post'=>$post_content_filtered,
                );
                array_push($result, $row_data);

                // # CHECK KARMA COUNT
                // $altt_uid = explode("u=", $profile_url);
                // $user = $this->Telegram_bot_model->getUserDatabyAlttID($altt_uid[1]); // validate
                // if(!empty($user)){
                //     $this->scrapeKarmaCount($profile_url, $login_page_data, $user);
                // }
                
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
                    $forum_url = "https://www.altcoinstalks.com/index.php?action=profile;u=".$ud['altt_uid'];
                    $this->scrapeKarmaCount($forum_url, $login_page_data, $user);
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
    public function scrapeKarmaCount($forum_url, $login_page_data, $user){
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
                    date_default_timezone_set('Asia/Manila');
                    $message = "Status: okay. Scraped user [$username]";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    date_default_timezone_set('Europe/Rome');
                    $this->Telegram_bot_model->saveKarmaPoint($data);
                }
            }
            $data_res = array(
                'status'=>true,
            );
        }
    }

    public function scrapeAlttForumForEditedPosts() {
        $scraped_post = $this->Telegram_bot_model->checkScrapedPost();
        $result = array(
           
        );
        if(!empty($scraped_post)){
            foreach($scraped_post as $sp){
                $current_time = time();
                $scrape_time = strtotime($sp['created_at']);
                $time_difference = $current_time - $scrape_time;
                $data_res = "";
                if ($time_difference > 300) {
                    $login_page_data = $this->Scrapper_model->scrapeLoginPage();
                    $login_forum = $this->Scrapper_model->loginForum($login_page_data);
                    if($login_forum){
                        $data_res = $this->scrapeEditedPosts($login_page_data, $sp['msg_id'], $sp['post_content']);
                    }
                    
                    if($data_res['response'] == true){
                        date_default_timezone_set('Asia/Manila');
                        $this->Telegram_bot_model->insertArchiveScrapedPost($data_res, $sp, 'active');
                        $this->Telegram_bot_model->deleteScrapedPost($sp['msg_id']);
                        array_push($result, $data_res);
                    }
                    else if($data_res['response'] == false && $data_res['error_message'] == "Moved to trash" ){
                        date_default_timezone_set('Asia/Manila');
                        // $this->Telegram_bot_model->insertArchiveScrapedPost($data_res, $sp, 'deleted');
                        $this->Telegram_bot_model->deleteScrapedPost($sp['msg_id']);
                        array_push($result, $data_res);
                    }
                    else{
                        array_push($result, $data_res);
                    }
                }
            }
        }
        $count = count($result);
        $message = "System: Scraped edited posts. post count: $count [6 mins]";
        $this->Scrapper_model->insertSystemActivityLog($message);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
	}

    
    public function scrapeEditedPosts($login_page_data, $msg_id, $initial_scraped_post)
    {
        $user = "";
        $forum_url = $this->getRedirectedURL($msg_id);
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
        
        $fatal_error_page = $xpath->query('//div[@id="fatal_error"]')->item(0);
        $expression = "//*[@id='fatal_error']//div[contains(concat(' ', normalize-space(@class), ' '), ' padding ')]";
        $fatal_error_message = $xpath->query($expression);

        if(strpos($html, "Please login below or") !== false){
            $data_res = array(
                'response'=>false,
                'msg_id'=>$msg_id,
                'error_message'=>"Moved to trash",
            );
            return $data_res;
            exit();
        }
        else if ($fatal_error_page) {
            foreach ($fatal_error_message as $fatal_error_message) {
                $fatal_error_message = $fatal_error_message->textContent . PHP_EOL;
            }

            if(stripos($fatal_error_message, "topic doesn't exist on this board" ) !== false){
                $message = "Scrapper Status: Error. Topic doesn't exist on this board. [msg_id ".$msg_id."]";
                $this->Scrapper_model->insertSystemActivityLog($message);
            }
            else if(stripos($fatal_error_message, "missing or off limits to you" ) !== false){
                $message = "Scrapper Status: Error. Page doesn't exist yet. [msg_id ".$msg_id."]";
                $this->Scrapper_model->insertSystemActivityLog($message);
                $fatal_error_page = $fatal_error_page->textContent;
            }
            $data_res = array(
                'response'=>false,
                'msg_id'=>$msg_id,
                'error_message'=>$fatal_error_message,
            );
            return $data_res;
            exit();
        }

        else{
            # HTML CONTENT
            $post_div = $xpath->query('//div[@class="post"]//div[@id="msg_'.$msg_id.'"]')->item(0);
            $html_post = $dom->saveHTML($post_div);

            # GET DATE POSTED
            $date_posted_element = $xpath->query("//h5[@id='subject_".$msg_id."']/following-sibling::div[contains(@class, 'smalltext')]");
            date_default_timezone_set('Europe/Rome');
            $date_posted = date('Y-m-d H:i:s');
            foreach ($date_posted_element as $date_posted_node) {
                $datetime = trim(str_replace(array('at',' »'), '', $date_posted_node->nodeValue));
            }

            if(stripos($datetime, "Today") !== false ){
                $datetime = explode("on: ", $datetime);
                $date_today = str_replace("Today", date('Y-m-d'), $datetime[1]);
                $date_posted = date('Y-m-d H:i:s', strtotime($date_today));
            } 
            else{
                $datetime = explode("on: ", $datetime);
                $date_posted = date('Y-m-d H:i:s', strtotime($datetime[1]));
            }

            # GET BOARD ID
            $board_id = "";
            $li_nodes = $xpath->query('(//div[@class="navigate_section"]/ul/li)[last()-1]');
            if ($li_nodes->length > 0) {
                $anchor_node = $xpath->query('.//a', $li_nodes->item(0))->item(0);
                if ($anchor_node !== null) {
                    $board_href = $anchor_node->getAttribute('href');
                    $board_name = $anchor_node->textContent;
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
                        'date_posted'=>$date_posted,
                        'msg_id'=>$msg_id,
                        'topic_id'=>$topic_id,
                        'board_id'=>$board_id,
                        'board_name'=>$board_name,
                        'poster_username'=>$user,
                        'subject_url'=>$subject_url,
                        'subject'=>$subject_content,
                        'post'=>$new_edited_post, 
                        'tg_post'=>$text_content,
                        'html_post'=>$html_post,
                        'edited_post'=>implode(" ", $edited_post),
                    );
        
                    $this->Telegram_bot_model->notifyUserEditedPost($data);
                    $message = "Status: okay. Scraped again [$msg_id]";
                    $this->Scrapper_model->insertSystemActivityLog($message);
                    return $data;
                } 
                else {
                    $data = array(
                        'response'=>true,
                        'date_posted'=>$date_posted,
                        'msg_id'=>$msg_id,
                        'topic_id'=>$topic_id,
                        'board_id'=>$board_id,
                        'board_name'=>$board_name,
                        'poster_username'=>$user,
                        'subject_url'=>$subject_url,
                        'subject'=>$subject_content,
                        'post'=>$post_content, 
                        'tg_post'=>$text_content,
                        'html_post'=>$html_post,
                        'edited_post'=>implode(" ", $edited_post),
                    );
                    return $data;
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
    }
    public function getRedirectedURL($msg_id){
        $url = "https://www.altcoinstalks.com/index.php?msg=".$msg_id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $message =  'Curl error: ' . curl_error($ch);
            $this->Scrapper_model->insertSystemActivityLog($message);
            exit;
        }
        $topic_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $topic_url;
    }
    
    // public function scrapeBoardTopicData() {
    //     $board_data = $this->db->SELECT('chat_id, board_id')->GET('tracked_board_tbl')->result_array();
    //     $data_arr = array();
    //     foreach($board_data as $board){
    //             $forum_url = "https://www.altcoinstalks.com/index.php?board=".$board['board_id'];
    //             $ch = curl_init($forum_url);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             $html = curl_exec($ch);
    //             if (curl_errno($ch)) {
    //                 $message = 'Curl error during request: ' . curl_error($ch);
    //                 $this->Scrapper_model->insertSystemActivityLog($message);
    //                 exit;
    //             }
    //             curl_close($ch);
    //             $dom = new DOMDocument();
    //             @$dom->loadHTML($html);
    //             $xpath = new DOMXPath($dom);

    //             $tbody = $xpath->query('//table[@class="table_grid"]/tbody')->item(0);
    //             if ($tbody) {
    //                 // Find all td elements with class "subject" inside the tbody
    //                 $tdElements = $xpath->query('.//td[contains(@class, "subject")]', $tbody);
                
    //                 foreach ($tdElements as $tdElement) {
    //                     // Find the span element inside the current td element with an ID containing "msg_"
    //                     $span = $xpath->query('.//span[contains(@id, "msg_")]/a', $tdElement)->item(0);
                
    //                     if ($span) {
    //                         $anchorText = $span->nodeValue;
    //                         $anchorLink = $span->getAttribute('href');
                
    //                         $status = true;
    //                         $topic_name = $anchorText;
    //                         $topic_url = $anchorLink;
    //                     }

    //                     $pTag = $xpath->query('.//p', $tdElement)->item(0);
    //                     $anchorInPTag = $xpath->query('.//a', $pTag)->item(0);
    //                     $username = $anchorInPTag->nodeValue;
    //                     $profile_url = $anchorInPTag->getAttribute('href');

    //                     $topic_arr = explode("topic=", $topic_url);
    //                     $topic_id = $topic_arr[1];

    //                     $row_array = array(
    //                         // 'status' => $status,
    //                         'board_id'=>$board['board_id'],
    //                         'topic_name'=>$topic_name,
    //                         'topic_id'=>$topic_id,
    //                         'username'=>$username,
    //                         'created_at'=>date('Y-m-d H:i:s'),
    //                     );
    //                     array_push($data_arr, $row_array);
    //                     // print_r($result);
    //                 }
    //             }
    //         }
    //         // $this->getBoardTopicDatePosted($data_arr);
    //         $this->output->set_content_type('application/json')->set_output(json_encode($data_arr));
    // }
    //  public function getBoardTopicDatePosted($data_arr) {
    //         // $topic_data = $this->db->SELECT('topic_id')->WHERE('date_posted', '0000-00-00 00:00:00')->GET('altt_topics_tbl')->result_array();
    //         $result = array();
    //         foreach($data_arr as $topic){
    //             $forum_url = "https://www.altcoinstalks.com/index.php?topic=".$topic['topic_id'];
    //             // $forum_url = "https://www.altcoinstalks.com/index.php?topic=".$topic_id;
    //             $ch = curl_init($forum_url);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             // curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
    //             $html = curl_exec($ch);
    //             if (curl_errno($ch)) {
    //                 $message = 'Curl error during request: ' . curl_error($ch);
    //                 $this->Scrapper_model->insertSystemActivityLog($message);
    //                 exit;
    //             }
    //             curl_close($ch);
    //             $dom = new DOMDocument();
    //             @$dom->loadHTML($html);
    //             $xpath = new DOMXPath($dom);
                
    //             $status = true;

    //             # GET DATE POSTED
    //             $date_posted = date('Y-m-d H:i:s');
    //             date_default_timezone_set('Europe/Rome');
    //             $keyinfoDiv = $xpath->query('//div[@class="keyinfo"][1]')->item(0);
    //             $smalltextDiv = $xpath->query('.//div[@class="smalltext"]', $keyinfoDiv)->item(0);
    //             if ($smalltextDiv) {
    //                 $datetime = trim(str_replace(array('at',' »'), '', $smalltextDiv->nodeValue));
    //             }
                
    //             if(stripos($datetime, "Today") !== false ){
    //                 $datetime = explode("on: ", $datetime);
    //                 $date_today = str_replace("Today at", date('Y-m-d'), $datetime[1]);
    //                 $date_posted = date('Y-m-d H:i:s', strtotime($date_today));
    //             } 
    //             else{
    //                 $datetime = explode("on: ", $datetime);
    //                 $date_posted = date('Y-m-d H:i:s', strtotime($datetime[1]));
    //             }

               
    //             $data_arr = array(
    //                 'board_id'=>$topic['board_id'],
    //                 'topic_name'=>$topic['topic_name'],
    //                 'topic_id'=>$topic['topic_id'],
    //                 'username'=>$topic['username'],
    //                 'date_posted'=>$date_posted,
    //             );
    //             array_push($result, $data_arr);


    //             date_default_timezone_set('Europe/Rome');
    //             $current_time = time();
    //             $scrape_time = strtotime($date_posted);
    //             $time_difference = $current_time - $scrape_time;
    //             $data_res = "";
    //             if ($time_difference <= 300) { // if the time difference is equal or below 5 mins
    //                 #notify new topic in a board
    //                 $row_arr = array(
    //                     'status'=>true,
    //                     'board_id'=>$topic['board_id'],
    //                     'topic_name'=>$topic['topic_name'],
    //                     'topic_id'=>$topic['topic_id'],
    //                     'username'=>$topic['username'],
    //                     'date_posted'=>$date_posted,
    //                 );
    //                 array_push($result, $row_arr);
    //             }
    //             // $this->db->WHERE('topic_id', $topic['topic_id'])->UPDATE('altt_topics_tbl', $data_arr);
    //         }
    //         $this->output->set_content_type('application/json')->set_output(json_encode($result));

    // }
    public function scrapeTopicData($topic_id) {
        $check_topic = $this->db->WHERE('topic_id', $topic_id)->GET('altt_topics_tbl')->num_rows();
        if($check_topic <= 0){
            $forum_url = "https://www.altcoinstalks.com/index.php?topic=".$topic_id;
            $ch = curl_init($forum_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".$login_page_data['session_id'].";");
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
            
            $status = true;
            # GET USERNAME
            $poster_div = $xpath->query('//div[@class="poster"][1]')->item(0);
            if ($poster_div) {
                $h4 = $xpath->query('.//h4', $poster_div)->item(0);
                if ($h4) {
                    $anchors = $xpath->query('.//a', $h4);
                    if ($anchors->length >= 1) {
                        $username = $anchors->item(0)->nodeValue;
                    }
                }
            } 
            # GET DATE POSTED
            $date_posted = date('Y-m-d H:i:s');
            date_default_timezone_set('Europe/Rome');
            $keyinfo_div = $xpath->query('//div[@class="keyinfo"][1]')->item(0);
            $smalltext_div = $xpath->query('.//div[@class="smalltext"]', $keyinfo_div)->item(0);
            if ($smalltext_div) {
                $datetime = trim(str_replace(array('at',' »'), '', $smalltext_div->nodeValue));
            }
            
            if(stripos($datetime, "Today") !== false ){
                $datetime = explode("on: ", $datetime);
                $date_today = str_replace("Today at", date('Y-m-d'), $datetime[1]);
                $date_posted = date('Y-m-d H:i:s', strtotime($date_today));
            } 
            else{
                $datetime = explode("on: ", $datetime);
                $date_posted = date('Y-m-d H:i:s', strtotime($datetime[1]));
            }

            # HTML POST CONTENT
            $post_div = $xpath->query('//div[@class="post"]//div[contains(@id, "msg_")]')->item(0);
            $post = $dom->saveHTML($post_div);
           
            $data_arr = array(
                'username'=>$username,
                'post'=>$post,
                'date_posted'=>$date_posted,
            );
            return $data_arr;
        }
            
        // $this->output->set_content_type('application/json')->set_output(json_encode($data_arr));
    }
}
