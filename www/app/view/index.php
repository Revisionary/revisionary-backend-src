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

			<form action="<?=site_url('projects', true)?>" method="get" class="new-project-form">

				<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
				<input type="hidden" name="add_new" value="true"/>
				<input type="hidden" name="project_ID" value="autodetect"/>
				<input type="hidden" name="category" value="0"/>
				<input type="hidden" name="order" value="0"/>

				<input type="hidden" name="screens[]" value="11"/>
				<input type="hidden" name="page_width" value="1440"/>
				<input type="hidden" name="page_height" value="900"/>


				<input type="url" name="page-url" class="large full" placeholder="ENTER A WEBSITE URL" tabindex="1" required autofocust/>
				<input type="submit" title="Go Revising!"/>
			</form>
			<span class="description">Add your comments and edit any website’s content, <br/>
				for <del>a fee</del> free! <br/><br/>

				<a href="#" class="button-oval" data-tooltip='Coming soon...' data-modal="video"><i class="fa fa-play-circle"></i> See how it works</a>

			</span>
		</div>
	</div>


	<a href="#features" class="more-info">
		MORE INFO
		<img src="<?=asset_url('icons/icon-arrow-down.svg')?>" alt=""/>
	</a>

</main><!-- #first-section -->


<section id="features" class="features">
	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-2-3">
		
			<h2>Ease of Use, <br> No Download or Installation</h2>
			<p>Put your live URL and start giving feedback on any website! No need to download and install anything to your computer or browser.</p>
			<div><a href="<?=site_url('signup')?>" class="button">Get Started</a></div>

		</div>
		<div class="col xl-1-3 xl-right">LITTLE VIDEO LIKE GIF</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-1-3">LITTLE VIDEO LIKE GIF</div>
		<div class="col xl-2-3 xl-right">
		
			<h2>Change the Live Content & Style</h2>
			<p>You don't have to wait to see the changes you want on a website until developers apply them for you. Just do it yourself and see how does it look before you send them!</p>
			<div><a href="<?=site_url('signup')?>" class="button">Do it Yourself</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-2-3">
		
			<h2>See the Content Differences</h2>
			<p>Great to see the content changes on a live site. But what's changed on that big paragraph? <br> You can see the content differences easily to see what's exactly removed and added.</p>
			<div><a href="<?=site_url('signup?trial=Plus')?>" class="button">Try Now</a></div>

		</div>
		<div class="col xl-1-3 xl-right">LITTLE VIDEO LIKE GIF</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-1-3">LITTLE VIDEO LIKE GIF</div>
		<div class="col xl-2-3 xl-right">
		
			<h2>On any Device Screen</h2>
			<p>Found an issue on mobile view of a website? Or, on larger screens? Just add a new device to your page. So, you can do changes and add your comments on any device screen size.</p>
			<div><a href="<?=site_url('signup')?>" class="button">See in Action</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-2-3">
		
			<h2>Phases</h2>
			<p>When you are done with all the changes on a page and it's been live, add a new phase to start from the new version of that page. Pull the latest changes and start revising again!</p>
			<div><a href="<?=site_url('signup')?>" class="button">Work in Versions</a></div>

		</div>
		<div class="col xl-1-3 xl-right">LITTLE VIDEO LIKE GIF</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-1-3">LITTLE VIDEO LIKE GIF</div>
		<div class="col xl-2-3 xl-right">
		
			<h2>Comments on Design Files</h2>
			<p>You can use Revisionary even on your design process! Upload your design files and add your comments as easy as commenting on live URLs with Revisionary App.</p>
			<div><a href="<?=site_url('signup')?>" class="button">Give a Design Feedback</a></div>

		</div>
	</div>

	<div class="wrap xl-flexbox xl-middle xl-gutter-8 container">
		<div class="col xl-2-3">
		
			<h2>Integrations (Coming Soon)</h2>
			<p>You'll be able to automatically create GitHub or BitBucket Issues, Asana or Trello Tasks. <br> Just with one click of adding pins on any page!</p>
			<div><a href="#" class="button">Notify Me</a></div>
			<!-- !!! -->

		</div>
		<div class="col xl-1-3 xl-right">LITTLE VIDEO LIKE GIF</div>
	</div>
</section>


<section id="second-section" class="decorative right-dark">
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


<section class="decorative left-dark">
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


	// Update the current screen size !!! Common?
	$(window).resize(function() {

		var width = $(this).width();
		var height = $(this).height();

		screenWidth = width;
		screenHeight = height - 45 - 2; // -45 for the topbar, -2 for borders !!! ?

		//console.log(width, height);

		// Show new values
		$('.screen-width').text(screenWidth);
		$('.screen-height').text(screenHeight);

		// Edit the input values
		$('input[name="page_width"]').attr('value', screenWidth);
		$('input[name="page_height"]').attr('value', screenHeight);


		$('[data-screen-id="11"]').attr('data-screen-width', screenWidth);
		$('[data-screen-id="11"]').attr('data-screen-height', screenHeight);


		$('.new-screen[data-screen-id="11"]').each(function() {

			var newScreenURL = $(this).attr('href');
			var widthOnURL = getParameterByName('page_width', newScreenURL);
			var heightOnURL = getParameterByName('page_height', newScreenURL);

			var newURL = newScreenURL.replace('page_width='+widthOnURL, 'page_width='+screenWidth);
			newURL = newURL.replace('page_height='+heightOnURL, 'page_height='+screenHeight);

			$(this).attr('href', newURL);
			//console.log(newURL);

		});


	}).resize();


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