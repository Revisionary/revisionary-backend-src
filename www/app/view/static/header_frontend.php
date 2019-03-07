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

					$notification_count = Notification::ID()->getCount();

				?>
				<nav id="main-navigation">
					<ul class="user-header-menu">
						<li class="menu-item"><a href="<?=site_url('upgrade')?>">UPGRADE</a></li>
						<li class="menu-item notifications-wrapper">



							<a href="#" class="notification-opener refresh-notifications" style="font-size: 20px;">

								<i class="fa fa-bell"></i>
								<div class="notif-no <?=$notification_count == 0 ? "hide" : ""?>"><?=$notification_count?></div>

							</a>
							<div class="notifications">
								<ul></ul>
							</div>



						</li>
					</ul>
				</nav><!-- #main-navigation -->


				<div class="dropdown">
					<a href="<?=site_url('projects')?>" class="invert-hover user-link bullet">
						<picture class="profile-picture big" <?=getUserInfo()['printPicture']?>>
							<span <?=getUserInfo()['userPic'] != "" ? "class='has-pic'" : ""?>><?=getUserInfo()['nameAbbr']?></span>
						</picture> <?=getUserInfo()['fullName']?>
					</a>
					<ul class="right user-menu xl-left">
						<li><a href="<?=site_url('projects')?>"><i class="fa fa-th"></i> All Projects</a></li>
						<li><a href="<?=site_url('account')?>" data-tooltip="In development..."><i class="fa fa-user-cog"></i> My Account</a></li>
						<li><a href="<?=site_url('logout')?>"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
					</ul>
				</div>

				<?php
				}
				?>

			</div><!-- .menu-side -->

		</header>