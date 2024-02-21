<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model {
    public function getTopicContent($topic_id){
        return $this->db->SELECT('adt.msg_id, att.board_id, att.topic_id, att.username, abt.board_name, att.topic_name as title, att.post, att.date_posted')
            ->FROM('altt_topics_tbl as att')
            ->JOIN('altt_scraped_archive_data_tbl as adt','adt.topic_id = att.topic_id','left')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=att.board_id', 'left')
            ->WHERE('att.topic_id', $topic_id)
            ->GET()->row_array();
    }
    public function getPostContent($msg_id){
        return $this->db->SELECT('asdt.board_id, topic_id, msg_id, username, abt.board_name, subject as title, html_post as post, date_posted')
            ->WHERE('msg_id', $msg_id)
            ->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->GET()->row_array();
    }
    public function getUserPostsContent($username, $row_per_page, $row_no){
        $query = $this->db->SELECT('asdt.board_id, topic_id, msg_id, username, abt.board_name, subject as title, html_post as post, date_posted')
        ->WHERE('username', $username)
        ->LIMIT($row_per_page, $row_no)
        ->ORDER_BY('date_posted',' desc')
        ->FROM('altt_scraped_archive_data_tbl as asdt')
        ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left');

        $data['posts'] = $query->result_array();
        $data['count'] = $query->num_rows();
        return $data;
    }
    public function searchPostsContent($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        if(empty($search)){
            return false;
            exit();
        }
        $posts = $this->db->SELECT('asdt.board_id, asdt.topic_id, asdt.msg_id, asdt.username, abt.board_name, asdt.subject as title, asdt.html_post as post, asdt.date_posted')
            ->WHERE("(asdt.board_id LIKE '%".$search."%' OR asdt.topic_id LIKE '%".$search."%' OR asdt.msg_id LIKE '%".$search."%' OR asdt.username LIKE '%".$search."%' OR asdt.post_content LIKE '%".$search."%' OR abt.board_name LIKE '%".$search."%')", NULL, FALSE)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('date_posted',' desc')
            ->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->GET()->result_array();

        $query_count = $this->db->FROM('altt_scraped_archive_data_tbl as asdt')
            ->JOIN('altt_boards_tbl as abt', 'abt.board_id=asdt.board_id', 'left')
            ->WHERE("(asdt.board_id LIKE '%".$search."%' OR asdt.topic_id LIKE '%".$search."%' OR asdt.msg_id LIKE '%".$search."%' OR asdt.username LIKE '%".$search."%' OR asdt.post_content LIKE '%".$search."%' OR abt.board_name LIKE '%".$search."%')", NULL, FALSE)
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
                'date_posted'=>date('F d, Y H:i:s', strtotime($post['date_posted'])),
            );
            array_push($result, $row_array);
        }
        $data['posts'] = $result;
        $data['count'] = $query_count;
        return $data;
    }
    public function getKarmaLogData($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        if(empty($search)){
        }
        $logs = $this->db->SELECT('uid, aklt.username, karma_point as karma, total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('aklt.created_at',' desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

        $query_count = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        
        
        date_default_timezone_set("Europe/Rome");
        $result = array();
        foreach($logs as $log){
            $created_at = strtotime($log['created_at']) - (7*3600);
            $row_array = array(
                'uid'=>$log['uid'],
                'username'=>$log['username'],
                'karma'=>$log['karma'],
                'total_karma'=>$log['total_karma'],
                'created_at'=>date('F d, Y H:i:s', $created_at),
            );
            array_push($result, $row_array);
        }
        $data['karma_log'] = $result;
        $data['count'] = $query_count;
        return $data;
    }
}