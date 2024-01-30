<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
# PAGE
$route['about'] = 'Page/about';
$route['privacy-terms'] = 'Page/terms';
$route['login'] = 'Page/login';
$route['logout'] = 'Page/logout';
$route['account'] = 'Page/dashboard';
$route['blog'] = 'Page/blog';
$route['tools'] = 'Page/tools';
$route['sitemap.xml'] = 'Sitemap/show';
$route['category/(:any)'] = 'Page/category/$1';
$route['tags/(:any)'] = 'Page/tags/$1';
$route['draft/(:any)'] = 'Page/draft/$1';
$route['stat/(:any)'] = 'Shortener/checkURLStat/$1';
$route['tools/bitcoin-balance-checker'] = 'Page/bitcoinBalanceChecker';
$route['tools/bitcoin-message-verifier'] = 'Page/bitcoinMessageVerifier';
$route['tools/bitcoin-price-to-image'] = 'Page/bitcoinToImage';
$route['tools/crypto/image/-to-image'] = 'Page/cryptoToImage';
$route['tools/bitcoin-wallet-notifier'] = 'Page/bitcoinWalletWatcherPage';
$route['tools/wallet-notifier-logs/(:any)'] = 'Page/walletNotifierLogs/$1';
$route['tools/bitcoin-fee-estimator'] = 'Page/bitcoinFeeEstimator';
$route['tools/altcoinstalks-telegram-notifier'] = 'Tools/alttTelegramNotifier';

# TOOLS 
$route['api/v1/bitcoin/_get_wallet_balance'] = 'Tools/getwalletBalance';
$route['api/v1/bitcoin/_save_wallet_watcher'] = 'Tools/saveBitcoinWalletWatcher';
$route['api/v1/logs/_get_logs'] = 'Tools/getNotifierLogs';
$route['api/v1/logs/_delete_record'] = 'Tools/deleteUniqueIDRecord';
$route['balance/(:any)'] = 'Tools/walletAddressBalanceToImage/$1';
$route['btc/price/(:any)'] = 'Tools/btcPriceToImage/$1';
$route['btc/history/(:any)'] = 'Tools/btcPriceHistoryToImage/$1';
$route['fiat/btc/(:any)'] = 'Tools/fiatBitcoinToImage/$1';
$route['bitcoin'] = 'Tools/bitcoinToolsStat';
$route['api/v1/bitcoin/_get_recommended_fees'] = 'Tools/bicoinFeeEstimate';
$route['api/crypto/price-to-img'] = 'Tools/cryptoPriceToImage';
$route['api/crypto/balance-checker'] = 'Tools/cryptoPriceToImage';

#ACCOUNT 
$route['account/dashboard'] = 'Page/dashboard';
$route['account/url-list'] = 'Page/urlList';
$route['account/blog'] = 'Page/blogList';
$route['account/blog/new'] = 'Page/newBlog';
$route['account/blog/edit/(:any)'] = 'Page/editBlog/$1';

#BLOG
$route['blog/edit/(:any)'] = 'Page/editBlog/$1';
$route['article/(:any)'] = 'Page/article/$1';
$route['api/v1/blog/_add_category'] = 'Blog/addCategory';
$route['api/v1/blog/_get_category'] = 'Blog/getCategory';
$route['api/v1/blog/_delete_category'] = 'Blog/deleteCategory';
$route['api/v1/blog/_add_blog'] = 'Blog/addBlog';
$route['api/v1/blog/_get_list'] = 'Blog/showBlogList';
$route['api/v1/blog/_update_article_status'] = 'Blog/updateArticleStatus';
$route['api/v1/blog/_update_blog'] = 'Blog/updateBlog';
$route['api/v1/blog/_remove_tag'] = 'Blog/removeTag';
$route['api/v1/blog/_add_tag'] = 'Blog/addTag';
$route['api/v1/blog/_update_image'] = 'Blog/updateImage';
$route['api/v1/blog/_delete_article'] = 'Blog/deleteArticle';
$route['api/v1/blog/_search_article'] = 'Blog/searchBlogArticle';
$route['api/v1/blog/_check_article'] = 'Blog/checkArticle';
$route['api/v1/blog/_add_image'] = 'Blog/uploadImage';

# IMAGES
$route['api/v1/images/_get_list'] = 'Blog/getImages';

# Article 
$route['api/v1/article/_get'] = 'Blog/getArticlesHomePageJS';
$route['api/v1/article/_get_data'] = 'Blog/getArticleDataJS';
$route['api/v1/article/_get_blog_category'] = 'Blog/getCategoryForPageJS';
$route['api/v1/article/_get_blog_tags'] = 'Blog/getArticleTagForPageJS';

# LOGIN
$route['api/v1/account/_login'] = 'Login/loginProcess';

#SCRAPPER
$route['api/scrapper/_set_time'] = 'Scrapper/setTimeRunning';
$route['api/scrapper/altcoinstalks'] = 'Scrapper/scrapeAlttForum';
$route['api/scrapper/altt/opt/(:num)'] = 'Scrapper/scrapeAlttForumOption2/$1';
$route['api/scrapper/edited/altcoinstalks/posts'] = 'Scrapper/scrapeAlttForumForEditedPosts';

#TELEGRAM
$route['api/telegram/bot/callback'] = 'Telegram_bot/callback';
$route['api/telegram/bot/_test_callback'] = 'Telegram_bot_test/callback'; // testing purpose
$route['api/telegram/users/_count'] = 'Telegram_bot/currentSubscribersToImg';

$route['telegram/scrapper'] = 'Telegram_bot/scrapper';
$route['api/telegram/bot/get-update'] = 'Telegram_bot/getUpdate';
$route['api/_telegram_register'] = 'Telegram_bot/registerTelegramData';
$route['api/_get_telegram_data'] = 'Telegram_bot/getTelegramData';
$route['api/_insert_telegram_msg'] = 'Telegram_bot/insertTelegramMsg';

#CRON
$route['api/v1/email/_bitcoin_wallet_watcher'] = 'Tools/bitcoinWalletWatcherNotifier';

$route['api/v1/xss/_get_csrf_data'] = 'App/getCsrfData';
$route['api/v1/_land'] = 'Page/newWebsiteVisits';

$route['default_controller'] = 'App/index';
$route['404_override'] = 'Error404';
$route['translate_uri_dashes'] = TRUE;
