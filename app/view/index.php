<?php require 'static/header_html.php' ?>
<?php require 'static/frontend/parts/header_frontend.php' ?>

	<div class="vertical-middle">
		<div class="enter-url">
			<div class="home-questions">
				<span class="are-you">Are you</span>
				<ul>
					<li>a Content editor</li>
					<li>an SEO Manager</li>
					<li>a Web Designer</li>
					<li>a Web Developer</li>
					<li>a Website Customer</li>
					<li>a Translator</li>
					<li>a Web Critical</li>
				</ul>
			</div>

			<form action="" method="get">
				<input type="url" name="revise" placeholder="ENTER A WEBSITE URL"/>
				<input type="submit"/>
			</form>
			<span class="description">Add your comments and edit any website’s content, <br/>
	for <del>a fee</del> free!</span>
		</div>
	</div>


	<a href="#second-section" class="more-info">
		MORE INFO
		<img src="<?=asset_url('icons/icon-arrow-down.svg')?>" alt=""/>
	</a>

</main><!-- #first-section -->


<section id="second-section" class="right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2><div>REVISIONARY FOR </div>WEB DEVELOPERS</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sit amet mollis justo. Cras laoreet sed dolor nec hendrerit. Praesent non commodo tortor. In euismod volutpat urna sed commodo. Praesent pretium aliquet feugiat. Nunc molestie eros eget sem vehicula, ac malesuada leo pretium.</p>
			<a href="#" class="learn-more">LEARN MORE</a>
		</div>

	</div>
	<div class="dark-side">

		<div class="side-content">
			<img src="<?=asset_url('icons/icon-web-developer.svg')?>" alt=""/>
		</div>

	</div>
</section>


<section class="left-dark">
	<div class="dark-side">

		<div class="side-content">
			<img src="<?=asset_url('icons/icon-content-editor.svg')?>" alt=""/>
		</div>

	</div>
	<div class="light-side">

		<div class="side-content">
			<h2><div>REVISIONARY FOR </div>CONTENT EDITORS</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sit amet mollis justo. Cras laoreet sed dolor nec hendrerit. Praesent non commodo tortor. In euismod volutpat urna sed commodo. Praesent pretium aliquet feugiat. Nunc molestie eros eget sem vehicula, ac malesuada leo pretium.</p>
			<a href="#" class="learn-more">LEARN MORE</a>
		</div>

	</div>
</section>

<script>
jQuery(document).ready(function($) {


	$('.home-questions ul > li:first-child').addClass('active');

	var total_question = $('.home-questions ul > li').length;
	var question = 1;
	setInterval(function() {

		$('.home-questions ul > li').removeClass('active');
		$('.home-questions ul > li').eq(question%total_question).addClass('active');

		question++;

	}, 2500);

});
</script>

<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>