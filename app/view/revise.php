<?php require 'static/header_html.php' ?>

<div id="loading" class="overlay">
	<span><div class="gps_ring"></div> LOADING...</span>
</div>

<div id="pin-mode-selector" class="overlay" style="display: none;"></div>

<div id="page" class="site">
	<main>

		<div class="iframe-container">

			<iframe src="http://localhost/wordpress" width="100%" height="100%" scrolling="auto"></iframe>
<!-- 			<iframe src="https://www.twelve12.com" width="100%" height="100%" scrolling="auto"></iframe> -->

		</div>


		<div id="revise-sections">
			<div class="top-left pins">

				<div class="tab wrap open">
					<div class="col xl-1-1">

						<div class="pins-filter">
							<a href="#" class="selected">Show All</a> |
							<a href="#">Only Incompleted</a> |
							<a href="#">Only Completed</a>
						</div>

						<div class="scrollable-content">
							<div class="pins-list">

								<div class="pin standard incomplete">

									<a href="#" class="pin-locator">
										<pin class="mid" data-pin-mode="standard">1
											<div class="notif-no">2</div>
										</pin>
									</a>

									<a href="#" class="pin-title close">Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Ike Elimsa</a> <span class="comment-date">32 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12 xl-last xl-right" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap xl-flexbox user-info">
													<div class="col xl-8-12 xl-last xl-right comment-user-name">
														<span class="comment-date">29 minutes ago</span> <a href="#">Bilal TAS</a>
													</div>
													<div class="col xl-4-12 xl-left comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col xl-right">
														Alright, updated on the site!
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Matt Pasaoglu</a> <span class="comment-date">22 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur.
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="pin live incomplete">

									<a href="#" class="pin-locator">
										<pin class="mid" data-pin-mode="live">2
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Ike Elimsa</a> <span class="comment-date">32 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12 xl-last xl-right" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap xl-flexbox user-info">
													<div class="col xl-8-12 xl-last xl-right comment-user-name">
														<span class="comment-date">29 minutes ago</span> <a href="#">Bilal TAS</a>
													</div>
													<div class="col xl-4-12 xl-left comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col xl-right">
														Alright, updated on the site!
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Matt Pasaoglu</a> <span class="comment-date">22 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur.
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="pin live complete">

									<a href="#" class="pin-locator">
										<pin class="complete mid" data-pin-mode="live">3
											<!-- <div class="notif-no">1</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Ike Elimsa</a> <span class="comment-date">32 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12 xl-last xl-right" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap xl-flexbox user-info">
													<div class="col xl-8-12 xl-last xl-right comment-user-name">
														<span class="comment-date">29 minutes ago</span> <a href="#">Bilal TAS</a>
													</div>
													<div class="col xl-4-12 xl-left comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col xl-right">
														Alright, updated on the site!
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Matt Pasaoglu</a> <span class="comment-date">22 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur.
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="pin private incomplete">

									<a href="#" class="pin-locator">
										<pin class="mid" data-pin-mode="private">4
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Private Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Ike Elimsa</a> <span class="comment-date">32 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12 xl-last xl-right" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap xl-flexbox user-info">
													<div class="col xl-8-12 xl-last xl-right comment-user-name">
														<span class="comment-date">29 minutes ago</span> <a href="#">Bilal TAS</a>
													</div>
													<div class="col xl-4-12 xl-left comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col xl-right">
														Alright, updated on the site!
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Matt Pasaoglu</a> <span class="comment-date">22 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur.
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="pin private incomplete">

									<a href="#" class="pin-locator">
										<pin class="mid" data-pin-mode="private-live">5
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Private Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Ike Elimsa</a> <span class="comment-date">32 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12 xl-last xl-right" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap xl-flexbox user-info">
													<div class="col xl-8-12 xl-last xl-right comment-user-name">
														<span class="comment-date">29 minutes ago</span> <a href="#">Bilal TAS</a>
													</div>
													<div class="col xl-4-12 xl-left comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col xl-right">
														Alright, updated on the site!
													</div>
												</div>
											</div>
										</div>

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);">
													<div class="new-comment-icon">*</div>
												</picture>
											</a>
											<div class="col xl-10-12 comment-inner-wrapper">
												<div class="wrap user-info">
													<div class="col xl-8-12 comment-user-name">
														<a href="#">Matt Pasaoglu</a> <span class="comment-date">22 minutes ago</span>
													</div>
													<div class="col xl-4-12 xl-right comment-date"></div>
												</div>
												<div class="wrap xl-1 comment-text">
													<div class="col">
														Lorem ipsum dolor sit amet, consectetur.
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="pin live incomplete">

									<a href="#" class="pin-locator">
										<pin class="mid" data-pin-mode="live">6
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title">New Pin</a>
								</div>

							</div>
						</div>
					</div>
					<div class="opener">
						<a href="#">PINS</a>
					</div>
				</div>

			</div>


			<div class="top-right share xl-right">

				<div class="tab wrap open">
					<div class="col xl-1-1 xl-center">

						<div class="share-tab-content">

							<div class="pin-share">
								<a href="#">Share only specific Pins <i class="fa fa-question-circle" aria-hidden="true"></i></a>
								<div class="shared-members">
									<span class="people light-border">

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);"></picture>
										</a>

									</span>

									<a class="member-selector" href="#">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
								</div>
							</div>

							<div class="page-share">
								<a href="#">Share only this Page <i class="fa fa-question-circle" aria-hidden="true"></i></a>
								<div class="shared-members">
									<span class="people light-border">

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/joey.png')?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);"></picture>
										</a>

									</span>

									<a class="member-selector" href="#">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
								</div>
							</div>

							<div class="project-share">
								<a href="#">Share this Project  <i class="fa fa-question-circle" aria-hidden="true"></i></a>
								<div class="shared-members">
									<span class="people light-border">

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/bill.png')?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/ike.png')?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/sara.png')?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=asset_url('images/avatars/matt.png')?>);"></picture>
										</a>

									</span>

									<a class="member-selector" href="#">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
								</div>
							</div>

							<div class="link-share">
								<a href="#"><i class="fa fa-link" aria-hidden="true"></i> Get Shareable Link... <i class="fa fa-question-circle" aria-hidden="true"></i></a>
							</div>

						</div>
					</div>
					<div class="opener">
						<a href="#">SHARE</a>
					</div>
				</div>

			</div>


			<div class="bottom-left info">

				<div class="tab wrap open">
					<div class="col xl-8-12 xl-left">
						<div class="breadcrumbs">
							<a href="<?=site_url('project/twelve12')?>" class="projects">Twelve12 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
							<sep>></sep>
							<a href="<?=site_url('project/twelve12/#main-pages')?>" class="pages">Main Pages <i class="fa fa-caret-down" aria-hidden="true"></i></a>
							<sep>></sep>
							<a href="<?=site_url('revise/23423142')?>" class="sections">Home <i class="fa fa-caret-down" aria-hidden="true"></i></a>
						</div>
						<div class="date created">Date Created: <span>1 Jul 2016 6:32 PM</span></div>
						<div class="date updated">Last Updated: <span>2 Jul 2016 1:59 PM</span></div>
					</div>
					<div class="col xl-4-12 xl-center">
						<div class="device-selector">
							<a href="#" class="select-device">Device <i class="fa fa-caret-down" aria-hidden="true"></i></a>
							<div class="device-icon"><i class="fa fa-laptop" aria-hidden="true"></i></div>
						</div>
						<a href="#" class="version-selector">v0.1</a>
					</div>
					<div class="opener">
						<a href="#">INFO</a>
					</div>
				</div>


			</div>


			<div class="bottom-right help xl-right">

				<div class="tab wrap open">
					<div class="col xl-1-1">
						Help Content<br/><br/><br/>
					</div>
					<div class="opener" style="transform: skewX(-45deg);background-color: black;padding-right: 10px;right: 95%;">
						<a href="#" style="transform: skewX(45deg);font-size: 10px;padding-bottom: 0;padding-left: 3px;padding-right: 16px;">?</a>
					</div>
				</div>

			</div>
		</div>


		<div class="add-pin-options">

			<a href="#" class="inspect-activator">
				<pin class="add-new big" data-pin-mode="live">+</pin>
			</a>
			<a href="#" class="pin-mode-selector">
				<i class="fa fa-caret-down" aria-hidden="true"></i>
				<i class="fa fa-caret-up open-icon" aria-hidden="true"></i>
			</a>

			<div class="pin-modes">
				<a href="#" class="activate-live-mode">
					LIVE PREFERRED COMMENT <pin class="add-new" data-pin-mode="live">+</pin>
				</a>
				<a href="#" class="activate-standard-mode">
					ONLY COMMENT <pin class="add-new" data-pin-mode="standard">+</pin>
				</a>
				<a href="#" class="activate-private-live-mode">
					LIVEABLE PRIVATE COMMENT <pin class="add-new" data-pin-mode="private-live">+</pin>
				</a>
			</div>

		</div>


	</main> <!-- main -->
</div> <!-- #page.site -->

<?php require 'static/footer_html.php' ?>