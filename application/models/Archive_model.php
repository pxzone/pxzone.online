<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model {
    public function getTopicContent($row_per_page, $row_no, $topic_id){
        return $this->db->SELECT('asdt.board_id, asdt.topic_id, asdt.msg_id, asdt.username, abt.board_name, asdt.subject as title, asdt.html_post as post, asdt.date_posted')
        ->WHERE($where_category)
        ->LIMIT($row_per_page, $row_no)
        ->ORDER_BY('date_posted',' desc')
        ->FROM('altt_scraped_archive_data_tbl as asdt')
        ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
        ->GET()->result_array();
    }
    public function getPostContent($msg_id){
        return $this->db->SELECT('asdt.board_id, topic_id, msg_id, username, abt.board_name, subject as title, html_post as post, date_posted')
            ->WHERE('msg_id', $msg_id)
            ->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->GET()->row_array();
    }
    public function getUserPostsContent($username){
        $query = $this->db->SELECT('asdt.board_id, topic_id, msg_id, username, abt.board_name, subject as title, html_post as post, date_posted')
            ->WHERE('username', $username)
            // ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('date_posted',' desc')
            ->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->GET();

        $data['posts'] = $query->result_array();
        $data['count'] = $query->num_rows();
        return $data;
    }
    public function searchPostsContent($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        $category = $this->input->get('category');
        $sort = $this->input->get('sort');
        if(empty($search)){
            return false;
            exit();
        }
        if($category == 'topic'){
            $where_category = array('asdt.topic_id'=>$search);
        }
        else if($category == 'post'){
            $where_category = array('asdt.msg_id'=>$search);
        }
        else if($category == 'username'){
            $where_category = array('asdt.username'=>$search);
        }
        else{
            $where_category = array('asdt.topic_id'=>$search);
        }
        
        $posts = $this->db->SELECT('asdt.board_id, asdt.topic_id, asdt.msg_id, asdt.username, abt.board_name, asdt.subject as title, asdt.html_post as post, asdt.date_posted')
            // ->WHERE("(asdt.board_id LIKE '%".$search."%' OR asdt.topic_id LIKE '%".$search."%' OR asdt.msg_id LIKE '%".$search."%' OR asdt.username LIKE '%".$search."%' OR asdt.post_content LIKE '%".$search."%' OR abt.board_name LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($where_category)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('date_posted', $sort)
            ->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->GET()->result_array();

        $query_count = $this->db->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            // ->WHERE("(asdt.board_id LIKE '%".$search."%' OR asdt.topic_id LIKE '%".$search."%' OR asdt.msg_id LIKE '%".$search."%' OR asdt.username LIKE '%".$search."%' OR asdt.post_content LIKE '%".$search."%' OR abt.board_name LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($where_category)
            ->GET()->num_rows();

        $result = array();
        foreach($posts as $post){
            $row_array = array(
                'board_id'=>$post['board_id'],
                'topic_id'=>$post['topic_id'],
                'msg_id'=>$post['msg_id'],
                'username'=>$post['username'],
                'board_name'=>$post['board_name'],
                'title'=>$post['title'],
                'post'=>$post['post'],
                'date_posted'=>date('M d, Y H:i:s', strtotime($post['date_posted'])).' CET',
            );
            array_push($result, $row_array);
        }
        $data['posts'] = $result;
        $data['count'] = $query_count;
        return $data;
    }
    
}