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

					// Get notifications
					$notifications = User::ID()->getNotifications();
					$newNotifications = array_filter($notifications, function($value) { return $value['notification_read'] == 0; });
					$notificationsCount = count($newNotifications);

				?>
				<nav id="main-navigation">
					<ul class="user-header-menu">
						<li class="menu-item"><a href="<?=site_url('upgrade')?>">UPGRADE</a></li>
						<li class="menu-item notifications-wrapper">



							<a href="#" class="notification-opener" data-tooltip="<?=$notificationsCount == 0 ? "No new notifications" : "$notificationsCount New Notifications"?>">

								<i class="fa fa-bell"></i>
								<div class="notif-no <?=$notificationsCount == 0 ? "hide" : ""?>"><?=$notificationsCount?></div>

							</a>
							<ul class="notifications xl-left">

								<?php

								if (count($notifications) == 0) {

									echo "<li>There's nothing to mention now.</li>";

								}


								foreach ($notifications as $notification) {

									$sender_ID = $notification['sender_user_ID'];
									$senderInfo = getUserInfo($sender_ID);
								?>
									<li class="new">

										<div class="wrap xl-table xl-middle">
											<div class="col image">

												<picture class="profile-picture" <?=$senderInfo['printPicture']?>> 																							<span class="has-pic"><?=$senderInfo['nameAbbr']?></span>
												</picture>

											</div>
											<div class="col content">

												<?=$senderInfo['fullName']?> <?=$notification['notification']?><br/>
												<div class="date"><?= timeago($notification['notification_time'])?></div>

											</div>
										</div>

									</li>
								<?php
								}
								?>
							</ul>



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
						<li><a href="<?=site_url('account')?>"><i class="fa fa-user-cog"></i> My Account</a></li>
						<li><a href="<?=site_url('logout')?>"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
					</ul>
				</div>

				<?php
				}
				?>

			</div><!-- .menu-side -->

		</header>