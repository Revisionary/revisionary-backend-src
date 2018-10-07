<?php require view('static/header_html'); ?>

<script>

	user_ID = '<?=currentUserID()?>';
	page_ID = '<?=$page_ID?>';
	element_index_nonce = '<?=$_SESSION["element_index_nonce"]?>';

	version_number = '<?=$version_number?>';
	version_ID = '<?=$version_ID?>';

	var pin_nonce = '<?=$_SESSION["pin_nonce"]?>';

</script>


<div class="progress-bar"></div>

<div id="loading" class="overlay">

	<div class="progress-info">
		<ul>
			<li style="color: white;"><?=Page::ID($page_ID)->cachedUrl?></li>
		</ul>
	</div>


	<span class="loading-text"><div class="gps_ring"></div> <span id="loading-info">LOADING...</span></span>
</div>

<div id="pin-type-selector" class="overlay" style="display: none;"></div>

<div id="page" class="site">

	<div class="iframe-container">

		<iframe id="the-page" name="the-page" src="" data-url="" width="<?=$width?>" height="<?=$height?>" scrolling="auto" style="min-width: <?=$width?>px; min-height: <?=$height?>px;"></iframe>

		<!-- THE PINS LIST -->
		<div id="pins"></div>

		<!-- THE PIN CURSOR -->
		<pin class="mouse-cursor big" data-pin-type="live">1</pin>

	</div>



	<div id="pin-window" class="loading"
		data-pin-id="0"
		data-pin-type="live"
		data-pin-edit-type="content"
		data-pin-private="0"
		data-pin-complete="0"
		data-pin-x="30"
		data-pin-y="30"
		data-pin-modification-type=""
		data-revisionary-edited="0"
		data-revisionary-showing-changes="0"
		data-revisionary-index="0"
		style="left: 10px; top: 10px;"
	>

		<div class="wrap xl-flexbox xl-between top-actions">
			<div class="col">

				<div class="wrap xl-flexbox actions">
					<div class="col action dropdown-container">

						<pin
							class="chosen-pin"
							data-pin-type="live"
							data-pin-private="0"
						></pin>
						<span class="dropdown-opener"><span>Live Edit</span> <i class="fa fa-caret-down" aria-hidden="true"></i></span>

						<nav class="dropdown xl-left">
							<ul class="type-convertor">

								<li>
									<a href="#" class="xl-flexbox xl-middle">
										<pin data-pin-type="live" data-pin-private="0"></pin>
										<span>Live</span>
									</a>
								</li>

								<li>
									<a href="#" class="xl-flexbox xl-middle">
										<pin data-pin-type="standard" data-pin-private="0"></pin>
										<span>Only Comment</span>
									</a>
								</li>

								<li>
									<a href="#" class="xl-flexbox xl-middle">
										<pin data-pin-type="live" data-pin-private="1"></pin>
										<span>Private</span>
									</a>
								</li>

							</ul>
						</nav>

					</div>
					<div class="col action" data-tooltip="Coming soon.">

						<i class="fa fa-user-o" aria-hidden="true"></i>
						<span>ASSIGNEE</span>

					</div>
				</div>

			</div>
			<div class="col">
				<a href="#" class="close-button"><i class="fa fa-check"></i></a>
			</div>
		</div>

		<div class="image-editor">

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
				<div class="col title">NEW IMAGE:</div>
				<div class="col">

					<a href="#" class="switch edits-switch original">
						<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
						SHOW ORIGINAL
					</a>

				</div>
			</div>

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
				<div class="col">ORIGINAL CONTENT:</div>
				<div class="col edits-switch-wrap">

					<a href="#" class="switch edits-switch changes">
						<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
						SHOW CHANGED
					</a>

				</div>
			</div>

			<div class="wrap xl-1">
				<div class="col">
					<div class="edit-content changes uploader">

					    <span><u>Click here</u> or drag here your image for preview</span>
					    <img src=""/>
					    <input type="file" name="image" id="filePhoto" />

					</div>
					<div class="edit-content original">
						TEST
						ORIGINAL
						CONTENT
					</div>
				</div>
			</div>
		</div>

		<div class="content-editor">

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
				<div class="col title">EDIT CONTENT:</div>
				<div class="col">

					<a href="#" class="switch edits-switch original">
						<img src="<?=asset_url('icons/edits-switch-off.svg')?>" alt=""/>
						SHOW ORIGINAL
					</a>

				</div>
			</div>

			<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
				<div class="col">ORIGINAL CONTENT:</div>
				<div class="col edits-switch-wrap">

					<a href="#" class="switch edits-switch changes">
						<img src="<?=asset_url('icons/edits-switch-on.svg')?>" alt=""/>
						SHOW CHANGED
					</a>

				</div>
			</div>

			<div class="wrap xl-1">
				<div class="col">
					<div class="edit-content changes" contenteditable="true">
						TEST
						CONTENT
					</div>
					<div class="edit-content original">
						TEST
						ORIGINAL
						CONTENT
					</div>
				</div>
			</div>

			<div class="wrap xl-1 xl-right difference-switch-wrap">
				<a href="#" class="col switch difference-switch" data-tooltip="Coming soon.">
					<i class="fa fa-random" aria-hidden="true"></i> SHOW DIFFERENCE
				</a>
			</div>

		</div>

		<div class="pin-comments" style="margin-bottom: 15px;"></div>

		<div class="comment-actions">

			<form action="" method="post" id="comment-sender">
				<div class="wrap xl-flexbox xl-between">
					<div class="col comment-input-col">
						<input type="text" class="comment-input" placeholder="Your comments..." required/>
					</div>
					<div class="col">
						<input type="image" src="<?=asset_url('icons/comment-send.svg')?>"/>
					</div>
				</div>
			</form>

		</div>

		<div class="bottom-actions">

			<div class="wrap xl-flexbox xl-between">
				<div class="col action dropdown-container">
					<a href="#" class="dropdown-opener">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i> MARK <i class="fa fa-caret-down" aria-hidden="true"></i>
					</a>
					<nav class="dropdown">
						<ul>
							<li>
								<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon.">
									<span><img src="<?=asset_url('icons/mark-rectangle.png')?>" width="15" height="10" alt=""/></span>
									RECTANGLE
								</a>
							</li>
							<li>
								<a href="#" class="xl-left" data-tooltip="Coming soon.">
									<span><img src="<?=asset_url('icons/mark-ellipse.png')?>" width="15" height="14" alt=""/></span>
									ELLIPSE
								</a>
							</li>
						</ul>
					</nav>
				</div>
				<div class="col action">
					<a href="#" class="remove-pin"><i class="fa fa-trash-o" aria-hidden="true"></i> REMOVE</a>
				</div>
				<div class="col action pin-complete">
					<a href="#" class="complete-pin">
						<pin
							data-pin-type="standard"
							data-pin-private="0"
							data-pin-complete="1"
						></pin>
						DONE
					</a>
					<a href="#" class="incomplete-pin">
						<pin
							data-pin-type="standard"
							data-pin-private="0"
							data-pin-complete="0"
						></pin>
						INCOMPLETE
					</a>
				</div>
			</div>

		</div>

	</div>

	<main>

		<div id="revise-sections">
			<div class="top-left pins open">

				<div class="tab wrap">
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
										<pin class="mid" data-pin-type="standard">1
											<div class="notif-no">2</div>
										</pin>
									</a>

									<a href="#" class="pin-title close">Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
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
													style="background-image: url(<?=User::ID(1)->userPicUrl?>);">
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
													style="background-image: url(<?=User::ID(4)->userPicUrl?>);">
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
										<pin class="mid" data-pin-type="live">2
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
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
													style="background-image: url(<?=User::ID(1)->userPicUrl?>);">
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
													style="background-image: url(<?=User::ID(4)->userPicUrl?>);">
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
										<pin class="complete mid" data-pin-type="live" data-pin-complete="1">3
											<!-- <div class="notif-no">1</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
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
													style="background-image: url(<?=User::ID(1)->userPicUrl?>);">
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
													style="background-image: url(<?=User::ID(4)->userPicUrl?>);">
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
										<pin class="mid" data-pin-type="standard" data-pin-private="1">4
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Private Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
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
													style="background-image: url(<?=User::ID(1)->userPicUrl?>);">
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
													style="background-image: url(<?=User::ID(4)->userPicUrl?>);">
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
										<pin class="mid" data-pin-type="live" data-pin-private="1">5
											<!-- <div class="notif-no">2</div> -->
										</pin>
									</a>

									<a href="#" class="pin-title close">Private Live Edit and Comment Pin <i class="fa fa-caret-up" aria-hidden="true"></i></a>
									<div class="pin-comments">

										<div class="comment wrap xl-flexbox xl-top">
											<a class="col xl-2-12" href="#">
												<picture class="profile-picture big square"
													style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
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
													style="background-image: url(<?=User::ID(1)->userPicUrl?>);">
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
													style="background-image: url(<?=User::ID(4)->userPicUrl?>);">
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
										<pin class="mid" data-pin-type="live">6
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


			<div class="top-right share xl-right open">

				<div class="tab wrap">
					<div class="col xl-1-1 xl-center">

						<div class="share-tab-content">

							<div class="pin-share">
								<a href="#">Share only specific Pins <i class="fa fa-question-circle" aria-hidden="true"></i></a>
								<div class="shared-members">
									<span class="people light-border">

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=User::ID(1)->userPicUrl?>);"></picture>
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
											<picture class="profile-picture" style="background-image: url(<?=User::ID(5)->userPicUrl?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=User::ID(4)->userPicUrl?>);"></picture>
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
											<picture class="profile-picture" style="background-image: url(<?=User::ID(1)->userPicUrl?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=User::ID(2)->userPicUrl?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=User::ID(3)->userPicUrl?>);"></picture>
										</a>

										<a href="#">
											<picture class="profile-picture" style="background-image: url(<?=User::ID(4)->userPicUrl?>);"></picture>
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

							<div class="link-share">
								<a href="#"><i class="fa fa-link" aria-hidden="true"></i> Share on my public profile... <i class="fa fa-question-circle" aria-hidden="true"></i></a>
							</div>

						</div>
					</div>
					<div class="opener">
						<a href="#">SHARE</a>
					</div>
				</div>

			</div>


			<div class="bottom-left info open">

				<div class="tab wrap">
					<div class="col xl-8-12 xl-left">


						<div class="breadcrumbs">
							<a href="<?=site_url('project/'.$project_ID)?>" class="projects">
								<?=Project::ID($project_ID)->getProjectInfo('project_name')?> <i class="fa fa-caret-down" aria-hidden="true"></i>
							</a>
							<sep>></sep>

							<?php
							if ($pageCat['cat_name'] != "") {
							?>
							<a href="<?=site_url('project/'.$project_ID.'/'.permalink($pageCat['cat_name']))?>" class="pages">
								<?=$pageCat['cat_name']?> <i class="fa fa-caret-down" aria-hidden="true"></i>
							</a>
							<sep>></sep>
							<?php
							}
							?>

							<a href="<?=site_url('project/'.$project_ID)?>" class="sections">
								<?=Page::ID($page_ID)->getPageInfo('page_name')?> <i class="fa fa-caret-down" aria-hidden="true"></i>
							</a>
						</div>


						<div class="date created">Date Created: <span><?=timeago(Page::ID($page_ID)->getPageInfo('page_created') )?></span></div>
						<div class="date updated">Last Updated: <span><?=timeago(Page::ID($page_ID)->getPageInfo('page_modified') )?></span></div>


					</div>
					<div class="col xl-4-12 xl-center">


						<div class="device-selector">

							<span class="dropdown-container">

								<a href="#" class="dropdown-opener select-device">
									Device <i class="fa fa-caret-down" aria-hidden="true"></i>
								</a>
								<?php
/*
									echo $parentpage_ID;
									die();
*/

								if ( $parentpage_ID == null ) $parentpage_ID = $page_ID;

								// SUB PAGES QUERY

								// Check if other devices available
								$db->where("parent_page_ID", $parentpage_ID);
								$db->orWhere("page_ID", $parentpage_ID);

								// Bring the archive info
								$db->join("archives arc", "arc.archived_object_ID = p.page_ID", "LEFT");
								$db->joinWhere("archives arc", "arc.archiver_user_ID", currentUserID());
								$db->joinWhere("archives arc", "arc.archive_type", "page");

								// Bring the delete info
								$db->join("deletes del", "del.deleted_object_ID = p.page_ID", "LEFT");
								$db->joinWhere("deletes del", "del.deleter_user_ID", currentUserID());
								$db->joinWhere("deletes del", "del.delete_type", "page");

								// Exclude deleted and archived
								$db->where('del.deleted_object_ID IS NULL');
								$db->where('arc.archived_object_ID IS NULL');

								// Bring the devices
								$db->join("devices d", "d.device_ID = p.device_ID", "LEFT");

								// Bring the device category info
								$db->join("device_categories d_cat", "d.device_cat_ID = d_cat.device_cat_ID", "LEFT");

								// Order by IDs
								$db->orderBy('d.device_ID', 'asc');

								$existing_devices = $db->get('pages p');

								?>
								<nav class="dropdown">
									<ul class="xl-left">
									<?php
									foreach ($existing_devices as $device) {
										if ($device['page_ID'] == $page_ID) continue;
									?>

										<li>
											<a href="<?=site_url('revise/'.$device['page_ID'])?>"><i class="fa <?=$device['device_cat_icon']?>" aria-hidden="true"></i> <?=$device['device_cat_name']?> (<?=$device['device_width']?>x<?=$device['device_height']?>)</a>
										</li>

									<?php
									}
									?>
										<li class="dropdown-container">
											<a href="#" class="dropdown-opener add-device">Add New <i class="fa fa-caret-right" aria-hidden="true"></i></a>
											<nav class="dropdown xl-left">
												<ul class="device-adder">
													<?php
													$db->orderBy('device_cat_order', 'asc');
													$device_cats = $db->get('device_categories');
													foreach ($device_cats as $device_cat) {
													?>

													<li>

														<div class="dropdown-container">
															<div class="dropdown-opener">
																<i class="fa <?=$device_cat['device_cat_icon']?>" aria-hidden="true"></i> <?=$device_cat['device_cat_name']?> <i class="fa fa-caret-right" aria-hidden="true"></i>
															</div>
															<nav class="dropdown selectable addable xl-left">
																<ul class="device-addd">
																	<?php
																	$db->where('device_cat_ID', $device_cat['device_cat_ID']);
																	$db->where('device_user_ID', 1);
																	$db->orderBy('device_order', 'asc');
																	$devices = $db->get('devices');
																	foreach ($devices as $device) {
																	?>
																	<li>
																		<a href="<?=site_url("project/$project_ID?new_device=".$device['device_ID']."&page_ID=$parentpage_ID&nonce=".$_SESSION["new_device_nonce"])?>"
																			data-device-id="<?=$device['device_ID']?>"
																			data-device-width="<?=$device['device_width']?>"
																			data-device-height="<?=$device['device_height']?>"
																			data-device-cat-name="<?=$device_cat['device_cat_name']?>"
																			data-device-cat-icon="<?=$device_cat['device_cat_icon']?>"
																		>
																			<?=$device['device_name']?> (<?=$device['device_width']?>x<?=$device['device_height']?>)
																		</a>
																	</li>
																	<?php
																	}

																	// Custom Device
																	if ($device_cat['device_cat_name'] == "Custom...") {
																	?>
																	<li><a href="#" data-device-id="<?=$device['device_ID']?>">Add New</a></li>
																	<?php
																	}
																	?>
																</ul>
															</nav>

														</div>

													</li>

													<?php
													}
													?>
												</ul>
											</nav>
										</li>
									</ul>
								</nav>
							</span>

							<div class="device-icon bottom-tooltip" data-tooltip="<?=$device_name?>"><i class="fa <?=$deviceIcon?>" aria-hidden="true"></i></div>
						</div>
						<a href="#" class="version-selector"><?=Page::ID($page_ID)->pageVersion?></a>


					</div>
					<div class="opener">
						<a href="#">INFO</a>
					</div>
				</div>


			</div>


			<div class="bottom-right help xl-right open">

				<div class="tab wrap">
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
				<pin class="add-new big" data-pin-type="live">+</pin>
			</a>
			<a href="#" class="pin-type-selector">
				<i class="fa fa-caret-down" aria-hidden="true"></i>
				<i class="fa fa-caret-up open-icon" aria-hidden="true"></i>
			</a>

			<div class="pin-types">
				<a href="#" class="activate-live-mode">
					LIVE PREFERRED COMMENT <pin class="add-new" data-pin-type="live" data-pin-private="0">+</pin>
				</a>
				<a href="#" class="activate-standard-mode">
					ONLY COMMENT <pin class="add-new" data-pin-type="standard" data-pin-private="0">+</pin>
				</a>
				<a href="#" class="activate-private-live-mode">
					LIVEABLE PRIVATE COMMENT <pin class="add-new" data-pin-type="live" data-pin-private="1">+</pin>
				</a>
			</div>

		</div>

	</main> <!-- main -->
</div> <!-- #page.site -->

<script>
// When page is ready
$(function(){

	var loadingProcessID = newProcess();


	// Post the request to AJAX
	var processInterval = setInterval(function(){

		// Send the new data with process ID
		$.post(ajax_url, {
			'type':'internalize-status',
			'page_ID': <?=$_url[1]?>,
			'queue_ID': <?=is_numeric($queue_ID) ? $queue_ID : "''"?>,
			'processID' : <?=is_numeric($process_ID) ? $process_ID : "''"?>
		}, function(result){


			$.each(result.data, function(key, data){

				// Append the log !!!
				if (key != "final")	console.log(key, data);


				// Update the proggress bar
				var width = data.processPercentage;


				editProcess(loadingProcessID, width);


				if (width == 100)
					endProcess(loadingProcessID);


				// Print the current status
				$('#loading-info').text( Math.round(width) + '% ' + data.processDescription + '...');


				// Don't repeat checking when done
				if (data.status == "not-running") {
					clearInterval(processInterval);
					if (width != 100) $('#loading-info').text( 'Error');
				}


				// If successfully downloaded
				if (data.processStatus == "ready") {

					// Update the global page URL
					page_URL = data.pageUrl + '?v=' + data.internalized;


					// Update the iframe url
					$('iframe').attr('src', page_URL);


					// Run the inspector
					runTheInspector();

				}



			});

		}, 'json');


	}, 500); // Interval

});
</script>

<?php require view('static/footer_html'); ?>