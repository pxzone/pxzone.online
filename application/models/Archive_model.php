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
        
        $posts = $this->db->SELECT('asdt.board_id, asdt.topic_id, asdt.msg_id, asdt.username, abt.board_name, asdt.subject as title, asdt.html_post as post, asdt.date_posted')
            // ->WHERE("(asdt.board_id LIKE '%".$search."%' OR asdt.topic_id LIKE '%".$search."%' OR asdt.msg_id LIKE '%".$search."%' OR asdt.username LIKE '%".$search."%' OR asdt.post_content LIKE '%".$search."%' OR abt.board_name LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($where_category)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('date_posted',' desc')
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
    public function getKarmaLogData($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        if(empty($search)){
        }
        $date_range = array('aklt.created_at >'=>'2024-02-19 00:00:00', 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        $logs = $this->db->SELECT('uid, aklt.username, karma_point as karma, total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('aklt.created_at',' desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

        $query_count = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
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
                'created_at'=>date('M d, Y H:i:s T', strtotime($log['created_at'])),
            );
            array_push($result, $row_array);
        }
        $data['karma_log'] = $result;
        $data['count'] = $query_count;
        return $data;
    }
    public function getKarmaLogDataSort($row_per_page, $row_no){
        $sort = $this->input->get('select_sort');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        
        $result = array();

        if($sort == 'custom' && !empty($from) && !empty($to)){
            
            $data_res = $this->customDateHighestKarma($row_per_page, $row_no, $from, $to);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                );
                array_push($result, $row_array);
            }
        }
        
        else if($sort == 'highest_karma_today'){
            $data_res = $this->highestKarmaToday($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                );
                array_push($result, $row_array);
            }
        }
        else if($sort == 'karma_120_days' || $sort == 'karma_90_days' || $sort == 'karma_60_days' || $sort == 'karma_30_days'){
            $data_res = $this->getXDaysKarma($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                );
                array_push($result, $row_array);
            }
        }
        else if($sort == 'highest_karma_all_time'){
            $data_res = $this->AllTimeHighKarma($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'total_karma'=>$log['total_karma'],
                );
                array_push($result, $row_array);
            }
        }
        else if($sort == 'highest_karma_this_month'){
            $data_res = $this->highestKarmaThisMonth($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                );
                array_push($result, $row_array);
            }
        }
        else if($sort == 'default'){
            $data_res = $this->defaultSortKarma($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                    'created_at'=> date('Y-m-d H:i:s T', strtotime($log['created_at'])),
                );
                array_push($result, $row_array);
            }
        }
        else{
            $data_res = $this->defaultSortKarma($row_per_page, $row_no);
            date_default_timezone_set("Europe/Rome");
            foreach($data_res['logs'] as $log){
                $row_array = array(
                    'uid'=>$log['uid'],
                    'username'=>$log['username'],
                    'position'=>$log['position'],
                    'karma'=>$log['karma'],
                    'total_karma'=>$log['total_karma'],
                    'created_at'=> date('Y-m-d H:i:s T', strtotime($log['created_at'])),
                );
                array_push($result, $row_array);
            }
        }
        
        
        $data['karma_log'] = $result;
        $data['count'] = $data_res['query_count'];
        return $data;
    }
    function defaultSortKarma($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        $date_range = array('aklt.created_at >'=>'2024-02-19 00:00:00', 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.position, karma_point as karma, total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('aklt.created_at',' desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

        $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;
    }
    function getXDaysKarma($row_per_page, $row_no){
        $sort = $this->input->get('select_sort');
        $search = $this->input->get('keyword');

        if($sort == "karma_120_days") {
            $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours -120 day')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        }
        else if($sort == "karma_90_days") {
            $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours -90 day')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        }
        else if($sort == "karma_60_days") {
            $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours -60 day')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        }
        else if($sort == "karma_30_days") {
            $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours -30 day')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        }        
        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.position, SUM(karma_point) as karma, aut.karma as total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('karma', 'desc')
            ->ORDER_BY('total_karma', 'desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

         $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;

    }
    public function customDateHighestKarma($row_per_page, $row_no, $from, $to){
        
        $start_date = date('Y-m-d 00:00:00', strtotime($from, strtotime('-7 hours')));
        $end_date = date('Y-m-d 23:59:59', strtotime($to));
        $date_range = array('aklt.created_at >'=>$start_date, 'aklt.created_at <'=> $end_date);

        $search = $this->input->get('keyword');
        // $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));

        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.position, SUM(karma_point) as karma, aut.karma as total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('karma', 'desc')
            ->ORDER_BY('total_karma', 'desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

         $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
             ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;
        
    }
    public function highestKarmaToday($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.position, SUM(karma_point) as karma, aut.karma as total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('karma', 'desc')
            ->ORDER_BY('total_karma', 'desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

         $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
             ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;
    }
    function AllTimeHighKarma($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        $date_range = array('aklt.created_at >'=>date('Y-m-d 00:00:00', strtotime('-7 hours')), 'aklt.created_at <'=> date('Y-m-d H:i:s'));
        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.activity, aut.position, aut.karma as total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->GROUP_BY('aklt.username')
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('total_karma', 'desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

         $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
             ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;
    }
    function highestKarmaThisMonth($row_per_page, $row_no){
        $search = $this->input->get('keyword');
        $date_range = array('aklt.created_at >'=>date('Y-m-01 00:00:00', strtotime('-7 hours')), 'aklt.created_at <'=> date('Y-m-t H:i:s'));
        $data['logs'] = $this->db->SELECT('uid, aklt.username, aut.position, SUM(karma_point) as karma, aut.karma as total_karma, aklt.created_at')
            ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
            ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->LIMIT($row_per_page, $row_no)
            ->ORDER_BY('karma', 'desc')
            ->ORDER_BY('total_karma', 'desc')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->result_array();

         $data['query_count'] = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
             ->WHERE($date_range)
            ->GROUP_BY('aklt.username')
            ->FROM('altt_karma_log_tbl as aklt')
            ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
            ->GET()->num_rows();
        return $data;
    }
}