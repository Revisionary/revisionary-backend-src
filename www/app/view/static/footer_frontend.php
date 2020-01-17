	<footer>
		<div class="wrap xl-2 container xl-flexbox xl-middle">
			<div class="col">
				<div class="site-title"><a href="<?=site_url()?>" rel="home">REVISIONARY APP</a></div>
				<div class="copyright">Copyright &copy; <?=date('Y')?></div>
			</div>
			<div class="col xl-right footer-links">
				<nav style="margin-bottom: 10px;">
					<a href="/help/get-started">Get Started</a> | 
					<a href="/use-cases">Use Cases</a> | 
					<a href="/pricing">Pricing</a> | 
					<a href="/contact">Contact</a>
				</nav>
				<div class="social">

					<a href="https://www.linkedin.com/company/revisionaryapp/" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
					<a href="https://twitter.com/RevisionaryApp" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
					<a href="https://www.facebook.com/RevisionaryApp/" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
					<a href="https://www.instagram.com/AppRevisionary/" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>
					<a href="https://www.youtube.com/channel/UCb_wJjNMOthbCeawrSkIA9w" target="_blank"><i class="fa fa-youtube-square" aria-hidden="true"></i></a>

				</div>
			</div>
		</div>
	</footer><!-- .site-footer -->


	<div class="bg-overlay modals">

		<?php

			// Common Video Modal
			require view('modules/video-modal');

			// Projects and Pages Modals
			if ( userLoggedIn() ) require view('modules/modals');

		?>

	</div>


</div><!-- #page.site -->