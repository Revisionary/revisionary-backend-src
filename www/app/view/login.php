<?php require view('static/header_html'); ?>
	<div id="page" class="site">

<section class="right-dark">
	<div class="light-side">

		<div class="side-content">
			<h2>SIGN IN <div>TO CHANGE THE WEB</div></h2>

			<?php
				foreach ($errors as $error)
					echo $error."<br>";

				if (count($errors) > 0) echo "<br>";
			?>

			<form method="post" action="">
				<div class="wrap">
					<div class="col xl-11-12">
						<input type="hidden" name="nonce" value="<?=$_SESSION['login_nonce']?>"/>
						<input type="text" name="username" value="<?=$userName?>" size="20" id="user_login" placeholder="Usename or E-Mail Address…" />
						<input type="password" name="password" value="" size="20" id="user_pass" placeholder="Password…" />

						<div class="wrap">
							<div class="col xl-7-12">
								<a href="<?=site_url('lost-password')?>" style="line-height: 90px;">Forgot password?</a>
							</div>
							<div class="col xl-5-12 xl-right">
								<input type="submit" name="login-submit" value="Login" class="user-submit" />
							</div>
						</div>
						<input type="hidden" name="redirect_to" value="<?=urldecode(get('redirect'))?>" />
					</div>
				</div>
			</form><br/>


			<div class="wrap register-button">
				<div class="col xl-11-12">
					<div class="xl-center button-desc">If you aren't registered yet</div>
					<a href="<?=site_url('signup'.( !empty(get('redirect')) ? "?redirect=".get('redirect') : "" ) )?>"><button class="full">REGISTER FOR FREE</button></a>
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