<?php require 'static/header_html.php' ?>
	<div id="page" class="site">

<section class="right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2>SIGN UP <div style="font-size: 32px;">TO MAKE THE WEB BETTER PLACE</div></h2>

			<form method="post" action="#">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="text" name="user_login" value="" size="20" placeholder="Username…" />
						<input type="text" name="user_email" value="" size="20" placeholder="E-Mail Address…" />
						<input type="text" name="user_name" value="" size="20" placeholder="First and Last Name…" />

						<div class="wrap xl-gutter-8">
							<div class="col xl-6-12 xl-right">
								<span class="register-message">Don't hesitate, it's <strong>free!</strong></span>
							</div>
							<div class="col xl-6-12">
								<input type="submit" name="user-submit" value="Register" class="user-submit full" />
							</div>
						</div>

						<input type="hidden" name="redirect_to" value="<?=site_url('projects')?>" />
						<input type="hidden" name="user-cookie" value="1" />
					</div>
				</div>
			</form><br/>


			<div class="wrap register-button">
				<div class="col xl-11-12">
					<div class="xl-center button-desc">If you are already registered</div>
				<a href="<?=site_url('login')?>"><button class="full">LOGIN</button></a>
				</div>
			</div>

		</div>

	</div>
	<div class="dark-side">

		<div class="side-content">
			<div class="wrap">
				<div class="col xl-1-12"></div>
				<div class="col xl-11-12 xl-center">
					<a href="<?=site_url()?>" rel="home"><h2><div>REVISIONARY APP</div></h2></a><br/>
					<img src="<?=asset_url('icons/icon-web-developer.svg')?>" alt=""/>
				</div>
			</div>
		</div>

	</div>
</section>

<?php require 'static/frontend/parts/footer_frontend.php' ?>
<?php require 'static/footer_html.php' ?>