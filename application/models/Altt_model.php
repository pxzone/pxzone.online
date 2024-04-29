<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Altt_model extends CI_Model {
    public function getKarmaPostStat(){
        $karma_count = $this->getKarmaCountStat('24h');
        $post_count_24h = $this->getPostCountStat('24h');
        $archive_topics_24h = $this->getTopicCountStat('24h');
        $archive_posts = $this->getPostCountStat('all');
        $archive_topics = $this->getTopicCountStat('all');
        $parsed_users = $this->getUsersCountStat('all');
        $data_res = array(
            'post_24h'=>number_format($post_count_24h),
            'topic_24h'=>number_format($archive_topics_24h),
            'karma_24h'=>number_format($karma_count),
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
        $date_range = array('date_posted >'=>date('Y-m-d 00:00:00', strtotime('-30 days -7 hours')), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
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
        $date_range = array('date_posted >'=>date('2024-02-07 00:00:00'), 'date_posted <'=> date('Y-m-d H:i:s', strtotime('-7 hours')));
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
    public function getLatestTopic(){ 
        $query = $this->db->SELECT('topic_id, topic_name')
            ->ORDER_BY('date_posted', 'desc')
            ->LIMIT(10)
            ->FROM('altt_topics_tbl')
            ->GET()->result_array();
        $result = array();

        foreach($query as $q){
            $row_arr = array(
                'topic_id'=>$q['topic_id'],
                'topic_name'=>$q['topic_name'],
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
    // public function getKarmaLogData($row_per_page, $row_no){
    //     $search = $this->input->get('keyword');
    //     if(empty($search)){
    //     }
    //     $date_range = array('aklt.created_at >'=>'2024-02-19 00:00:00', 'aklt.created_at <'=> date('Y-m-d H:i:s'));
    //     $logs = $this->db->SELECT('uid, aklt.username, karma_point as karma, total_karma, aklt.created_at')
    //         ->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
    //         ->WHERE($date_range)
    //         ->LIMIT($row_per_page, $row_no)
    //         ->ORDER_BY('aklt.created_at',' desc')
    //         ->FROM('altt_karma_log_tbl as aklt')
    //         ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
    //         ->GET()->result_array();

    //     $query_count = $this->db->WHERE("(aklt.username LIKE '%".$search."%')", NULL, FALSE)
    //         ->WHERE($date_range)
    //         ->FROM('altt_karma_log_tbl as aklt')
    //         ->JOIN('altt_users_tbl as aut', 'aut.username=aklt.username', 'left')
    //         ->GET()->num_rows();
        
        
    //     date_default_timezone_set("Europe/Rome");
    //     $result = array();
    //     foreach($logs as $log){
    //         $created_at = strtotime($log['created_at']) - (7*3600);
    //         $row_array = array(
    //             'uid'=>$log['uid'],
    //             'username'=>$log['username'],
    //             'karma'=>$log['karma'],
    //             'total_karma'=>$log['total_karma'],
    //             'created_at'=>date('M d, Y H:i:s T', strtotime($log['created_at'])),
    //         );
    //         array_push($result, $row_array);
    //     }
    //     $data['karma_log'] = $result;
    //     $data['count'] = $query_count;
    //     return $data;
    // }
    
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
    public function getKarmaLogDataSortExport($row_per_page, $row_no){
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
}