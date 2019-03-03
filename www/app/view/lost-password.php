<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2>LOST PASSWORD</h2>

			<?php
				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="hidden" name="nonce" value="<?=$_SESSION['login_nonce']?>"/>
						<input type="email" class="large full" name="email" value="<?=$userName?>" size="20" id="user_login" placeholder="Usename or E-Mail Address…" />

						<div class="wrap">
							<div class="col xl-3-12">
								<a href="<?=site_url('login')?>" style="line-height: 90px;"><i class="fa fa-chevron-left"></i> Back to Sign In</a>
							</div>
							<div class="col xl-9-12 xl-right">
								<input type="submit" class="large" name="lost-password-submit" value="Send Reset Link" class="user-submit" />
							</div>
						</div>
						<input type="hidden" name="redirect_to" value="<?=htmlspecialchars_decode(get('redirect'))?>" />
					</div>
				</div>
			</form><br/>


			<div class="wrap register-button xl-hidden">
				<div class="col xl-11-12">
					<div class="xl-center button-desc">If you aren't registered yet</div>
					<a href="<?=site_url('signup'.( !empty(get('redirect')) ? "?redirect=".urlencode(get('redirect')) : "" ) )?>"><button class="large full">REGISTER FOR FREE</button></a>
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

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>