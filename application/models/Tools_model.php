<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools_model extends CI_Model {
    public function getBtcBalance($wallet_address){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mempool.space/api/address/".$wallet_address,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        $funded_txo_sum = $response->chain_stats->funded_txo_sum;
        $spent_txo_sum = $response->chain_stats->spent_txo_sum;
        $balance = $funded_txo_sum - $spent_txo_sum;
        return $balance / 100000000; 
    }
    public function getFiatValue($coin_balance, $coin_name, $currency){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.coingecko.com/api/v3/simple/price?ids=$coin_name&vs_currencies=".$currency,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data_obj = json_decode($response);
        $coin_to_fiat = $data_obj->$coin_name->$currency;
        return $coin_balance * $coin_to_fiat;
    }
    public function btcPriceHistoryToImage($date,$currency){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.coingecko.com/api/v3/coins/bitcoin/history?date=".$date,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data_obj = json_decode($response);
        $btc_usd = $data_obj->market_data->current_price->$currency;
        return $btc_usd;
    }
    public function getWalletTxs($address){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://blockstream.info/api/address/".$address."/txs",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data_obj = json_decode($response);
        return $data_obj;
    }
    public function verifyWalletAddress($wallet_address){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mempool.space/api/v1/validate-address/" . $wallet_address,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data_obj = json_decode($response);
        if($data_obj->isvalid == true){
            return true;
        }
        else{
            return false;
        }
    }
    public function getBtcToolsMisc($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function insertNewWalletWatcherRecord($data_arr){
        $this->db ->INSERT('wallet_watcher_tbl', $data_arr);
    }
    public function checkWalletEmail(){
        return $this->db->WHERE('wallet_address',$this->input->post('wallet_address'))
            ->WHERE('email_address',$this->input->post('email_address'))
            ->GET('wallet_watcher_tbl')->num_rows();
    }
    public function getRecordsforNotification(){
        return $this->db->SELECT('unique_id, wallet_address, email_address, prev_num_tx')
            ->WHERE('status','active')
            ->GET('wallet_watcher_tbl')->result_array();
    }
    public function generateUniqueID ($length = 32) {
        $unique_id = sprintf( '%04x-%04x-%04x-%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0C2f ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0x2Aff ), mt_rand( 0, 0xffD3 ), mt_rand( 0, 0xff4B )
        );
        $check = $this->db->WHERE('unique_id',$unique_id)->GET('wallet_watcher_tbl')->num_rows();
        if ($check > 0) {
            $this->generateUniqueID();
        }
        else{
           return $unique_id;
        }
    }
    public function updateNewTxRecord($address, $to, $prev_num_tx){
        $data_arr = array('prev_num_tx'=>$prev_num_tx);
        $this->db->WHERE('wallet_address',$address)
            ->WHERE('email_address',$to)
            ->UPDATE('wallet_watcher_tbl',$data_arr);
    }
    public function notifierLogs($data){
        $data_arr = array(
            'unique_id'=>$data['unique_id'],
            'wallet_address'=>$data['wallet_address'],
            'txid'=>$data['hash'],
            'btc_value'=>$data['btc_value'],
            'usd_value'=>$data['usd_value'],
            'tx_date'=>date('Y-m-d H:i:s', $data['datetime']),
            'created_at'=>date('Y-m-d H:i:s'),
        );
        $this->db->INSERT('wallet_notifier_logs_tbl',$data_arr);
    }
    public function verifyUniqueID($id){
        return $this->db->WHERE('unique_id',$id)->GET('wallet_watcher_tbl')->num_rows();
    }
    public function getNotifierLogCount(){
        return $this->db->WHERE('unique_id',$this->input->get('unique_id'))
            ->GET('wallet_notifier_logs_tbl')->num_rows();
    }
    public function getNotifierLogs($row_per_page, $row_no){
        $query = $this->db->SELECT('txid, btc_value, usd_value, tx_date')
            ->WHERE('unique_id',$this->input->get('unique_id'))
            ->ORDER_BY('created_at', 'desc')
            ->LIMIT($row_per_page, $row_no)
            ->GET('wallet_notifier_logs_tbl')->result_array();
            $result = array();
        
        foreach($query as $q){
            $array = array(
                'txid'=> '<a href="https://blockstream.info/tx/'.$q['txid'].'" target="_blank" rel="noopener nofollow">'.substr($q['txid'],0,10).'...'.substr($q['txid'],-10).'</a>',
                'btc_value'=>$q['btc_value'],
                'usd_value'=>number_format(round($q['usd_value'], 2),2),
                'tx_date'=>date('m/d/Y h:i A', strtotime($q['tx_date'])),
            );
            array_push($result, $array);
        }
        return $result;
    }
    public function getUniqueIDData(){
        return $this->db->SELECT('wallet_address,email_address')->WHERE('unique_id',$this->input->get('unique_id'))->GET('wallet_watcher_tbl')->row_array();
    }
    public function deleteUniqueIDRecord(){
        if(!empty($this->input->post('unique_id'))){
            $this->db->WHERE('unique_id', $this->input->post('unique_id'))
                ->DELETE('wallet_notifier_logs_tbl');
            
            $this->db->WHERE('unique_id', $this->input->post('unique_id'))
                ->DELETE('wallet_watcher_tbl');  
            
            $response['status'] = 'success';
            $response['message'] = 'Record successfully deleted!';
        }
        return $response;
    }
    public function getCyptoToFiatValue($balance, $coin, $currency){
       
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.coingecko.com/api/v3/simple/price?ids=$coin&vs_currencies=$currency",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data_obj = json_decode($response);
        $coin_fiat = $data_obj->$coin->$currency;

        $balance = $balance * $coin_fiat;

        return $balance;
    }

    public function getEthBalance($address) {
        $api_key = "S6SM9YWP8NI2X8ERE58RI7X6SRBAR3YA31";
        $api_url = "https://api.etherscan.io/api?module=account&action=balance&address={$address}&tag=latest&apikey={$api_key}";

        try {
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            if ($data['status'] == '1') {
                // Convert balance from wei to ether
                $balanceWei = (int)$data['result'];
                $balanceEth = $balanceWei / 1e18;
                return number_format($balanceEth, 4);
            } else {
                echo "Error: {$data['message']}\n";
                return null;
            }
        } catch (Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            return null;
        }
    }
    public function getBnbBalance($address) {
        $api_key = "IAZZX68E3JZD4WTVXU2Q179BMQVTUJ2119";
        $api_url = "https://api.bscscan.com/api?module=account&action=balance&address={$address}&apikey={$api_key}";

        try {
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            if ($data['status'] == '1') {
                // Convert balance from wei to ether
                $balanceWei = (int)$data['result'];
                $balanceBnb = $balanceWei / 1e18;
                return number_format($balanceBnb, 6);
            } else {
                echo "Error: {$data['message']}\n";
                return null;
            }
        } catch (Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            return null;
        }
    }
    public function okLinkFetchCoinBalanceApi($address, $coin){
        $auth = $this->api_auth->authKeys();
        $api_key = $auth['okLink_key'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.oklink.com/api/v5/explorer/address/address-summary?chainShortName=$coin&address=$address");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = "Ok-Access-Key: $api_key";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if(isset($data['data'][0]['balance'])){
            $balance = $data['data'][0]['balance'] ; 
        }
        else{
            $balance = 0.00;
        }
        return $balance;
    }
    public function okLinkFetchTokenBalanceApi($address, $coin, $protocol_type){
        $auth = $this->api_auth->authKeys();
        $api_key = $auth['okLink_key'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.oklink.com/api/v5/explorer/address/token-balance?chainShortName=$coin&address=$address&protocolType=$protocol_type");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = "Ok-Access-Key: $api_key";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($response, true);
        
        if ($data && isset($data['data'][0]['tokenList'])) {
            // Extract USDT data
            $usdtData = null;
            foreach ($data['data'][0]['tokenList'] as $token) {
                if ($token['symbol'] === 'USDT') {
                    $usdtData = $token;
                    break;
                }
            }
            if ($usdtData) {
                $balance = $usdtData['holdingAmount'];
            }
            else{
                $balance = 0.00;
            }
        }
        else{
            $balance = 0.00;
        }

        // return $response;
        return $balance;
        // return $usdtData['holdingAmount'];
    }
    // okilink api_key 
  
    public function getWebsiteList(){
        return $this->db->SELECT('id, website_url')
            ->WHERE('status', 'active')
            ->GET('monitor_website_tbl')->result_array();
    }
    public function insertWebsiteActivity($data_arr){
        $this->db->INSERT('monitor_website_activity_tbl', $data_arr);
    }
    public function getMonitoredSiteStatus($site){
        return $this->db->SELECT('name, website_url')
            ->WHERE('name', $site)
            ->WHERE('status', 'active')
            ->GET('monitor_website_tbl')->row_array();
    }
    public function getMonitorWebsiteDataChart(){
        $site = $this->input->get('site');

        if($this->agent->is_mobile()){
            $date_range = array('mwat.created_at >'=>date('Y-m-d 00:00:00', strtotime('-30 days')), 'mwat.created_at <'=> date('Y-m-d H:i:s'));
            $vert_count = 30;
            $query_all = $this->db->SELECT('mwat.response_time, mwat.status, mwat.created_at')
                ->FROM('monitor_website_tbl as mwt')
                ->JOIN('monitor_website_activity_tbl as mwat', 'mwat.website_id=mwt.id', 'left')
                ->WHERE('mwt.name', $site)
                ->WHERE('mwt.status', 'active')
                ->WHERE($date_range)
                ->ORDER_BY('mwat.created_at','desc')
                ->GET()->result_array();
        }
        else{
            $date_range = array('mwat.created_at >'=>date('Y-m-d 00:00:00', strtotime('-60 days')), 'mwat.created_at <'=> date('Y-m-d H:i:s'));
            $vert_count = 60;
            $query_all = $this->db->SELECT('mwat.response_time, mwat.status, mwat.created_at')
                ->FROM('monitor_website_tbl as mwt')
                ->JOIN('monitor_website_activity_tbl as mwat', 'mwat.website_id=mwt.id', 'left')
                ->WHERE('mwt.name', $site)
                ->WHERE('mwt.status', 'active')
                ->WHERE($date_range)
                ->ORDER_BY('mwat.created_at','desc')
                ->GET()->result_array();
        }
        $latest_row = $this->getUptimeLatestRow($site);
        
        $result_arr = array();
        foreach($query_all as $q){
            $row_array = array(
                'response_time' => $q['response_time'],
                'status' => $q['status'],
                'date' => date('M d, Y', strtotime($q['created_at']))
            );
            array_push($result_arr, $row_array);
        }
        $result_arr;

        $grouped = array();
        foreach($result_arr as $entry){
            $date = $entry['date'];
            $status = $entry['status'];
            if(!isset($grouped[$date])){
                $grouped[$date] = array(
                    'date'=>$date,
                    'status'=>array(),
                    'down_count'=>0,
                );
            }
            $grouped[$date]['status'][] = $status;
            if ($status == 'down') {
                $grouped[$date]['down_count']++;
            }
        }
        foreach ($grouped as &$entry) {
            $status = in_array('down', $entry['status']) ? 'down' : 'up';
            $entry['status'] = $status;
        }
        $response['data'] = array_values($grouped);
        $response['latest_query'] = $latest_row;
        $response['count'] = $vert_count;
        return $response;
    }
    public function getUptimeLatestRow($site){
        return $this->db->SELECT('mwat.response_time, mwat.status, mwat.created_at')
            ->FROM('monitor_website_tbl as mwt')
            ->JOIN('monitor_website_activity_tbl as mwat', 'mwat.website_id=mwt.id', 'left')
            ->WHERE('mwt.name', $site)
            ->WHERE('mwt.status', 'active')
            ->LIMIT(1)
            ->ORDER_BY('mwat.created_at','desc')
            ->GET()->row_array();
    }
    public function getMonitorWebsiteActivity(){
        $site = $this->input->get('site');
        $date_range = array('mwat.created_at >'=>date('Y-m-d 00:00:00', strtotime('-15 days')), 'mwat.created_at <'=> date('Y-m-d H:i:s'));
        $query_all = $this->db->SELECT('mwat.response_time, mwat.status, mwat.status_code, mwat.created_at')
            ->FROM('monitor_website_tbl as mwt')
            ->JOIN('monitor_website_activity_tbl as mwat', 'mwat.website_id=mwt.id', 'left')
            ->WHERE('mwt.name', $site)
            ->WHERE('mwt.status', 'active')
            ->WHERE('mwat.status', 'down')
            ->WHERE($date_range)
            ->ORDER_BY('mwat.created_at','desc')
            ->GET()->result_array();
        
            $result_arr = array();
            foreach($query_all as $q){
                $row_array = array(
                    'response_time' => $q['response_time'],
                    'status' => $q['status'],
                    'status_code' => $q['status_code'],
                    'date' => date('M d, Y H:i:s', strtotime($q['created_at']))
                );
                array_push($result_arr, $row_array);
            }
            $result_arr;
    
            $grouped = array();
            $details = array();
            $count = 1;
            foreach($result_arr as $entry){
                $datetime = date('h:i:s A', strtotime($entry['date']));
                $date = date('M d, Y', strtotime($entry['date']));
                $status = $entry['status'];
                if(!isset($grouped[$date])){
                    $grouped[$date] = array(
                        'id'=>$count,
                        'date'=>$date,
                        'status'=>$status,
                        'details'=>array(),
                    );
                    $count++;
                }
            }
            foreach ($result_arr as $entry2) {
                $date = date('M d, Y', strtotime($entry2['date']));
                $details_arr = array(
                    'datetime' =>date('h:i:s A', strtotime($entry2['date'])),
                    'status_code' =>$entry2['status_code'],
                    'response_time' =>$entry2['response_time'],
                );
                array_push($grouped[$date]['details'] , $details_arr);
            }
            
            $response['data'] = array_values($grouped);
            return $response;
        
    }
    public function getResponseTimeActivity(){
        $site = $this->input->get('site');
        $date_range = array('mwat.created_at >'=>date('Y-m-d 00:00:00', strtotime('-24 hours')), 'mwat.created_at <'=> date('Y-m-d H:i:s'));
        $query_all = $this->db->SELECT('mwat.response_time, mwat.created_at as date')
            ->FROM('monitor_website_tbl as mwt')
            ->JOIN('monitor_website_activity_tbl as mwat', 'mwat.website_id=mwt.id', 'left')
            ->WHERE('mwt.name', $site)
            ->WHERE('mwt.status', 'active')
            ->WHERE($date_range)
            // ->LIMIT(360)
            ->GROUP_BY('hour(mwat.created_at)')
            ->GROUP_BY('hour(mwat.response_time)')
            ->ORDER_BY('mwat.created_at','desc')
            ->GET()->result_array();
        $result = array();
        foreach($query_all as $q){
            $row_data = array(
                'date'=>date('M d h:i A', strtotime($q['date'])),
                'response_time'=>substr($q['response_time'], 0, -2)
            );
            array_push($result, $row_data);
        }
        return $result;
    }
    
}