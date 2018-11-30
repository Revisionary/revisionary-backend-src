<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

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
					<li>a Web Consultant</li>
				</ul>
			</div>

			<form action="<?=site_url('projects', true)?>" method="get">

				<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
				<input type="hidden" name="add_new" value="true"/>
				<input type="hidden" name="category" value="0"/>
				<input type="hidden" name="order" value="0"/>

				<input type="hidden" name="devices[]" value="11"/>
				<input type="hidden" name="page-width" value="1440"/>
				<input type="hidden" name="page-height" value="900"/>


				<input type="url" name="page-url" placeholder="ENTER A WEBSITE URL" tabindex="1" required autofocus/>
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


	// QUESTIONS LOOP
	$('.home-questions ul > li:first-child').addClass('active');

	var total_question = $('.home-questions ul > li').length;
	var question = 1;
	setInterval(function() {

		$('.home-questions ul > li').removeClass('active');
		$('.home-questions ul > li').eq(question%total_question).addClass('active');

		question++;

	}, 2500);


	// SMOOTH SCROLL ON ID CLICKS
	// Select all links with hashes
	$('a[href*="#"]')
		// Remove links that don't actually link to anything
		.not('[href="#"]')
		.not('[href="#0"]')
		.click(function(event) {
		// On-page links
		if (
		  location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
		  &&
		  location.hostname == this.hostname
		) {
		  // Figure out element to scroll to
		  var target = $(this.hash);
		  target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
		  // Does a scroll target exist?
		  if (target.length) {
		    // Only prevent default if animation is actually gonna happen
		    event.preventDefault();
		    $('html, body').animate({
		      scrollTop: target.offset().top
		    }, 1000, function() {
		      // Callback after animation
		      // Must change focus!
		      var $target = $(target);
		      $target.focus();
		      if ($target.is(":focus")) { // Checking if the target was focused
		        return false;
		      } else {
		        $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
		        $target.focus(); // Set focus again
		      };
		    });
		  }
		}
	});

});
</script>

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>