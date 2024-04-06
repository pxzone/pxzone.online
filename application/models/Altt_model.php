<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Altt_model extends CI_Model {
    public function getKarmaPostStat(){
        $karma_count = $this->getKarmaCountStat('24h');
        $post_count = $this->getPostCountStat('24h');
        $archive_posts = $this->getPostCountStat('all');
        $archive_topics = $this->getTopicCountStat('all');
        $parsed_users = $this->getUsersCountStat('all');
        $data_res = array(
            'post_count'=>number_format($post_count),
            'karma_count'=>number_format($karma_count),
            'archive_posts_count'=>number_format($archive_posts),
            'archive_topics_count'=>number_format($archive_topics),
            'parsed_users'=>number_format($parsed_users),
        );

        return $data_res;
    }
    public function getKarmaCountStat($range){
        if($range == '24h'){
            $date_range = array('created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'created_at <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
            $query = $this->db->SELECT('SUM(karma_point) as karma_count')
                ->WHERE($date_range)
                ->GET('altt_karma_log_tbl')->row_array();
        }
        return $query['karma_count'];
    }
    public function getPostCountStat($range){
        if($range == '24h'){
            $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
            $query = $this->db->SELECT('COUNT(id) as post_count')
                ->WHERE($date_range)
                ->FROM('altt_scraped_archive_data_tbl')
                ->GET()->row_array();
        }
        else if($range == 'all'){
            $query = $this->db->SELECT('COUNT(id) as post_count')
                ->FROM('altt_scraped_archive_data_tbl')
                ->GET()->row_array();
        }
        return $query['post_count'];
    }
    public function getTopicCountStat($range){
        if($range == '24h'){
            $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
            $query = $this->db->SELECT('COUNT(id) as topic_count')
                ->WHERE($date_range)
                ->FROM('altt_topics_tbl')
                ->GET()->row_array();
        }
        else if($range == 'all'){
            $query = $this->db->SELECT('COUNT(id) as topic_count')
                ->FROM('altt_topics_tbl')
                ->GET()->row_array();
        }
        return $query['topic_count'];
    }
    public function getUsersCountStat($range){
        if($range == '24h'){
            $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
            $query = $this->db->SELECT('COUNT(id) as users_count')
                ->WHERE($date_range)
                ->FROM('altt_users_tbl')
                ->GET()->row_array();
        }
        else if($range == 'all'){
            $query = $this->db->SELECT('COUNT(id) as users_count')
                ->FROM('altt_users_tbl')
                ->GET()->row_array();
        }
        return $query['users_count'];
    }

    public function getPostsChartStat(){
        $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-27 days -7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
        $groupBy = 'DATE(date_posted)';
        $query = $this->db->SELECT('DATE(date_posted) as date, COUNT(msg_id) as posts')
            ->WHERE($date_range)
            ->GROUP_BY($groupBy)
            ->ORDER_BY('date_posted','asc')
            ->GET('altt_scraped_archive_data_tbl')->result_array();

        $result = array();
        foreach($query as $q){
            $array = array(
                'date'=>date('M d', strtotime($q['date'])),
                'posts'=>$q['posts']
            );
            array_push($result, $array);
        }
        return $result;
    }
    public function getTopicsChartStat(){
        $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-27 days -7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
        $groupBy = 'DATE(date_posted)';
        $query = $this->db->SELECT('DATE(date_posted) as date, COUNT(topic_id) as topics')
            ->WHERE($date_range)
            ->GROUP_BY($groupBy)
            ->ORDER_BY('date_posted','asc')
            ->GET('altt_topics_tbl')->result_array();

        $result = array();
        foreach($query as $q){
            $array = array(
                'date'=>date('M d', strtotime($q['date'])),
                'topics'=>$q['topics']
            );
            array_push($result, $array);
        }
        return $result;
    }
    public function getTopicsByReplies(){ 
        $query = $this->db->SELECT('att.topic_name, att.topic_id, COUNT(aadt.topic_id) as reply_count')
            ->GROUP_BY('aadt.topic_id')
            ->ORDER_BY('reply_count','desc')
            ->LIMIT(10)
            ->FROM('altt_topics_tbl as att')
            ->JOIN('altt_scraped_archive_data_tbl as aadt','aadt.topic_id=att.topic_id','left')
            ->GET()->result_array();
        $all_count = $this->db->GET('altt_topics_tbl')->num_rows();

        $result = array();
        foreach($query as $q){
            $reply_count = $q['reply_count'];
            $percent = ($reply_count / $all_count) * 100;
            $row_arr = array(
                'topic_id'=>$q['topic_id'],
                'topic_name'=>$q['topic_name'],
                'reply_count'=>$reply_count,
                'percent'=>round($percent),
            );
            array_push($result, $row_arr);
         }
        return $result;
    
    }
    public function getTopicStarters(){ 
        $query = $this->db->SELECT('uid, att.username, COUNT(topic_id) as topic_count')
            ->GROUP_BY('att.username')
            ->ORDER_BY('topic_count', 'desc')
            ->LIMIT(10)
            ->FROM('altt_topics_tbl as att')
            ->JOIN('altt_users_tbl as aut','aut.username=att.username','left')
            ->GET()->result_array();

        $all_count = $this->db->FROM('altt_topics_tbl as att')->GET()->num_rows();

        $result = array();
        foreach($query as $q){
            $topic_count = $q['topic_count'];
            $percent = ($topic_count / $all_count) * 100;
            $row_arr = array(
                'uid'=>$q['uid'],
                'username'=>$q['username'],
                'topic_count'=>$topic_count,
                'percent'=>round($percent),
            );
            array_push($result, $row_arr);
            }
        return $result;
    }
    public function getTopBoards(){ 
        $query = $this->db->SELECT('abt.board_id, abt.board_name, COUNT(act.msg_id) as post_count')
            ->GROUP_BY('att.board_id')
            ->ORDER_BY('post_count', 'desc')
            ->LIMIT(10)
            ->FROM('altt_boards_tbl as abt')
            ->JOIN('altt_topics_tbl as att','att.board_id=abt.board_id','left')
            ->JOIN('altt_scraped_archive_data_tbl as act','act.topic_id=att.topic_id','left')
            ->GET()->result_array();

        $all_count = $this->db->GET('altt_scraped_archive_data_tbl')->num_rows();

        $result = array();
        foreach($query as $q){
            $post_count = $q['post_count'];
            $percent = ($post_count / $all_count) * 100;
            $row_arr = array(
                'board_id'=>$q['board_id'],
                'board_name'=>$q['board_name'],
                'post_count'=>$post_count,
                'percent'=>round($percent),
            );
            array_push($result, $row_arr);
            }
        return $result;
    }
    public function getTopPosters(){ 
        $query = $this->db->SELECT('aut.uid, aat.username, COUNT(aat.msg_id) as post_count')
            ->GROUP_BY('aut.username')
            ->ORDER_BY('post_count', 'desc')
            ->LIMIT(10)
            ->FROM('altt_scraped_archive_data_tbl as aat')
            ->JOIN('altt_users_tbl as aut','aut.username=aat.username','left')
            ->GET()->result_array();

        $all_count = $this->db->GET('altt_scraped_archive_data_tbl')->num_rows();
        $result = array();

        foreach($query as $q){
            $post_count = $q['post_count'];
            $percent = ($post_count / $all_count) * 100;
            $row_arr = array(
                'uid'=>$q['uid'],
                'username'=>$q['username'],
                'post_count'=>$post_count,
                'percent'=>round($percent),
            );
            array_push($result, $row_arr);
        }
        return $result;

    }
    public function getNullUsers(){ 
        $query = $this->db->SELECT('aat.username, aat.msg_id')
            ->GROUP_BY('aat.username')
            ->WHERE('aut.username', null)
            ->FROM('altt_scraped_archive_data_tbl as aat')
            ->JOIN('altt_users_tbl as aut','aut.username=aat.username','left')
            ->GET()->result_array();
        return $query;
    }
}