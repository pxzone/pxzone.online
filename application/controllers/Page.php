<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Page extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->library('user_agent');
        $this->load->model('Site_settings_model');
        $this->load->model('Csrf_model');
        $this->load->model('User_model');
        $this->load->model('Blog_model');
        $this->load->model('Tools_model');
    }
    public function about(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'About Us';
        $data['description'] = 'Learn more about bitcoin and crptocurrency tools';
        $data['canonical_url'] = base_url('about');
        $data['state'] = "about";
        $data['url_param'] = "";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('home/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/about');
    	$this->load->view('home/footer');
    }
    public function donate(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'Donate';
        $data['description'] = '
        ';
        $data['canonical_url'] = base_url('donate');
        $data['state'] = "donate";
        $data['url_param'] = "";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('home/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/donate');
    	$this->load->view('home/footer');
    }
    public function privacy(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'Privacy Policy';
        $data['description'] = 'By using the PX Dev website, you consent to the data practices described in this statement.';
        $data['canonical_url'] = base_url('privacy');
        $data['url_param'] = "";
        $data['state'] = "privacy";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('home/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/privacy');
    	$this->load->view('home/footer');
    }
    public function terms(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'Terms of Use';
        $data['description'] = 'By using the PX Dev website, you consent to the data practices described in this statement.';
        $data['canonical_url'] = base_url('privacy-terms');
        $data['url_param'] = "";
        $data['state'] = "terms";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('home/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/terms');
    	$this->load->view('home/footer');
    }
    public function login(){
        if(isset($_COOKIE['remember_login'])) {
            $userCookieData = $this->User_model->checkCookie($_COOKIE['remember_login']); //check if cookie token is the same on server
            $last_url = $this->input->get('return');
            if (isset($userCookieData)) {
                $this->session->set_userdata('user_id', $userCookieData['user_id']);
                $this->session->set_userdata($userCookieData['user_type'], $userCookieData['user_type']);
                $this->session->set_userdata('username', $userCookieData['username']);

                $message = 'Logged in using remember token cookie.';
                $this->User_model->insertActivityLog($message); 


                if ($last_url != '') {
                    header('location:'.base_url( ).$last_url);
                }
                else{
                    header('location:'.base_url('account/dashboard'));
                }

            }
            else{
                unset($_COOKIE['remember_login']); 
                setcookie('remember_login', '', time() - 3600, '/');
                $session = array(
                    'user_id', 
                    'username',
                );
                $this->session->unset_userdata($session);
                header('location:'.base_url('login?return=').uri_string());
            }
        }
        else if (!isset($this->session->user_id)) {
            $data['siteSetting'] = $this->Site_settings_model->siteSettings();
            $data['social_media'] = $this->Site_settings_model->getSocialMedias();
            $data['title'] = 'Login';
            $data['description'] = 'Login your account.';
            $data['canonical_url'] = base_url('login');
            $data['url_param'] = "";
            $data['state'] = "login";
            $data['login_token'] = base64_encode( openssl_random_pseudo_bytes(32)); /* generated token */
            $data['csrf_data'] = $this->Csrf_model->getCsrfData();
            $this->load->view('account/header', $data);
            $this->load->view('home/nav');
            $this->load->view('account/login');
            $this->load->view('home/footer');
        }
        else{
           header('location:'.base_url('account/dashboard')); 
        }
    }
    public function dashboard(){
        if (isset($this->session->user_id)) {
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'Dashboard';
        $data['description'] = 'Login your account.';
        $data['canonical_url'] = base_url('dashboard');
        $data['url_param'] = "";
        $data['state'] = "dashboard";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $data['user_data'] = $this->User_model->getUserData(); 
    	$this->load->view('account/header', $data);
    	$this->load->view('account/nav');
    	$this->load->view('account/dashboard');
    	$this->load->view('account/footer');
        }
        else{
            header('location:'.base_url('login?return=').uri_string());
        }
    }
    public function urlList(){
        if (isset($this->session->user_id)) {
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['title'] = 'URL List';
        $data['description'] = 'URL lists';
        $data['canonical_url'] = base_url('url-list');
        $data['url_param'] = "";
        $data['state'] = "url_list";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $data['user_data'] = $this->User_model->getUserData(); 
    	$this->load->view('account/header', $data);
    	$this->load->view('account/nav');
    	$this->load->view('account/url_list');
    	$this->load->view('account/footer');
        }
        else{
            header('location:'.base_url('login?return=').uri_string());
        }
    }
    public function article($url){
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['article_data'] = $this->Blog_model->getArticleDataURL($url);
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        if (!empty($data['article_data']['title']) && $data['article_data']['status'] == 'published') {
            $data['canonical_url'] = $data['article_data']['url'];
            $data['state'] = 'article';
            $data['url_param'] = '';
            $data['title'] = $data['article_data']['title'];
            $data['description'] = $data['article_data']['description'];
            $data['nonce'] = $this->Site_settings_model->generateNonce();
            $this->load->view('article/header', $data);
            $this->load->view('article/navbar');
            $this->load->view('article/article');
            $this->load->view('article/footer');
        }
        else{
            $this->Site_settings_model->error404();
        }
    }
    public function category($category){
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['nonce'] = $this->Site_settings_model->generateNonce();
        $data['title'] = 'Category '.ucwords($category);
        $data['category'] = ucwords($category);
        $data['description'] = $category. '';
        $data['canonical_url'] = base_url('category/').$category;
        $data['state'] = 'blog_category';
        $data['url_param'] = '';
        $this->load->view('home/header', $data);
        $this->load->view('home/nav');
        $this->load->view('article/category');
        $this->load->view('article/footer');
    }
    public function tags($tags){
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['nonce'] = $this->Site_settings_model->generateNonce();
        $data['title'] = 'Tag '.ucwords(str_replace('-',' ',$tags));
        $data['tags'] = ucwords($tags);
        $data['description'] = $tags. '';
        $data['canonical_url'] = base_url('tags/').$tags;
        $data['state'] = 'blog_tags';
        $data['url_param'] = '';
        $this->load->view('home/header', $data);
        $this->load->view('home/nav');
        $this->load->view('article/tags');
        $this->load->view('article/footer');
    }
    public function draft($url){
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['article_data'] = $this->Blog_model->getArticleDataURL($url);
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        if (!empty($data['article_data']['title']) && $data['article_data']['status'] == 'draft') {
            $data['canonical_url'] = $data['article_data']['url'];
            $data['state'] = 'article';
            $data['url_param'] = '';
            $data['title'] = $data['article_data']['title'];
            $data['description'] = $data['article_data']['description'];
            $data['nonce'] = $this->Site_settings_model->generateNonce();
            $this->load->view('article/header', $data);
            $this->load->view('article/navbar');
            $this->load->view('article/article');
            $this->load->view('article/footer');
        }
        else{
            $this->Site_settings_model->error404();
        }
    }
    public function blog(){
        $data['recent_blog_data'] = $this->Blog_model->getRecentArticleDataForPage();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['blog_data'] = $this->Blog_model->getArticleDataForPage();
        $data['user_data'] = $this->User_model->getUserData(); 
        $data['canonical_url'] = base_url('blog');
        $data['description'] = 'Learn more about bitcoin and cryptocurrency tools, news, tips and tricks available online.';
        $data['title'] = 'Blog';
        $data['state'] = 'blog';
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $data['url_param'] = "";
        $this->load->view('home/header', $data);
        $this->load->view('home/nav');
        $this->load->view('home/blog');
        $this->load->view('home/footer');
    }
    public function tools(){
        $data['recent_blog_data'] = $this->Blog_model->getRecentArticleDataForPage();
        $data['social_media'] = $this->Site_settings_model->getSocialMedias();
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['blog_data'] = $this->Blog_model->getArticleDataForPage();
        $data['user_data'] = $this->User_model->getUserData(); 
        $data['canonical_url'] = base_url('tools');
        $data['description'] = 'Learn more about bitcoin and cryptocurrency tools, news, tips and tricks available online.';
        $data['title'] = 'Tools';
        $data['state'] = 'tools';
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $data['url_param'] = "";
        $this->load->view('home/header', $data);
        $this->load->view('home/nav');
        $this->load->view('home/tools');
        $this->load->view('home/footer');
    }
    public function blogList(){
        if (isset($this->session->user_id)) {
            $data['user_data'] = $this->User_model->getUserData(); 
            $data['siteSetting'] = $this->Site_settings_model->siteSettings();
            $data['canonical_url'] = base_url('account/blog');
            $data['nonce'] = $this->Site_settings_model->generateNonce();
            $data['description'] = '';
            $data['title'] = 'Blog List';
            $data['state'] = 'blog_list';
            $data['csrf_data'] = $this->Csrf_model->getCsrfData();
            $data['url_param'] = "";
            $this->load->view('account/header', $data);
            $this->load->view('account/nav');
            $this->load->view('account/blog_list');
            $this->load->view('account/footer');
        }
        else{
           header('location:'.base_url('login?return=').uri_string());
        }
   }
   public function newBlog(){
        if (isset($this->session->user_id)) {
            $data['user_data'] = $this->User_model->getUserData(); 
            $data['siteSetting'] = $this->Site_settings_model->siteSettings();
            $data['canonical_url'] = base_url('account/blog/new');
            $data['nonce'] = $this->Site_settings_model->generateNonce();
            $data['csrf_data'] = $this->Csrf_model->getCsrfData();
            $data['blog_category'] = $this->Blog_model->getCategorySelect();
            $data['description'] = '';
            $data['title'] = 'New Blog';
            $data['state'] = 'new_blog';
            $data['url_param'] = "";
            $this->load->view('account/header', $data);
            $this->load->view('account/nav');
            $this->load->view('account/add_blog');
            $this->load->view('account/footer');
         }
        else{
            header('location:'.base_url('login?return=').uri_string());
        }
    }
    public function editBlog($article_pub_id){
        if (isset($this->session->user_id)) {
            $data['user_data'] = $this->User_model->getUserData(); 
            $data['siteSetting'] = $this->Site_settings_model->siteSettings();
            $data['canonical_url'] = base_url('account/blog/edit/'.$article_pub_id);
            $data['nonce'] = $this->Site_settings_model->generateNonce();
            $data['csrf_data'] = $this->Csrf_model->getCsrfData();
            $data['article_data'] = $this->Blog_model->getArticleDataID($article_pub_id);
            $content = $data['article_data']['content'];
            $data['article_data']['content'] = $this->unAmpify($content);
            $data['blog_category'] = $this->Blog_model->getCategorySelect();
            $data['description'] = '';
            $data['title'] = 'Edit Blog';
            $data['state'] = 'edit_blog';
            $data['url_param'] = "";
            $this->load->view('account/header', $data);
            $this->load->view('account/nav');
            $this->load->view('account/edit_blog');
            $this->load->view('account/footer');
         }
        else{
            header('location:'.base_url('login?return=').uri_string());
        }
    }
    public function unAmpify($html) {
	    # Replace amp custom elements with default img, audio, and video elements/tags
	    $html = str_ireplace(
	        ['<amp-youtube','<amp-img','<amp-video','/amp-video>','</amp-audio','/audio>'],
	        ['<video','<img','<video','/video>','<amp-audio','/audio>'],
	        $html
	    );

	    # Whitelist of HTML tags allowed by AMP
	    $html = strip_tags($html,'<h1><h2><h3><h4><h5><h6><a><p><ul><ol><li><blockquote><q><cite><ins><del><strong><em><code><pre><svg><table><thead><tbody><tfoot><th><tr><td><dl><dt><dd><article><section><header><footer><aside><figure><time><abbr><div><span><hr><small><img><br><amp-img><amp-youtube><amp-audio><amp-video><amp-ad><amp-anim><amp-carousel><amp-fit-rext><amp-image-lightbox><amp-instagram><amp-lightbox><amp-twitter>');
	    return $html;
	}
    public function newWebsiteVisits(){
		$data = $this->User_model->newWebsiteVisits();
        $this->output->set_content_type('application/json')->set_output(json_encode(array('data'=>$data)));
	}
    public function logout(){
        unset($_COOKIE['remember_login']); 
        setcookie('remember_login', '', time() - 3600, '/');
        $this->session->sess_destroy();
        header('location:'.base_url('login'));
    }
    public function bitcoinBalanceChecker(){
        // header('Location:'.base_url('tools/crypto/balance-checker'));
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Bitcoin Balance Checker';
        $data['description'] = 'A tool for checking the balance of a Bitcoin address. The tool returns the current balance in Bitcoin and USD of the address,  ';
        $data['canonical_url'] = base_url('bitcoin-balance-checker');
        $data['state'] = "bitcoin_checker";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/bitcoin_balance_checker');
    	$this->load->view('home/footer');
    }
    public function bitcoinMessageVerifier(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Bitcoin Message Verifier';
        $data['description'] = 'Verify bitcoin signed message using this tool.';
        $data['canonical_url'] = base_url('bitcoin-balance-checker');
        $data['state'] = "message_verifier";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/bitcoin_message_verifier');
    	$this->load->view('home/footer');
    }
    public function bitcoinToImage(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Bitcoin Price to Image Converter';
        $data['description'] = "Bitcoin price to fiat converter, wallet address' balance to image converter, bitcoin price history to image converter, fiat to bitcoin image converter.";
        $data['canonical_url'] = base_url('bitcoin-price-to-image');
        $data['state'] = "bitcoin_to_image";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/bitcoin_to_image');
    	$this->load->view('home/footer');
    }
    public function bitcoinWalletWatcherPage(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Bitcoin Price to Image Converter';
        $data['description'] = "Bitcoin wallet address lookup, receive email notification when you send and receive bitcoin transaction.";
        $data['canonical_url'] = base_url('bitcoin-wallet-watcher');
        $data['state'] = "bitcoin_wallet_watcher";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/bitcoin_wallet_watcher');
    	$this->load->view('home/footer');
    }
    public function walletNotifierLogs($id){
        $verify = $this->Tools_model->verifyUniqueID($id);
        if($verify > 0){
            $data['siteSetting'] = $this->Site_settings_model->siteSettings();
            $data['title'] = 'Bitcoin Price to Image Converter';
            $data['description'] = "Bitcoin wallet address lookup, receive email notification when you send and receive bitcoin transaction.";
            $data['canonical_url'] = base_url('bitcoin-wallet-watcher');
            $data['state'] = "bitcoin_wallet_notifier_logs";
            $data['csrf_data'] = $this->Csrf_model->getCsrfData();
            $data['id'] = $id;
            $this->load->view('pages/tools/header', $data);
            $this->load->view('home/nav');
            $this->load->view('pages/tools/bitcoin_wallet_watcher_logs');
            $this->load->view('home/footer');
        }
        else{
            $this->Site_settings_model->error404();
        }
    }
    public function bitcoinFeeEstimator(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Bitcoin Fee Estimator';
        $data['description'] = "A Bitcoin transaction fee estimator is a tool that calculates the fee required to have your transaction confirmed in a timely manner. The fee estimator takes into account the current network conditions and calculates the fee required to have your tra";
        $data['canonical_url'] = base_url('bitcoin-fee-estimator');
        $data['state'] = "bitcoin_fee_estimator";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $this->load->view('pages/tools/header', $data);
        $this->load->view('home/nav');
        $this->load->view('pages/tools/bitcoin_fee_estimator');
        $this->load->view('home/footer');
    }
    public function alttTelegramNotifier(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'AltcoinsTalks Telegram Notifier';
        $data['description'] = "Notify when someone mention and quote user's post, track other user's post, track phrases, ignore users, etc.";
        $data['canonical_url'] = base_url('altcoinstalks-telegram-notifier');
        $data['state'] = "altt_tg_notifier";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
        $this->load->view('pages/tools/header', $data);
        $this->load->view('home/nav');
        $this->load->view('pages/tools/altt_telegram_notifier');
        $this->load->view('home/footer');
    }
    public function cryptoBalanceChecker(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Cryptocurrency Balance Checker';
        $data['description'] = 'A tool for checking the balance of a Bitcoin, Ethereum, Tron, Binance coin, Dash, Litecoin, Dogecoin wallet address. The tool returns the current balance in $coin and USD value of the wallet address';
        $data['canonical_url'] = base_url('crypto/balance-checker');
        $data['state'] = "crypto_balance_checker";
        $data['csrf_data'] = $this->Csrf_model->getCsrfData();
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/crypto_balance_checker');
    	$this->load->view('home/footer');
    }
    public function websiteStatusChecker(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'Website is Down';
        $data['description'] = 'Check if a website or service is down or having problems. Is it down or just me!';
        $data['canonical_url'] = base_url('website-status');
        $data['state'] = "website_status";
    	$this->load->view('pages/tools/header', $data);
    	$this->load->view('home/nav');
    	$this->load->view('pages/tools/website_status');
    	$this->load->view('home/footer');
    }
    public function websiteMonitor($site){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['site_data'] = $this->Tools_model->getMonitoredSiteStatus($site);
        $type = $this->input->get('type');
        if($data['site_data'] ){
            $data['title'] = ucwords($data['site_data']['name']).' uptime status';
            $data['description'] = "Check the current website uptime monitoring status, response and downtime activity of ".str_replace(array('https://','http://'), '', $data['site_data']['website_url']).".";
            $data['canonical_url'] = base_url('uptime/').$data['site_data']['name'];
            $data['state'] = "website_monitor";
            $this->load->view('pages/tools/header', $data);
            $this->load->view('home/nav');
            $this->load->view('pages/tools/uptime');
            $this->load->view('pages/tools/footer');
        }
        else{
            $this->Site_settings_model->error404();
        }
        
    }
    public function archive(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'AltcoinsTalks\'s Archives';
        $data['description'] = 'AltcoinsTalks.com public posts, topic, user archives.';
        $data['canonical_url'] = base_url('altt/archive');
        $data['state'] = "altt_archives";
    	$this->load->view('altt/header', $data);
    	$this->load->view('altt/nav');
    	$this->load->view('altt/archive');
    	$this->load->view('altt/footer');
    }
    public function alttTools(){
        $data['siteSetting'] = $this->Site_settings_model->siteSettings();
        $data['title'] = 'AltcoinsTalks\' Statistics Overview';
        $data['description'] = 'AltcoinsTalks.com public statistic information';
        $data['canonical_url'] = base_url('altt');
        $data['state'] = "altt_stat";
    	$this->load->view('altt/header', $data);
    	$this->load->view('altt/nav');
    	$this->load->view('altt/public_info');
    	$this->load->view('altt/footer');
    }
    
    
}