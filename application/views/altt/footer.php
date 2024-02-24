        
		<div class="" id="_menu_backdrop"></div>
		
		<div id="loader" class="loader-div" hidden>
            <div class="loader-wrapper">
                <img src="<?=base_url('assets/images/other/loader.gif')?>" width="120" heigth="120">
            </div>
        </div>

		<div class="footer-div pb-4">
			<div class="container">
				<div class="row  footer-text">
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
						<ul class="no-list-style ml-n-3">
						</ul>
					</div>	

					<div class="col-lg-3">
						<ul class="no-list-style ml-n-3 ">
						</ul>
					</div>	

				</div>
				<div class="footer-text pb-3">
					<div class="float-start font-13 ">
						&copy; <?=date('Y', strtotime($siteSetting['created_at']))?>. <?=$siteSetting['website_name']?> All rights reserved.
					</div>
					<div class="float-end  font-13 ">
						<i class="uil-github"></i> <a href="https://github.com/pxzone/pxzone.online" rel="noopener" target="_blank">PX Zone</a>
					</div>
				</div>
			</div>
		</div>
		<!-- bundle -->
		<script>
			var base_url = "<?=base_url();?>";
			var current_url = "<?=current_url();?>";
			var keyword = "<?=(isset($_GET['search'])) ? $_GET['search'] : ""?>";
		</script>
	    <script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_web_package.min.js"></script>
		<script src="<?=base_url()?>assets/js/_access.js?v=<?=filemtime('assets/js/_access.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_webapp.js?v=<?=filemtime('assets/js/_webapp.js')?>"></script>
		<script src="<?=base_url()?>assets/js/auth/_altt_archive.js?v=<?=filemtime('assets/js/auth/_altt_archive.js')?>"></script>
		<script>
			<?php if (isset($_GET['search']) && stripos(current_url(), "karma-log") !== false ) { ?>

			$("#search").val(keyword);
			fetchKarmaLogs(1, keyword, 'default');
			<?php } else if (stripos(current_url(), "karma-log") !== false) { ?>

			fetchKarmaLogs(1, keyword, 'default');
			<?php }?>

		</script>
	</body>
</html>