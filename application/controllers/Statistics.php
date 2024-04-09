<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Statistics extends CI_Controller {

	function __construct() {
		parent::__construct();
        $this->load->model('Statistics_model');
	}
    public function getSiteVisits(){
    	$data = $this->Statistics_model->getSiteVisits();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
}