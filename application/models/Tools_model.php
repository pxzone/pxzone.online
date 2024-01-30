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
    public function getFiatValue($btc_balance, $currency){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=".$currency,
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
        $btc_fiat = $data_obj->bitcoin->$currency;
        return $btc_balance * $btc_fiat;
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
            CURLOPT_URL => "https://mempool.space/api/address/" . $wallet_address,
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
        $data = $data_obj;
        return $data;
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
        $api_key = "0039e6ff-bf5b-4986-8421-090137d94233";
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
        $api_key = "0039e6ff-bf5b-4986-8421-090137d94233";
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
  
    
}