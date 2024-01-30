        
		<div class="" id="_menu_backdrop"></div>
		
		<div id="loader" class="loader-div" hidden>
            <div class="loader-wrapper">
                <img src="<?=base_url('assets/images/other/loader.gif')?>" width="120" heigth="120">
            </div>
        </div>

		<div class="footer-div pb-4">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div class="cursor-pointer"> <!-- logo -->
		            	</div>
					</div>
					<div class="col-lg-6">
			            <ul class="no-list-style-inline ml-n-3 mt-2 font-20 footer-social-icon">
						</ul>
					</div>
					<div class="col-lg-6">
					</div>
					<div class="col-lg-3">
						<ul class="no-list-style ml-n-3 footer-text">
						</ul>
					</div>	

					<div class="col-lg-3">
						<ul class="no-list-style ml-n-3 footer-text">
						</ul>
					</div>	

				</div>
				<div class="text-center footer-text font-13 ">
					&copy; <?=date('Y')?>. <?=$siteSetting['website_name']?> All rights reserved.
				</div>
			</div>
		</div>
		<!-- bundle -->
		<script>
			var base_url = "<?=base_url();?>";
			var _state = <?=($state) ? '"'.$state.'"' : ''?>;
			<?php if ($state == 'bitcoin_wallet_notifier_logs') {?>var unique_id = "<?=$id;?>"<?php } ?>

		</script>
	    <script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_web_package.min.js"></script>
		<script src="<?=base_url()?>assets/js/_access.js?v=<?=filemtime('assets/js/_access.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_webapp.js?v=<?=filemtime('assets/js/_webapp.js')?>"></script>
		<script src="<?=base_url()?>assets/js/sweetalert2.all.min.js"></script>
		<script src="<?=base_url()?>assets/js/auth/_csrf.js?v=<?=filemtime('assets/js/auth/_csrf.js')?>"></script>
		
		<?php if ($state == 'login') {?><script src="<?=base_url()?>assets/js/auth/_login.js"></script>
		<script src="<?=base_url()?>assets/js/vendor/croppie.js"></script><?php } ?>

		<script src="<?=base_url()?>assets/js/auth/app.js?v=<?=filemtime('assets/js/auth/app.js')?>"></script>
		<?php if ($state == 'bitcoin_checker') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_balance.js"></script><?php } ?>

		<?php if ($state == 'message_verifier') {?>
		<?php } ?>

		<?php if ($state == 'bitcoin_wallet_watcher' || $state == 'bitcoin_wallet_notifier_logs') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_watcher.js?v=<?=filemtime('assets/js/auth/_bitcoin_watcher.js')?>"></script>
		<?php } ?>
		<?php if ($state == 'bitcoin_fee_estimator') {?><script src="<?=base_url()?>assets/js/auth/_bitcoin_fee_estimator.js?v=<?=filemtime('assets/js/auth/_bitcoin_fee_estimator.js')?>"></script>
		<?php } ?>

	</body>
</html>