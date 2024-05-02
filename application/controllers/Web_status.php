<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Web_status extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->model('Csrf_model');
        $this->load->model('Tools_model');
        $this->load->library('Api_auth');
        $this->load->library('user_agent');
    }

    public function checkWebsiteStatus(){
        // $url = "https://mixtum.io/";
        $url = $this->input->get('site');
        $type = $this->input->get('type');
        $remove_domain = $this->input->get('remove_domain');
        $website = str_replace(array('https://','http://'), '', $url);
        $http_status = 0;
        $response_time = 0;
        $response = "Error occured!";
        
        if (!empty($url)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in seconds
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36');
            $start_time = microtime(true);
            // Execute the request
            curl_exec($ch);
            $response_time = (microtime(true) - $start_time) * 1000; 

            // Check if any error occurred
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status >= 200 && $http_status < 300 && $remove_domain == 'yes') {
                $response = "Website is up";
                $color = "008000";
            }
            else if ($http_status >= 200 && $http_status < 300 ) {
                $response = ucwords($website). " is up";
                $color = "008000";
            }             
            else if ($http_status > 300 && $http_status <= 511 && $remove_domain == 'yes'){
                $color = "FF0000";
                $response = "Website is Down";
            }
            else if ($http_status > 300 && $http_status <= 511){
                $color = "FF0000";
                $response = ucwords($website). " is down";
            }
        }
        else{
            $response = "Error occured!";
            $color = "Error occured!";
        }

        if(isset($url) && isset($type) && $type == 'img'){
            $this->textToImage($response, $color);
        }
        else if(isset($url) && !isset($type)){
            $api_data = array(
                'status_code' => $http_status,
                'message' => $response,
                'website_url' => $url,
                'response_time' => round($response_time, 0).  " ms",
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($api_data));
        }
        else if(isset($url) && $type == 'json'){
            $api_data = array(
                'status_code' => $http_status,
                'message' => $response,
                'website_url' => $url,
                'response_time' => round($response_time, 0).  " ms",
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($api_data));
        }
    }

    public function textToImage($final_value, $color){
        $width = strlen($final_value) * 9.5;
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15.5);
        if($color && $color !== ''){
            $hex = '#'.$color;
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }
        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < $width ; $i++) {  
            for ($j=0; $j < $width ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);

        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function monitorWebsiteStatus(){
        $ip_address = $this->input->ip_address();
        $ip_whitelisted = array(
            '195.211.124.130',
        );
        if (in_array($ip_address, $ip_whitelisted)) {
            $allowed = true;
        } 
        else {
            $allowed = false;
        }

        if($allowed == false){
            $websites = $this->Tools_model->getWebsiteList();
            foreach($websites as $website){
                if (!empty($website['website_url'])) {
                    $ch = curl_init($website['website_url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in seconds
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36');
                    $start_time = microtime(true);
                    // Execute the request
                    curl_exec($ch);
                    $response_time = (microtime(true) - $start_time) * 1000; 
        
                    // Check if any error occurred
                    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
                    if ($http_status >= 200 && $http_status < 300 ) {
                        $timezone = $this->Tools_model->getTimeZone($website['name']);
                        $timestamp = strtotime(date('Y-m-d H:i:s'));
                        date_default_timezone_set($timezone);
                        $created_at = date('Y-m-d H:i:s', $timestamp);
    
                        $data_arr = array(
                            'website_id'=> $website['id'],
                            'response_time'=> round($response_time, 2). ' ms',
                            'status'=> 'up',
                            'status_code'=> $http_status,
                            'created_at'=> $created_at,
                        );
                        $this->Tools_model->insertWebsiteActivity($data_arr);
                    }
                    else {
                        $timezone = $this->Tools_model->getTimeZone($website['name']);
                        $timestamp = strtotime(date('Y-m-d H:i:s'));
                        date_default_timezone_set($timezone);
                        $created_at = date('Y-m-d H:i:s', $timestamp);
    
                        $data_arr = array(
                            'website_id'=> $website['id'],
                            'response_time'=> round($response_time, 2). ' ms',
                            'status'=> 'down',
                            'status_code'=> $http_status,
                            'created_at'=> $created_at,
                        );
                        $this->Tools_model->insertWebsiteActivity($data_arr);

                        // DOWNTIME ALERT
                        if($website['name'] == 'altcoinstalks' && $http_status > 500){
                            
                        }

                    }
                    $response = "Monitor: Successs";
                }
            }
        }
        else{
            $response = "Action not allowed";
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$response)));

    }
    public function getMonitorWebsiteData(){
        $site = $this->input->get('site');
        $timezone = $this->Tools_model->getTimeZone($site);
        $response = $this->Tools_model->getMonitorWebsiteDataChart($timezone);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
    public function getMonitorWebsiteActivity(){
        $site = $this->input->get('site');
        $timezone = $this->Tools_model->getTimeZone($site);
        $response = $this->Tools_model->getMonitorWebsiteActivity($timezone);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
    public function getResponseTimeActivity(){
        $site = $this->input->get('site');
        $timezone = $this->Tools_model->getTimeZone($site);
        $response = $this->Tools_model->getResponseTimeActivity($timezone);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
    public function test(){
        $offset = $this->input->get('offset');
        $json_data = file_get_contents('https://raw.githubusercontent.com/pxzone/utc_timezone_offset/main/data.json');
        $data = json_decode($json_data, true);
        $response = $data[$offset];
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}
    