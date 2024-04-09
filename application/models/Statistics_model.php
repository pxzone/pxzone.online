<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Statistics_model extends CI_Model {
    public function getSiteVisits () {
        $range = $this->input->get('range');
        if($range == 'today') {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
            $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);
        }
        else if($range == '7_days') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-7 day', strtotime(date('Y-m-d 00:00:00'))));
            $end_date = date('Y-m-d 23:59:59');
            $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);
        }

        else if($range == '15_days') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-15 day', strtotime(date('Y-m-d 00:00:00'))));
            $end_date = date('Y-m-d 23:59:59');
            $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);
        }

        else if($range == '1_month') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-30 day', strtotime(date('Y-m-d 00:00:00'))));
            $end_date = date('Y-m-d 23:59:59');
            $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);
        }
        else if($range == '1_year') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-365 day', strtotime(date('Y-m-d 00:00:00'))));
            $end_date = date('Y-m-d 23:59:59');
            $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);
        }
 
        $site_visit = $this->getWebsiteVisitStat($date_range);
        $visit_today = $this->getWebsiteVisitTodayStat();
        $data['visit_today'] = $visit_today;
        $data['site_visit'] = $site_visit;
        return $data;
    }
    public function getWebsiteVisitTodayStat(){
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');
        $date_range = array('created_at >'=>$start_date, 'created_at <'=> $end_date);

        $query = $this->db->WHERE($date_range)
            ->GET('website_visits_tbl')->num_rows();
        return $query;
    }
    public function getWebsiteVisitStat($date_range){
        $groupBy = 'DATE(created_at)';
        $query = $this->db->SELECT('DATE(created_at) as date, COUNT(views_id) as views')
            ->WHERE($date_range)
            ->GROUP_BY($groupBy)
            ->ORDER_BY('created_at','asc')
            ->GET('website_visits_tbl')->result_array();

        $result = array();
        foreach($query as $q){
            $array = array(
                'date'=>date('d/m', strtotime($q['date'])),
                'views'=>$q['views']
            );
            array_push($result, $array);
        }
        return $result;
    }
}