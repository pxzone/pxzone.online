<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Archive extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->library('user_agent');
        $this->load->library('pagination');
        $this->load->model('Archive_model');
        $this->load->model('Site_settings_model');
    }
    public function getTopicContent($topic_id){
        $data = $this->Archive_model->getTopicContent($topic_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    public function getPostContent($msg_id){
        $data = $this->Archive_model->getPostContent($msg_id);
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    public function getUserPostsContent($username){
        $row_no = $this->input->get('page_no');
        // Row per page
        $row_per_page = 15;

        // Row position
        if(empty($row_no)){
            $row_no = 1;
        }
        $row_no = ($row_no-1) * $row_per_page;

        $data_res = $this->Archive_model->getUserPostsContent($username, $row_per_page, $row_no);
        $result = $data_res['posts'];
        $all_count = $data_res['count'];

        // Pagination Configuration
        $config['base_url'] = base_url('api/user/'.$username);
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
    public function getPosts($msg_id){
        $this->output->cache(60);
        $row_data = $this->Archive_model->getPostContent($msg_id);
        
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Cryptocurrency Balance Checker';
        $data['description'] = 'A tool for checking the balance of a Bitcoin, Ethereum, Tron, Binance coin, Dash, Litecoin, Dogecoin wallet address. The tool returns the current balance in $coin and USD value of the wallet address';
        $data['canonical_url'] = base_url('altt');
        $data['state'] = "altt_archives";
        $data['msg_id'] = $msg_id;
        $data['post'] = $row_data;
    	$this->load->view('altt/header', $data);
    	$this->load->view('altt/nav');
    	$this->load->view('altt/archive');
    	$this->load->view('altt/footer');
    }
    public function getTopics($topic_id){
        $this->output->cache(60);
        $row_data = $this->Archive_model->getTopicContent($topic_id);

        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Cryptocurrency Balance Checker';
        $data['description'] = 'A tool for checking the balance of a Bitcoin, Ethereum, Tron, Binance coin, Dash, Litecoin, Dogecoin wallet address. The tool returns the current balance in $coin and USD value of the wallet address';
        $data['canonical_url'] = base_url('altt');
        $data['state'] = "altt_archives";
        $data['topic_id'] = $topic_id;
        $data['post'] = $row_data;
    	$this->load->view('altt/header', $data);
    	$this->load->view('altt/nav');
    	$this->load->view('altt/archive');
    	$this->load->view('altt/footer');
    }
    public function searchPosts(){
        $row_no = $this->input->get('page_no');
        // Row per page
        $row_per_page = 15;

        
        // Row position
        if($row_no != 0){
            $row_no = ($row_no-1) * $row_per_page;
        }

        $data_res = $this->Archive_model->searchPostsContent($row_per_page, $row_no);
        $result = $data_res['posts'];
        $all_count = $data_res['count'];

        // Pagination Configuration
        $config['base_url'] = base_url('api/altt/_search?');
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