        
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
								<a class="btn btn-lg btn-primary rounded c-white" href="<?=base_url('donate')?>">Donate</a>
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

		<!-- <div id="_tos_privacy_consent" hidden="hidden">
		    This website uses cookies. By continuing to use this website you are giving consent to cookies being used. Visit our <a target="_blank" class="text-kwartz" rel="noopener" href="<?=base_url('terms')?>">Terms and Conditions</a> and <a target="_blank" rel="noopener" class="text-kwartz" href="<?=base_url('privacy')?>">Privacy Policy</a>. <button class="btn btn-kwartz c-white btn-agree-tos-privacy rounded" id="_agreed_tos_privacy">I Agree</button>
		</div> -->


		<script>
			var base_url = "<?=base_url();?>";
			var _state = <?=($state) ? '"'.$state.'"' : ''?>;
			<?= ($url_param !== '') ? "var _url_param = '".$url_param."';" : ''?>
			<?= ($state=='blog_category')?"var _category = '".$category."';" : ''?>				
			<?= ($state=='blog_tags')?"var _tags = '".$tags."';" : ''?>				
		</script>
	    <script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_web_package.min.js"></script>
		<script src="<?=base_url()?>assets/js/_webapp.js?v=<?=filemtime('assets/js/_webapp.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_access.js?v=<?=filemtime('assets/js/_access.js')?>"></script>
		<script src="<?=base_url()?>assets/js/auth/_article.js?v=<?=filemtime('assets/js/auth/_article.js')?>"></script>
		<?=($state=='blog_category' || $state =='blog_tags')?'<script src="'.base_url().'assets/js/auth/_blog_category.js?v=<?='.filemtime('assets/js/auth/_blog_category.js').'?>"></script>':''?>
	</body>
</html>