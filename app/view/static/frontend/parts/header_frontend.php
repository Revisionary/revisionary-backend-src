<div id="page" class="site">

	<div class="bg-overlay">


		<div id="add-new" class="popup-window xl-center scrollable-content">
			<h2>Add New Project</h2>

			<form action="">



				<div class="wrap xl-center">
					<div class="col xl-5-7">


						<h4 class="xl-left xl-hidden">Project Info</h4>

						<h3>Site Name <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
						<input type="text" placeholder="e.g. Google, BBC, ..."/>


						<a href="#" class="more-options">More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>


						<div class="more-options-wrapper xl-hidden">


							<h3>Project Members <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
							<p>Members here!</p>


							<h4 class="xl-left">Page Info</h4>

							<h3>First Page URL <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
							<input type="text" placeholder="https://example.com/..."/>


							<h3>Page Name <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
							<input type="text" placeholder="e.g. Home, About, ..."/>


							<h3>Devices <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
							<p>Devices here!</p>


						</div>


					</div>
				</div>



				<!-- Actions -->
				<div class="wrap xl-center">
					<div class="col xl-3-8">


						<button class="cancel-button light small">Cancel</button>


					</div>
					<div class="col xl-3-8">


						<button class="dark small">Add</button>


					</div>
				</div>
				<br/>

			</form>


		</div>


	</div>

	<div class="progress-bar">
		<div class="progress">
			<div class="gradient"></div>
		</div>
	</div>

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
						<picture class="profile-picture big" <?=User::ID()->printPicture()?>>
							<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							<div class="notif-no">3</div>
						</picture> <?=User::ID()->fullName?>
					</a>

					<nav class="dropdown user-menu">
						<div class="notifications">
							There's nothing to mention now.<br/>
							Your notifications will be here.
						</div>

						<div class="user-menu-content">
							<ul class="user-menu-items">
								<li class="menu-item"><a href="<?=site_url('projects')?>">Projects</a></li>
								<li class="menu-item"><a href="<?=site_url('profile/'.User::ID()->userName)?>">Profile</a></li>
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