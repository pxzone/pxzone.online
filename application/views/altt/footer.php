        
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
		<script>
			var base_url = "<?=base_url()?>";
			var current_url = "<?=current_url();?>";
			<?php if (stripos(current_url(), "altt/karma-log") !== false ) { ?>

			var keyword = "<?=(isset($_GET['search'])) ? $_GET['search'] : ""?>";
			var sort = "<?=(isset($_GET['sort'])) ? $_GET['sort'] : "default"?>";<?php }?>

			<?php if (stripos(current_url(), "altt/karma-log") !== false && isset($_GET['sort']) && $_GET['sort'] == 'custom' && !empty($_GET['from']) && !empty($_GET['to'])) { ?>
			
			var from = "<?=$_GET['from']?>";
			var to = "<?=$_GET['to']?>";

			<?php } ?>

			<?php if (stripos(current_url(), "altt/archive") !== false ) { 
				
			if(isset($_GET['topic'])){
				$category = "topic";
				$search = $_GET['topic'];
			}
			else if(isset($_GET['post'])){
				$category = "post";
				$search = $_GET['post'];
			}
			else if(isset($_GET['username'])){
				$category = "username";
				$search = $_GET['username'];
			}
			else {
				$category = "";
				$search = "";
			}?>

			var keyword = "<?=$search?>";
			var category = "<?=$category?>";<?php } ?>

		</script>
	    <script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_web_package.min.js"></script>
		<script src="<?=base_url()?>assets/js/_access.js?v=<?=filemtime('assets/js/_access.js')?>"></script>
		<script src="<?=base_url()?>assets/js/_webapp.js?v=<?=filemtime('assets/js/_webapp.js')?>"></script>
		<?= (current_url() == base_url('altt')) ? '<script src="'.base_url().'assets/js/auth/_altt.js?v='.filemtime('assets/js/auth/_altt.js').'"></script>' : "" ?>
		
		<script src="<?=base_url()?>assets/js/auth/_altt_archive.js?v=<?=filemtime('assets/js/auth/_altt_archive.js')?>"></script>
		<script src="<?=base_url()?>assets/js/vendor/chart.js"></script>
		<script src="<?=base_url()?>assets/js/vendor/moment.min.js"></script>
		<script src="<?=base_url()?>assets/js/vendor/daterangepicker.min.js"></script>
		<script>
			<?php if (isset($_GET['search']) && stripos(current_url(), "altt/karma-log") !== false ) { ?>
			$("#search").val(keyword);
			fetchKarmaLogs(1, keyword, 'default');
			<?php } else if (stripos(current_url(), "altt/karma-log") !== false && isset($_GET['sort']) && $_GET['sort'] == 'custom' && !empty($_GET['from']) && !empty($_GET['to'])) { ?>
			
			$("#select_sort").val(sort)
			$('.custom-date').daterangepicker();
			from = "<?=$_GET['from']?>";
			to = "<?=$_GET['to']?>";
			fetchKarmaLogs(1, keyword, sort, from, to);

			<?php } else if (stripos(current_url(), "altt/karma-log") !== false) { ?>
				
			$('.custom-date').daterangepicker();
			fetchKarmaLogs(1, keyword, sort, '', '', 50);<?php }?>
			
			<?php if (stripos(current_url(), "altt/archive") !== false ) { ?>

			$("#keyword").val(keyword);
			searchArchives(1, keyword, category, 'asc');<?php }?>

		</script>
	</body>
</html>