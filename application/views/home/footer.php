        
		<div class="" id="_menu_backdrop"></div>
		
		<div id="loader" class="loader-div" hidden>
            <div class="loader-wrapper">
                <img src="<?=base_url('assets/images/other/loader.gif')?>" width="120" heigth="120">
            </div>
        </div>

		<div class="footer-div pb-4">
			<div class="container">
				<div class="row  footer-text font-14  pt-4">
					<div class="col-lg-6">
						<div class="cursor-pointer footer-details">
							<p>Show your support: <br>
								<a class="btn btn-lg bg-primary rounded c-white" href="<?=base_url('donate')?>">Donate</a>
							</p>
		            	</div>
					</div>
					
					<div class="col-lg-6">
						<ul class="no-list-style float-right footer-details">
							<li><i class="uil-github"></i> <a href="https://github.com/pxzone/pxzone.online" rel="noopener nofollow" target="_blank">Github</a></li>
							<li><i class="uil-bitcoin"></i> <a href="https://bitcointalk.org/index.php?action=profile;u=1000813;" rel="noopener nofollow" target="_blank">Bitcointalk</a></li>
							<li><i class="uil-yen-circle"></i> <a href="https://www.altcoinstalks.com/index.php?action=profile;u=97172;" rel="noopener nofollow" target="_blank">AltcoinsTalks</a></li>
						</ul>
					</div>	
				</div>
				<div class="footer-text pb-3 pt-3">
					<div class="text-center font-13 ">
						&copy; <?=date('Y', strtotime($siteSetting['created_at']))?>. <?=$siteSetting['website_name']?> All rights reserved.
					</div>
				</div>
			</div>
		</div>
		<!-- bundle -->
		<script>
			var base_url = "<?=base_url();?>";
			var _state = <?=($state) ? '"'.$state.'"' : ''?>;
			<?php if ($state == 'bitcoin_wallet_notifier_logs') {?>var unique_id = "<?=$id;?>"<?php } ?>

			<?php if ($state == 'crypto_balance_checker') {?>var wallet_address = "<?=isset($_GET['wallet_address']) ? $_GET['wallet_address'] : ''?>";
			var coin = "<?=isset($_GET['coin']) ? $_GET['coin'] : ''?>";<?php } ?>

		</script>
	    <script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_web_package.min.js"></script>
		<script src="<?=base_url()?>assets/js/_access.js?v=<?=filemtime('assets/js/_access.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_webapp.js?v=<?=filemtime('assets/js/_webapp.js')?>"></script>
		<script src="<?=base_url()?>assets/js/sweetalert2.all.min.js"></script>
		<script src="<?=base_url()?>assets/js/auth/_csrf.js?v=<?=filemtime('assets/js/auth/_csrf.js')?>"></script>
		<script src="<?=base_url()?>assets/js/auth/app.js?v=<?=filemtime('assets/js/auth/app.js')?>"></script>
		<?php if ($state == 'login') {?><script src="<?=base_url()?>assets/js/auth/_login.js"></script>
		<script src="<?=base_url()?>assets/js/vendor/croppie.js"></script>
		<?php } else if ($state == 'bitcoin_checker') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_balance.js"></script>
		<?php } else if ($state == 'message_verifier') {?>
		<?php } else if ($state == 'bitcoin_wallet_watcher' || $state == 'bitcoin_wallet_notifier_logs') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_watcher.js?v=<?=filemtime('assets/js/auth/_bitcoin_watcher.js')?>"></script>
		<?php } else if ($state == 'bitcoin_fee_estimator') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_fee_estimator.js?v=<?=filemtime('assets/js/auth/_bitcoin_fee_estimator.js')?>"></script>
		<?php } else if ($state == 'crypto_balance_checker' ) {?><script src="<?=base_url()?>assets/js/auth/_crypto.js?v=<?=filemtime('assets/js/auth/_crypto.js')?>"></script>
		<?php } ?>
		
		<script>
			<?php if ($state == 'crypto_balance_checker') {?>getWalletBalance(wallet_address, coin); <?php } ?>

		</script>
	</body>
</html>