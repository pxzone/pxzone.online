<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Tools extends CI_Controller {

	function __construct(){
        parent::__construct();
        $this->load->model('Site_settings_model');
        $this->load->model('Csrf_model');
        $this->load->model('Tools_model');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->library('Api_auth');
    }
    
    public function getwalletBalance(){
        $this->output->cache(.3);
        $status = false;
        $wallet_address = $this->input->get('wallet_address');
		$verify_wallet_address = $this->Tools_model->verifyWalletAddress($wallet_address);
        if($verify_wallet_address == true){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($wallet_address);
            $usd_value = $this->Tools_model->getFiatValue($balance, 'bitcoin', 'usd');
            $eur_value = $this->Tools_model->getFiatValue($balance, 'bitcoin', 'eur');
            $response = array(
                array(
                'balance'=>$balance,
                'usd_value'=>number_format($usd_value,2),
                'eur_value'=>number_format($eur_value, 2)
                )
            );
        }
        else{
            $response['status'] = 'error';
            $response['message'] = 'Invalid Address!';
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$response)));
    }
    public function walletAddressBalanceToImage($address){
        $btc_data = $this->Tools_model->getBtcBalance($address);
        $btc_balance = $btc_data; 
        
        if($this->input->get('currency') == 'usd' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($btc_balance, 'bitcoin', 'usd');
            $final_value = number_format($fiat_value,2).' USD';
        }
        else if($this->input->get('currency') == 'eur' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($btc_balance, 'bitcoin', 'eur');
            $final_value = number_format($fiat_value,2).' EUR';
        }
        else if($this->input->get('currency') == 'gbp' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($btc_balance, 'bitcoin', 'gbp');
            $final_value = number_format($fiat_value,2).' GBP';
        }
        else if($this->input->get('currency') == 'php' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($btc_balance, 'bitcoin', 'php');
            $final_value = number_format($fiat_value,2).' PHP';
        }
        else{
            $final_value = number_format($btc_balance,4). ' BTC';
        }
        $width = strlen($final_value) * 10.4;
        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15);
        if($this->input->get('color') && $this->input->get('color') !== ''){
            $hex = '#'.$this->input->get('color');
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }
        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 500 ; $i++) {  
            for ($j=0; $j < 500 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);

        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function btcPriceToImage($price){
        if($this->input->get('currency') == 'eur' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($price, 'bitcoin', 'eur');
            $final_value = number_format($fiat_value,2).' EUR';
        }
        else if($this->input->get('currency') == 'gbp' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($price, 'bitcoin', 'gbp');
            $final_value = number_format($fiat_value,2).' GBP';
        }
        else if($this->input->get('currency') == 'php' && $this->input->get('currency') !== null){
            $fiat_value = $this->Tools_model->getFiatValue($price, 'bitcoin', 'php');
            $final_value = number_format($fiat_value,2).' PHP';
        }
        else{
            $fiat_value = $this->Tools_model->getFiatValue($price, 'bitcoin', 'usd');
            $final_value = number_format($fiat_value,2).' USD';
        }

        $width = strlen($final_value) * 10.4;

        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15);
        if($this->input->get('color') && $this->input->get('color') !== ''){
            $hex = '#'.$this->input->get('color');
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }

        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 500 ; $i++) {  
            for ($j=0; $j < 500 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);
        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function btcPriceHistoryToImage($date){
        if($this->input->get('currency') == 'eur' && $this->input->get('currency') !== null){
            $btc_price = $this->Tools_model->btcPriceHistoryToImage($date,'eur');
            $final_value = number_format(round($btc_price,2),2).' EUR';
        }
        else if($this->input->get('currency') == 'gbp' && $this->input->get('currency') !== null){
            $btc_price = $this->Tools_model->btcPriceHistoryToImage($date,'gbp');
            $final_value = number_format(round($btc_price,2),2).' GBP';
        }
        else if($this->input->get('currency') == 'php' && $this->input->get('currency') !== null){
            $btc_price = $this->Tools_model->btcPriceHistoryToImage($date,'php');
            $final_value = number_format(round($btc_price,2),2).' PHP';
        }
        else{
            $btc_price = $this->Tools_model->btcPriceHistoryToImage($date,'usd');
            $final_value = number_format(round($btc_price,2),2).' USD';
        }
        
        $width = strlen($final_value) * 10.4;

        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15);
        
        if($this->input->get('color') && $this->input->get('color') !== ''){
            $hex = '#'.$this->input->get('color');
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }

        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 500 ; $i++) {  
            for ($j=0; $j < 500 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);
        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function fiatBitcoinToImage($fiat_value){
        if($this->input->get('currency') == 'eur' && $this->input->get('currency') !== null){
            $currency_value = $this->Tools_model->getFiatValue(1, 'bitcoin', 'eur');
            $final_value = round($fiat_value/$currency_value,5).' BTC';
        }
        else if($this->input->get('currency') == 'gbp' && $this->input->get('currency') !== null){
            $currency_value = $this->Tools_model->getFiatValue(1, 'bitcoin', 'gbp');
            $final_value = round($fiat_value/$currency_value,5).' BTC';
        }
        else if($this->input->get('currency') == 'php' && $this->input->get('currency') !== null){
            $currency_value = $this->Tools_model->getFiatValue(1, 'bitcoin', 'php');
            $final_value = round($fiat_value/$currency_value,5).' BTC';
        }
        else{
            $currency_value = $this->Tools_model->getFiatValue(1, 'bitcoin', 'usd');
            $final_value = round($fiat_value/$currency_value,5).' BTC';
        }
        $width = strlen($final_value) * 11;

        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15);
        
        if($this->input->get('color') && $this->input->get('color') !== ''){
            $hex = '#'.$this->input->get('color');
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }

        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 500 ; $i++) {  
            for ($j=0; $j < 500 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);
        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function saveBitcoinWalletWatcher(){
        $wallet_address = $this->input->post('wallet_address');
        $email_address = $this->input->post('email_address');
        $verify_wallet_address = $this->Tools_model->verifyWalletAddress($wallet_address);
        $check_wallet_email = $this->Tools_model->checkWalletEmail();
        
        $this->form_validation->set_rules('email_address', 'Email Address', 'required|trim|valid_email',
            array(
                'required' => '%s is required!',
            )
        );
        if($verify_wallet_address == null || $verify_wallet_address == 'Invalid Bitcoin address'){
            $response['status']='error';
            $response['message']='Invalid Bitcoin Address!';
        }
        else  if ($this->form_validation->run() == FALSE) {
            $response['status'] = 'error';
            $response['message'] = $this->form_validation->error_array('email_address');
        }
        else if($check_wallet_email > 0){
            $response['status']='error';
            $response['message']='Wallet and Email address already exists!';
        }
        else if(!empty($verify_wallet_address->address)){
            $unique_id = $this->Tools_model->generateUniqueID();
            $data_arr = array (
                'wallet_address'=>$wallet_address,
                'email_address'=>$email_address,
                // 'prev_num_tx'=>$verify_wallet_address->n_tx,
                'unique_id'=>$unique_id,
                'status'=>'active',
                'created_at'=>date('Y-m-d H:i:s'),
            );
            $this->Tools_model->insertNewWalletWatcherRecord($data_arr);
            $this->newSubscriberNotifierEmail($unique_id,$email_address);
            $response['status']='success';
            $response['message']="Thanks for subscribing. You will receive new notification email when there's a new transaction on your wallet address.";
        }
        else{
            $response['status']='error';
            $response['message']='Something went wrong!';
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$response)));

    }
    # CRON JOB
    public function bitcoinWalletWatcherNotifier(){
        $address = $this->Tools_model->getRecordsforNotification();
        if(!empty($address)){
            foreach($address as $a){
                $previous_num_transactions  = $a['prev_num_tx'];
                $to = $a['email_address'];
                $wallet_address = $a['wallet_address'];
                $unique_id = $a['unique_id'];

                $url = "https://mempool.space/api/address/$wallet_address/txs";
                $response = file_get_contents($url);

                if ($response !== false) {
                    $transactions = json_decode($response, true);

                    if (!empty($transactions)) {
                        // Get the latest transaction details
                        $latestTransaction = $transactions[0];
                        $txid = $latestTransaction['txid'];
                        $value = "";
                        $confirmations = $latestTransaction['confirmations'];
                        $isIncoming = $this->isTransactionIncoming($latestTransaction, $address);
                        $tx_type =  ($isIncoming ? 'incoming' : 'outgoing') . "\n";

                        $subject = 'New Outgoing Bitcoin Transaction';
                        if($isIncoming){
                            $subject = 'New Incoming Bitcoin Transaction';
                        }
                        // Send the email
                        $this->notifyWalletWatcherEmail($subject, $to, $value, $wallet_address, $unique_id, $txid);
                        $this->Tools_model->updateNewTxRecord($wallet_address, $to, $num_transactions);
                    } else {
                        echo "No transactions found for the address.\n";
                    }
                } else {
                    echo "Error fetching transactions.\n";
                }
            }
        }
        $data = array(
            'status'=>true,
            'response'=>''
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function isTransactionIncoming($transaction, $address) {
        $data['status'] == 'incoming';
        foreach ($transaction['vin'] as $input) {
            if ($input['prevout']['scriptpubkey_address'] === $address) {
                $data['status'] = 'outgoing';
                return $data; // Outgoing transaction
            }
        }
        if($data['status'] == 'outgoing'){
            foreach ($transaction['vout'] as $output) {
                $data['value'] = $output['value'];
                return $data; 
            }
        }
       
        return true; // Incoming transaction
    }
    public function newSubscriberNotifierEmail($unique_id, $email_address){
       
        $config = array (
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'priority' => '1'
        );
        $data['website_settings']= $this->Site_settings_model->siteSettings();;
        $data['header_image'] = base_url().'assets/images/logo/hh-logo-light.png';
        $data['header_image_url'] = base_url('tools/bitcoin-wallet-notifier') . '?utm_source=pxzone&utm_medium=notification&utm_campaign=email';
        $data['unique_id_url'] = base_url().'tools/wallet-notifier-logs/' . $unique_id;

        $this->email->initialize($config);
        $this->email->from('hello@pxzone.online','Wallet Transaction Notifier');
        $this->email->to($email_address); 
        $this->email->subject('Thanks for Subscribing!');
        $body = $this->load->view('email/new_wallet_notify_subscriber', $data, TRUE);
        $this->email->message($body);
        $this->email->send();
        
    }
    public function notifyWalletWatcherEmail($subject, $to, $amount, $wallet_address, $unique_id, $txid){
        $config = array (
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'priority' => '1'
        );
        $data['website_settings']= $this->Site_settings_model->siteSettings();;
        $data['header_image'] = base_url().'assets/images/logo/hh-logo-light.png';
        $data['header_image_url'] = base_url('tools/bitcoin-wallet-notifier') . '?utm_source=pxzone&utm_medium=notification&utm_campaign=email';
        $data['btc_value'] = ($amount/100000000);
        $data['usd_value'] = $this->Tools_model->getFiatValue($data['btc_value'],'usd');
        $data['wallet_address'] = $wallet_address;
        $data['unique_id_url'] = base_url().'tools/wallet-notifier-logs/' . $unique_id;
        $data['unique_id'] = $unique_id;
        $data['txid'] = 'https://blockstream.info/tx/' . $txid;
        $data['hash'] = $txid;

        $this->Tools_model->notifierLogs($data);

        $this->email->initialize($config);
        $this->email->from('hello@pxdev.click','Wallet Transaction Notifier');
        $this->email->to($to); 
        $this->email->subject($subject);
        $body = $this->load->view('email/bitcoin_wallet_watcher', $data, TRUE);
        $this->email->message($body);
        $this->email->send();
        $data = array(
            'status'=>'success',
            'message'=>'Email successfully sent!'
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function getNotifierLogs(){
        if (empty($this->input->get('unique_id'))) {
            $data['status'] = 'error';
            $data['message'] = "Action not allowed!";
        }
        else{
            $row_no = $this->input->get('page_no');
            // Row per page
            $row_per_page = 10;

            // Row position
            if($row_no != 0){
              $row_no = ($row_no-1) * $row_per_page;
            }

            // All records count
            $all_count = $this->Tools_model->getNotifierLogCount();

            // Get records
            $result = $this->Tools_model->getNotifierLogs($row_per_page, $row_no);

            // Pagination Configuration
            $config['base_url'] = base_url('api/v1/logs/_get_logs');
            $config['use_page_numbers'] = TRUE;
            $config['total_rows'] = $all_count;
            $config['per_page'] = $row_per_page;

            // Pagination with bootstrap
            $config['page_query_string'] = TRUE;
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
            $data['attribute'] = $this->Tools_model->getUniqueIDData();
            $data['result'] = $result;
            $data['row'] = $row_no;
            $data['count'] = $all_count;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function deleteUniqueIDRecord(){
        $data = $this->Tools_model->deleteUniqueIDRecord();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
    }
    public function bitcoinToolsStat(){
        if($this->input->get('misc') == 'unconfirmed_tx' && $this->input->get('misc') !== ''){
            $tool_url = "https://blockchain.info/q/unconfirmedcount";
            $data = $this->Tools_model->getBtcToolsMisc($tool_url);
            $final_value = number_format($data). ' Unconfirmed Transaction';
        }
        else if($this->input->get('misc') == '24h_tx_count' && $this->input->get('24h_tx_count') !== ''){
            $tool_url = "https://blockchain.info/q/24hrtransactioncount";
            $data = $this->Tools_model->getBtcToolsMisc($tool_url);
            $final_value = number_format($data) . '';
        }
        else if($this->input->get('misc') == '24h_btc_sent' && $this->input->get('24h_btc_sent') !== ''){
            $tool_url = "https://blockchain.info/q/24hrbtcsent";
            $data = $this->Tools_model->getBtcToolsMisc($tool_url);
            $final_value = number_format($data/1000000000) . '';
        }
        else if($this->input->get('misc') == 'latest_hash' && $this->input->get('latest_hash') !== ''){
            $tool_url = "https://blockchain.info/q/latesthash";
            $data = $this->Tools_model->getBtcToolsMisc($tool_url);
            $final_value = $data;
        }
        else if($this->input->get('misc') == 'block_height' && $this->input->get('latest_hash') !== ''){
            $block = $this->input->get('block');
            $tool_url = "https://mempool.space/api/block-height/".$block;
            $data = $this->Tools_model->getBtcToolsMisc($tool_url);
            $final_value = $data;
        }
        else if($this->input->get('misc') == 'fastest_fee' && $this->input->get('fastest_fee') !== ''){
            $tool_url = "https://mempool.space/api/v1/fees/recommended";
            $response = $this->Tools_model->getBtcToolsMisc($tool_url);
            $data_obj = json_decode($response);
            $data = $data_obj->fastestFee. ' sat/vBytes';
            $final_value = $data;
        }
        else if($this->input->get('misc') == 'fastest_fee' && $this->input->get('fastest_fee') !== ''){
            $tool_url = "https://mempool.space/api/v1/fees/recommended";
            $response = $this->Tools_model->getBtcToolsMisc($tool_url);
            $data_obj = json_decode($response);
            $data = $data_obj->fastestFee. ' sat/vBytes';
            $final_value = $data;
        }
        else if($this->input->get('misc') == 'economy_fee' && $this->input->get('economy_fee') !== ''){
            $tool_url = "https://mempool.space/api/v1/fees/recommended";
            $response = $this->Tools_model->getBtcToolsMisc($tool_url);
            $data_obj = json_decode($response);
            $data = $data_obj->economyFee. ' sat/vBytes';
            $final_value = $data;
        }
        else if($this->input->get('misc') == 'minimum_fee' && $this->input->get('minimum_fee') !== ''){
            $tool_url = "https://mempool.space/api/v1/fees/recommended";
            $response = $this->Tools_model->getBtcToolsMisc($tool_url);
            $data_obj = json_decode($response);
            $data = $data_obj->minimumFee. ' sat/vBytes';
            $final_value = $data;
        }
        else{
            $final_value = 'No result';
        }
        $width = strlen((string)$final_value) * 10.8;
        // Set the content-type
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15);
        if($this->input->get('color') && $this->input->get('color') !== ''){
            $hex = '#'.$this->input->get('color');
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }
        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < 700 ; $i++) {  
            for ($j=0; $j < 700 ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);

        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    public function bicoinFeeEstimate(){
        $tool_url = "https://mempool.space/api/v1/fees/recommended";
        $response = $this->Tools_model->getBtcToolsMisc($tool_url);
        $this->output->set_content_type('application/json')->set_output($response);
    }
    public function testEmail(){
        $config = array (
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'priority' => '1'
        );
        $data['website_settings']= $this->Site_settings_model->siteSettings();;
        $data['header_image'] = base_url().'assets/images/logo/hh-logo-light.png';
        $data['header_image_url'] = base_url('tools/bitcoin-wallet-notifier') . '?utm_source=pxdev&utm_medium=notification&utm_campaign=email';
        $this->email->initialize($config);
        $this->email->from('hello@pxzone.online','Test');
        $this->email->to('kenkarlodev@gmail.com'); 
        $this->email->subject('Test Email');
        $body = 'This is a test email set for every 1 minute';
        $this->email->message($body);
        $this->email->send();
        $data = array(
            'status'=>'success',
            'message'=>'Email successfully sent!'
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function cryptoPriceToImage(){
        $token = $this->input->get('token');
        $coin = $this->input->get('coin');
        $color = $this->input->get('color');
        $address = $this->input->get('address');
        $currency = $this->input->get('currency');
        $balance_only = $this->input->get('balance_only');

        $ticker = "";
        $balance = 0.00;

        if(empty($token) && empty($coin) && empty($color) && empty($address) && empty($currency)){
            $show[0] = "Your lucky numbers on 6/55 \"".rand(0,55).' - '.rand(0,55).' - '.rand(0,55).' - '.rand(0,55).' - '.rand(0,55).' - '.rand(0,55)."\"";
            $show[1] = "How do you get $1,000 in cryptocurrency? Invest $2,000 on ICO.";
            $show[2] = "What's the difference between investing in bitcoin and getting married? If your marriage fails, you only lose half of your wealth. ";
            $show[3] = "Why won't the government embrace Bitcoin? They hate the idea of Proof Of Work.";
            $show[4] = "Uhhh....... ye?";

            $data = rand(0, 4);
            $final_value = $show[$data];
            $color = "";
            $this->textToImage($final_value, $color);
        }
        else if($coin == 'bitcoin'){
            $balance = $this->Tools_model->getBtcBalance($address);
            // $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'btc');
            $ticker = "BTC";
        }
        else if($coin == 'ethereum' && $token == 'usdt'){
            $balance = $this->Tools_model->okLinkFetchTokenBalanceApi($address, 'eth', 'token_20');
            $ticker = "TRX";
        }
        else if($coin == 'ethereum'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'eth');
            $ticker = "ETH";
        }
        else if($coin == 'binancecoin'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'bsc');
            $ticker = "BNB";
        }
        else if($coin == 'tron' && $token == 'usdt'){
            $balance = $this->Tools_model->okLinkFetchTokenBalanceApi($address, $coin, 'token_20');
            $ticker = "USDT";
        }
        else if($coin == 'tron'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'tron');
            $ticker = "TRX";
        }
        else if($coin == 'litecoin'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'ltc');
            $ticker = "LTC";
        }
        else if($coin == 'dogecoin'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'doge');
            $ticker = "DOGE";
        }
        else if($coin == 'bitcoin-cash'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'bch');
            $ticker = "BCH";
        }
        else if($coin == 'dash'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'dash');
            $ticker = "DASH";
        }
        else if($coin == 'matic'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'polygon');
            $ticker = "MATIC";
        }
        else {
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, $coin);
            $ticker = strtoupper($coin);
        }

        
        // $num_balance = number_format($balance,6);
        if(!empty($currency) && $token !== 'usdt'){
            $curency_balance = '- '.$this->getCurrencyBalance($balance, $coin, $currency);
        }
        else{
            $curency_balance = "";
        }

        $final_value = "{$address} {$balance} {$ticker} {$curency_balance}";
        if(!empty($token) && $token == 'usdt' && $balance_only == true){
            $final_value = "{$balance} USDT";
            $this->textToImage($final_value, $color);
        }
        else if($balance_only == true){
            $final_value = "{$balance} {$ticker} {$curency_balance}";
            $this->textToImage($final_value, $color);
        }
        else if($token == 'usdt'){
            $final_value = "{$address} {$balance} USDT";
            $this->textToImage($final_value, $color);
        }
        else {
            $this->textToImage($final_value, $color);
        }
        
        // $this->output->set_content_type('application/json')->set_output($balance);

    }
    public function textToImage($final_value, $color){
        $width = strlen($final_value) * 9.5;
        header ('Content-Type: image/png');
        $im = imagecreatetruecolor($width, 15.5);
        if($color && $color !== ''){
            $hex = '#'.$color;
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $text_color = imagecolorallocate($im, $r, $g, $b);
        }
        else{
            $text_color = imagecolorallocate($im, 0,0,0);
        }
        $bg_color = imagecolorallocate($im, 255, 255, 255); 
        
        for ($i=0; $i < $width ; $i++) {  
            for ($j=0; $j < $width ; $j++) {  
                imagesetpixel ($im ,$i,$j ,$bg_color);
            }
        }
        $color = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $color);
        imagesavealpha($im, true);

        imagestring($im, 5, 5, 0, $final_value, $text_color);
        imagepng($im);
        imagedestroy($im);
    }
    function okLinkFetchTokenBalanceApi(){
        $coin = $this->input->get('coin');
        $protocol_type = $this->input->get('protocol_type');
        $address = $this->input->get('address');
        $data = $this->Tools_model->okLinkFetchTokenBalanceApi($address, $coin, $protocol_type);
        $this->output->set_content_type('application/json')->set_output($data);

    }
    public function getCurrencyBalance($balance, $coin, $currency){
        $fiat_value = $this->Tools_model->getCyptoToFiatValue($balance, $coin, $currency);
        
        // if($currency == 'usd' && $currency !== null){
        //     $final_value = number_format($fiat_value, 2);
        // }
        // else if($currency == 'eur' && $currency !== null){
        //     $final_value = number_format($fiat_value, 2);
        // }
        // else if($currency == 'gbp' && $currency !== null){
        //     $final_value = number_format($fiat_value, 2);
        // }
        // else if($currency == 'php' && $currency !== null){
        //     $final_value = number_format($fiat_value, 2);
        // }
        return number_format($fiat_value, 2) .' '.strtoupper($currency);
    }
    public function getCryptoWalletBalance(){
        $status = false;
		$data_balance = $this->getCryptoBalance();
        if($data_balance){
            // $btc_balance = $this->Tools_model->getCryptoBalance($wallet_address);
            $usd_value = $this->Tools_model->getFiatValue($data_balance['balance'], $data_balance['coin_name'], 'usd');
            $eur_value = $this->Tools_model->getFiatValue($data_balance['balance'], $data_balance['coin_name'], 'eur');
            $response = array(
                'balance'=>$data_balance['balance'],
                'ticker'=>$data_balance['ticker'],
                'usd_value'=>number_format($usd_value,2),
                'eur_value'=>number_format($eur_value, 2)
            );
        }
        else{
            $response['status'] = 'error';
            $response['message'] = 'Invalid Address!';
        }
		
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$response)));
    }
    public function getCryptoBalance(){
        $coin = $this->input->get('coin');
        $address = $this->input->get('wallet_address');

        if($coin == 'btc'){
            // $balance = $this->Tools_model->getBtcBalance($address);
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'btc');
            $ticker = "BTC";
            $coin_name = "bitcoin";
        }
        else if($coin == 'ethereum' && $token == 'usdt'){
            $balance = $this->Tools_model->okLinkFetchTokenBalanceApi($address, 'eth', 'token_20');
            $ticker = "TRX";
            $coin_name = "USDT";
        }
        else if($coin == 'eth'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'eth');
            $ticker = "ETH";
            $coin_name = "ethereum";
        }
        else if($coin == 'bsc' || $coin == 'bnb'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'bsc');
            $ticker = "BNB";
            $coin_name = "binancecoin";
        }
        else if($coin == 'tron' && $token == 'usdt'){
            $balance = $this->Tools_model->okLinkFetchTokenBalanceApi($address, $coin, 'token_20');
            $ticker = "USDT";
            $coin_name = "USDT";
        }
        else if($coin == 'tron'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'tron');
            $ticker = "TRX";
            $coin_name = "tron";
        }
        else if($coin == 'ltc'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'ltc');
            $ticker = "LTC";
            $coin_name = "litecoin";
        }
        else if($coin == 'doge'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'doge');
            $ticker = "DOGE";
            $coin_name = "dogecoin";
        }
        else if($coin == 'bitcoin-cash'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'bch');
            $ticker = "BCH";
            $coin_name = "dogecoin";
        }
        else if($coin == 'dash'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'dash');
            $ticker = "DASH";
            $coin_name = "dash";
        }
        else if($coin == 'matic'){
            $balance = $this->Tools_model->okLinkFetchCoinBalanceApi($address, 'polygon');
            $ticker = "MATIC";
            $coin_name = "matic-network";
        }

        $data = array(
            'balance'=>$balance,
            'ticker'=>$ticker,
            'coin_name'=>$coin_name
        );
        return $data;
    }
    public function checkUptimeStatus() {
        // $data = shell_exec('uptime');
        // $uptime = explode(' up ', $data);
        // $uptime = explode(',', $uptime[1]);
        // $uptime = $uptime[0].', '.$uptime[1];

        $header_check = get_headers("https://www.altcoinstalks.com");
        $response_code = $header_check[0];
        $data_res = array(
            'website'=>"https://www.altcoinstalks.com",
            'status'=>$response_code,
        );
        $this->output->set_content_type('application/json')->set_output(json_encode(array($data_res)));
    }
    
}
