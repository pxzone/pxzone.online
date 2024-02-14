<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model {
    public function getTopicContent($topic_id){
        return $this->db->SELECT('att.board_id, att.topic_id, att.username, adt.board_name, att.topic_name as title, att.post, att.date_posted')
        ->FROM('altt_topics_tbl as att')
        ->JOIN('altt_scraped_archive_data_tbl as adt','adt.topic_id = att.topic_id','left')
        ->WHERE('att.topic_id', $topic_id)
        ->GET()->row_array();
    }
    public function getPostContent($msg_id){
        return $this->db->SELECT('board_id, topic_id, msg_id, username, board_name, subject as title, html_post as post, date_posted')
            ->WHERE('msg_id', $msg_id)
            ->GET('altt_scraped_archive_data_tbl')
            ->row_array();
    }
    public function getUserPostsContent($username, $row_per_page, $row_no){
        $query = $this->db->SELECT('board_id, topic_id, msg_id, username, board_name, subject as title, html_post as post, date_posted')
        ->WHERE('username', $username)
        ->LIMIT($row_per_page, $row_no)
        ->ORDER_BY('date_posted',' desc')
        ->GET('altt_scraped_archive_data_tbl');
        
        $data['posts'] = $query->result_array();
        $data['count'] = $query->num_rows();
        return $data;
    }
}