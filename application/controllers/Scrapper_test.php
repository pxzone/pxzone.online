<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require FCPATH.'vendor/autoload.php';

date_default_timezone_set('Asia/Manila');

class Scrapper_test extends CI_Controller {
    function __construct (){
        parent::__construct();
        $this->load->library('telegram_api');
        $this->load->model('Scrapper_test_model');
        $this->load->model('Telegram_bot_test_model');
    }

    # FOR TESTING
    public function scrapeAlttForumTest($msg_id) {
        $login_page_data = $this->scrapeLoginPage();
        $login_forum = $this->loginForumTest($login_page_data);
        if($login_forum){
            $this->scrapePostsTest($login_page_data, $msg_id); // scrape
            // echo $login_forum;
        }
        else{
            echo 'error';
        }
	}
    public function scrapeAlttForumOption2($msg_id) {
        $login_page_data = $this->scrapeLoginPage();
        $login_forum = $this->loginForum($login_page_data);
        if($login_forum){
            $this->scrapePostsOption2($login_page_data, $msg_id);
        }
        else{
            echo 'error';
        }
	}
    
    public function scrapePostsOption2($login_page_data, $msg_id){
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
        
        # USERNAME
        $aWith_id = $xpath->query('//a[@id="msg'.$msg_id.'"]')->item(0);
        if ($aWith_id) {
            $posterAnchor = $xpath->query('following::div[@class="poster"][1]//a', $aWith_id)->item(0);
            if ($posterAnchor) {
                $user = $posterAnchor->textContent;
            }
        } 

        # POST SUBJECT
        $subjectContentElements = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a');
        foreach ($subjectContentElements as $subjectContentElements) {
            $subjectContent = $subjectContentElements->textContent;
        }

        # POST URL
        $aTag = $xpath->query('//h5[@id="subject_'.$msg_id.'"]//a')->item(0);
        if ($aTag) {
            $subject_url = $aTag->getAttribute('href');
        }

        # GET POST WHOLE CONTENT INCLUDING DIVS/BLOCKQUOTE
        $postContentElements = $xpath->query('//div[@id="msg_'.$msg_id.'"][position() = 1]')->item(0);
        if($postContentElements){
            $postContent = $postContentElements->textContent;
        }

        # GET POST CONTENT WITHOUT OTHER DIVS/BLOCKQUOTE
        // $divs = $xpath->query('//*[@id="msg_'.$msg_id.'"]');
        // if ($divs->length > 0) {
        //     $targetDiv = $divs->item(0);
        //     $clonedDiv = $targetDiv->cloneNode(true);
        //     $this->removeNestedElements($clonedDiv, 'div');
        //     $this->removeNestedElements($clonedDiv, 'blockquote');
        //     $targetDiv->nodeValue = '';
        //     foreach ($clonedDiv->childNodes as $childNode) {
        //         $targetDiv->appendChild($childNode->cloneNode(true));
        //     }
        //     $textContent = $clonedDiv->textContent;
        //     $textContent . PHP_EOL;
        // }

        $targetDiv = $xpath->query('//div[@id="msg_'.$msg_id.'"]')->item(0);
        if ($targetDiv) {
            foreach ($targetDiv->getElementsByTagName('div') as $div) {
                $div->parentNode->removeChild($div);
            }
        }
        // Remove blockquote tags
        foreach ($xpath->query('//blockquote') as $blockquote) {
            $blockquote->parentNode->removeChild($blockquote);
        }
        $textContent = $dom->textContent;

        if($user){
            $data_res = array(
                'response'=>true,
                'msg_id'=>$msg_id,
                'username'=>$user,
                'subject_url'=>$subject_url,
                'subject'=>$subjectContent,
                'post'=>$postContent,
                'tg_post'=>$textContent,

            );
            $this->Telegram_bot_test_model->mentionUser($data_res);
            
        }
        else{
            $fatal_error_page = $xpath->query('//div[@id="fatal_error"]')->item(0);
            $expression = "//*[@id='fatal_error']//div[contains(concat(' ', normalize-space(@class), ' '), ' padding ')]";
            $fatal_error_message = $xpath->query($expression);

            if ($fatal_error_page) {
                // $this->Scrapper_model->updateMsgIdAttempt($msg_id, $attempt);
                
                foreach ($fatal_error_message as $fatal_error_message) {
                    $fatal_error_message = $fatal_error_message->textContent . PHP_EOL;
                }

                if(stripos($fatal_error_message, "topic doesn't exist on this board" ) !== false){
                    $this->Scrapper_test_model->insertNewMsgID($msg_id);
                    $message = "Scrapper Status: Error. Topic doesn't exist..";
                    $this->Scrapper_test_model->insertSystemActivityLog($message);
                }
                else if(stripos($fatal_error_message, "missing or off limits to you" ) !== false){
                    $message = "Scrapper Status: Error. Page doesn't exist yet. Last msg_id ".$msg_id;
                    $this->Scrapper_test_model->insertSystemActivityLog($message);
                    $fatal_error_page = $fatal_error_page->textContent;

                }
                
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                    'error_message'=>$fatal_error_message,
              );
            }
            
            else if($attempt >= 3){
                $this->Scrapper_test_model->insertNewMsgID($msg_id);
                $message = "Scrapper Status: Error. Insert new msg_id due to frequent error";
                $this->Scrapper_test_model->insertSystemActivityLog($message);
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                );
            }
            else{
                $data_res = array(
                    'status'=>false,
                    'msg_id'=>$msg_id,
                );
                $this->Scrapper_test_model->updateMsgIdAttempt($msg_id, $attempt);
                $message = "Scrapper Status: Error. Can't scrap data. Last msg_id ".$msg_id;
                $this->Scrapper_test_model->insertSystemActivityLog($message);
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }
    public function scrapePostsTest($login_page_data, $msg_id)
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

        $data_res = array(
            'status'=>false,
            'msg_id'=>$msg_id,
        );
        if($user){
            $data = array(
                'response'=>true,
                'msg_id'=>$msg_id,
                'poster_username'=>$user, // post author
                'subject_url'=>$subject_url,
                'subject'=>$subject_content,
                'post'=>$post_content,
                'tg_post'=>$text_content,
                'edited_post'=>$edited_post_status,
            );
            $this->Telegram_bot_test_model->notifyUser($data);
            // $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
            // $this->Scrapper_model->insertNewMsgID($msg_id);
            $message = "Scrapper Status: Okay [msg_id $msg_id][test]";
            $this->Scrapper_model->insertSystemActivityLog($message);
            $data_res = array(
                'status'=>true,
                'msg_id'=>$msg_id,
            );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data_res));
    }
    public function removeNestedElements(DOMNode $node, $tagName)
    {
        $nestedElements = $node->getElementsByTagName($tagName);
        foreach ($nestedElements as $nestedElement) {
            $this->removeNestedElements($nestedElement, $tagName);
            $nestedElement->parentNode->removeChild($nestedElement);
        }
    }
    public function loginForumTest($login_page_data){
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
            return $response;
            // return true;
        } else {
            return false;
        }
    }
    public function mentionInTelegramTest(){
        $response = $this->Telegram_bot_test_model->mentionInTelegramTest();
        $this->output->set_content_type('application/json')->set_output(json_encode($response));

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
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $finalUrl;
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
    # ACCESS USING CRON JOB EVERY 5 MINUTE
    public function scrapeAlttForumForEditedPosts() {
        $scraped_post = $this->Telegram_bot_test_model->checkScrapedPost();

        if(!empty($scraped_post)){
            foreach($scraped_post as $sp){
                $login_page_data = $this->scrapeLoginPage();
                $login_forum = $this->loginForumTest($login_page_data);
                if($login_forum){
                    $this->scrapeEditedPosts($login_page_data, $sp['msg_id'], $sp['post_content']);
                }
                else{
                    echo 'error';
                }
                sleep(3); // sleep 5 seconds then scrape again
                // $this->Telegram_bot_test_model->deleteScrapedPost($sp['msg_id']);
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
        $edited_post_status = false;
        $edited_post_div = $xpath->query('//*[@id="modified_$msg_id"]')->item(0);
        if ($edited_post_div) {
            // Check if there is an <em> tag inside the specific div
            $emTags = $xpath->query('.//em', $edited_post_div);
    
            if ($emTags->length > 0) {
                $edited_post_status = true;
            }
        }

        # IF POST IS EDITED
        if($user && $edited_post_status > 0){
            $initial_scraped_post = str_word_count($initial_scraped_post, 1);
            $edited_post = str_word_count($post_content, 1); // from new scraped data on the msg_id

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
    
                $this->Telegram_bot_test_model->notifyUser($data);
                // $this->Telegram_bot_model->saveScrapedData($data); # SAVED SCRAPED DATA
                // $this->Scrapper_model->insertNewMsgID($msg_id);
                $message = "Scrapper Status: Okay. Scraped again [msg_id $msg_id][test]";
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
    
}
