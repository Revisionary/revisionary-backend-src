<div id="page" class="site">

	<div class="bg-overlay">

		<?php

			// Projects and Pages Modals
			if ($_url[0] == "projects" || $_url[0] == "project") require view('modules/modals');

		?>

	</div>

	<div class="progress-bar"></div>

	<main id="first-section">

		<header class="wrap xl-flexbox xl-middle xl-2 xl-outside-60">

			<div class="col branding-side xl-left">

				<?php if ( isset($_url[0]) && $_url[0] == "index" ) : ?>
					<h1 class="site-title"><a href="<?php echo site_url(); ?>" rel="home">REVISIONARY APP <span>ALPHA</span></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo site_url(); ?>" rel="home">REVISIONARY APP <span>ALPHA</span></a></p>
				<?php endif; ?>

			</div><!-- .branding-side -->

			<div class="col menu-side xl-right inline-guys">

				<?php
				if ( !userloggedIn() ) {
				?>

				<nav id="main-navigation">

					<ul>
						<li class="login-link"><a href="<?=site_url('login')?>">Login</a></li>
						<li class="signup-link"><a href="<?=site_url('signup')?>">Sign Up</a></li>
					</ul>

				</nav><!-- #main-navigation -->

				<?php
				} else {
				?>
				<nav id="main-navigation">
					<ul class="user-header-menu">
						<li class="menu-item"><a href="<?=site_url('upgrade')?>">UPGRADE</a></li>
					</ul>
				</nav><!-- #main-navigation -->


				<div class="dropdown-container">
					<a href="#" class="dropdown-opener invert-hover user-link bullet">
						<picture class="profile-picture big" <?=getUserData()['printPicture']?>>
							<span <?=getUserData()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserData()['nameAbbr']?></span>
							<div class="notif-no">3</div>
						</picture> <?=getUserData()['fullName']?>
					</a>

					<nav class="dropdown user-menu">
						<ul>
							<li class="notifications">
								There's nothing to mention now.<br/>
								Your notifications will be here.
							</li>
							<li><a href="<?=site_url('projects')?>">Projects</a></li>
							<li><a href="<?=site_url('profile/'.getUserData()['userName'])?>">Profile</a></li>
							<li><a href="<?=site_url('account')?>">Account</a></li>
							<li><a href="<?=site_url('logout')?>">Logout</a></li>
						</ul>
					</nav>
				</div>

				<?php
				}
				?>

			</div><!-- .menu-side -->

		</header>