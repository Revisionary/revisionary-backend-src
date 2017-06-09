<div id="page" class="site">

	<main id="first-section">

		<header class="wrap xl-flexbox xl-middle xl-2 xl-outside-60">

			<div class="col branding-side xl-left">

				<?php if ( $_url[0] == "index" ) : ?>
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
						<picture class="profile-picture big" style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);"><div class="notif-no">3</div></picture> Bilal TAS
					</a>

					<nav class="dropdown user-menu">
						<div class="notifications">
							There's nothing to mention now.<br/>
							Your notifications will be here.
						</div>

						<div class="user-menu-content">
							<ul class="user-menu-items">
								<li class="menu-item"><a href="<?=site_url('projects')?>">Projects</a></li>
								<li class="menu-item"><a href="<?=site_url('profile/bilaltas')?>">Profile</a></li>
								<li class="menu-item"><a href="<?=site_url('account')?>">Account</a></li>
								<li class="menu-item"><a href="<?=site_url('logout')?>">Logout</a></li>
							</ul>
						</div>

					</nav>
				</div>

				<?php
				}
				?>

			</div><!-- .menu-side -->

		</header>