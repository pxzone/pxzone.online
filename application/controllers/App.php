<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class App extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->model('Site_settings_model');
        $this->load->model('Csrf_model');
    }
    public function index(){
        // $this->output->cache(43200); // cache for one month
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'index';
        $data['description'] = '';
        $data['canonical_url'] = base_url();
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $data['state'] = "index";
        $data['url_param'] = "";
    	$this->load->view('home/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('home/index');
    	$this->load->view('home/footer');
        // $this->output->delete_cache('/'); // delete cache url
    }
    public function getCsrfData() { 
        $data = $this->Csrf_model->getCsrfData();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
   
}
