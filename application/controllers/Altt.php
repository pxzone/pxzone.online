<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Altt extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->library('user_agent');
        $this->load->library('pagination');
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
    public function getLatestTopic(){
        $data = $this->Altt_model->getLatestTopic();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT ));
    }
    public function getKarmaLogSort(){
        $row_no = $this->input->get('page_no');
        // Row per page
        $row_per_page = $this->input->get('num_sort');

        // Row position
        if($row_no != 0){
            $row_no = ($row_no-1) * (int)$row_per_page;
        }

        $data_res = $this->Altt_model->getKarmaLogDataSort($row_per_page, $row_no);
        $result = $data_res['karma_log'];
        $all_count = $data_res['count'];

        // Pagination Configuration
        $config['base_url'] = base_url('api/altt/karma/_get?');
        $config['use_page_numbers'] = TRUE;
        $config['total_rows'] = $all_count;
        $config['per_page'] = $row_per_page;

        // Pagination with bootstrap
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page_no';
        $config['full_tag_open'] = '<ul class="pagination btn-xs">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item ">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tagl_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tagl_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item disabled">';
        $config['first_tagl_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tagl_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');
        $config['next_link'] = 'Next'; // change > to 'Next' link
        $config['prev_link'] = 'Previous'; // change < to 'Previous' link

        // Initialize
        $this->pagination->initialize($config);

        // Initialize $data Array
        $data['pagination'] = $this->pagination->create_links();
        $data['result'] = $result;
        $data['row'] = $row_no;
        $data['count'] = $all_count;
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }

}