<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Altt extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->model('Site_settings_model');
        $this->load->model('Altt_model');
    }
    public function getKarmaPostStat(){
        $data = $this->Altt_model->getKarmaPostStat();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    public function getPostsChartStat(){
        $data = $this->Altt_model->getPostsChartStat();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    public function getTopicsChartStat(){
        $data = $this->Altt_model->getTopicsChartStat();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    # Top 10 Topics (by Replies)
    public function getTopicsByReplies(){
        $data = $this->Altt_model->getTopicsByReplies();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }
    # Top Topic Starters
    public function getTopicStarters(){
        $data = $this->Altt_model->getTopicStarters();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }
    # Top 10 Boards
    public function getTopBoards(){
        $data = $this->Altt_model->getTopBoards();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }
    # Top 10 posters
    public function getTopPosters(){
        $data = $this->Altt_model->getTopPosters();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }
    public function getNullUsers(){
        $data = $this->Altt_model->getNullUsers();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }

}