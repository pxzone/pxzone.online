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
        $websites = $this->Tools_model->getWebsiteList();

        foreach($websites as $website){
            $http_status = 503;
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
                    $data_arr = array(
                        'website_id'=> $website['id'],
                        'response_time'=> round($response_time, 2). ' ms',
                        'status'=> 'up',
                        'status_code'=> $http_status,
                        'created_at'=> date('Y-m-d H:i:s'),
                    );
                    $this->Tools_model->insertWebsiteActivity($data_arr);
                }
                else {
                    $data_arr = array(
                        'website_id'=> $website['id'],
                        'response_time'=> round($response_time, 2). ' ms',
                        'status'=> 'down',
                        'status_code'=> $http_status,
                        'created_at'=> date('Y-m-d H:i:s'),
                    );
                    $this->Tools_model->insertWebsiteActivity($data_arr);
                }
            }
            else{
            }
        }
    }
    public function getMonitorWebsiteData(){
        $response = $this->Tools_model->getMonitorWebsiteDataChart();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
    public function getMonitorWebsiteActivity(){
        $response = $this->Tools_model->getMonitorWebsiteActivity();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
    public function getResponseTimeActivity(){
        $response = $this->Tools_model->getResponseTimeActivity();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$response)));
    }
}
    