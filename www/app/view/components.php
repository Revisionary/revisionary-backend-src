<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<h1 class="xl-center"><?=$page_title?></h1>


		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-40 xl-table">
			<div class="col">

				<b>Logo </b><br/><br/>
				<img src="<?=asset_url("images/revisionary-logo.png")?>" alt=""/>

			</div>
			<div class="col">

				<b>Icon </b><br/><br/>
				<img src="<?=asset_url("images/revisionary-icon.png")?>" alt=""/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Titles </b><br/><br/>
		<h1>Heading 1</h1>
		<h2 style="font-size: 40px;">Heading 2</h2>
		<h3>Heading 3</h3>
		<h4>Heading 4</h4>
		<h5>Heading 5</h5>
		<h6>Heading 6</h6>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Paragraph</b> <br/>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>


		<br/>
		<br/>
		<br/>
		<br/>

		<b>Dropdowns </b><br/><br/>
		<div class="wrap xl-gutter-40">
			<div class="col">


				<span class="dropdown">
					<a href="#" class="learn-more">Dropdown on Link <i class="fa fa-caret-down" aria-hidden="true"></i></a>
					<ul>
						<li><span>No Link Item</span></li>
						<li><a href="#">Linked Item</a></li>
						<li class="selected"><a href="#">Selected Item</a></li>
						<li><a href="#"><i class="fa fa-user"></i> Item with Icon</a></li>
						<li class="selected"><a href="#"><i class="fa fa-user"></i> Selected Iconic Item</a></li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Alt Dropdown Item <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul>
								<li><span>Sub Item 1</span></li>
								<li>
									<a href="#"><i class="fa fa-sign-in-alt"></i> Sub Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
									<ul>
										<li><span>Subber Item 1</span></li>
										<li>

											<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
											<ul>
												<li><span>Subber Item 1</span></li>
												<li>
													<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2 <i class="fa fa-caret-right" aria-hidden="true"></i></a>
													<ul>
														<li><span>Subber Item 1</span></li>
														<li><span>Subber Item 2</span></li>
														<li><span>Subber Item 3</span></li>
													</ul>
												</li>
												<li><span>Subber Item 3</span></li>
											</ul>

										</li>
										<li><span>Subber Item 3</span></li>
									</ul>
								</li>
								<li><a href="#"><i class="fa fa-sign-in-alt"></i> Sub Item 3</a></li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Selectables <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul class="selectable">
								<li><a href="#">Selectable 1</a></li>
								<li class="selected"><a href="#">Selectable 2</a></li>
								<li><a href="#">Selectable 3</a></li>
								<li><a href="#">Selectable 4</a></li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Addables <i class="fa fa-caret-right" aria-hidden="true"></i></a>
							<ul class="addable">
								<li><a href="#">Addable 1</a></li>
								<li><a href="#">Addable 2</a></li>
								<li><a href="#">Addable 3</a></li>
								<li><a href="#">Addable 4</a></li>
							</ul>
						</li>
					</ul>
				</span>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Text Inputs </b><br/><br/>
		<div class="wrap xl-gutter-24">
			<div class="col">

				<small>Normal</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Hover</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Focus</small><br/>
				<input type="text" class="large" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Hover</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

				<small>Focus</small><br/>
				<input type="text" placeholder="Text Box" style="display: inline-block;"/><br/><br/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>


		<b>Buttons </b><br/><br/>
		<div class="wrap xl-gutter-24">
			<div class="col">

				<small>Normal</small><br/>
				<button class="large"><a href="#">Large Button</a></button><br/><br/>

				<small>Hover</small><br/>
				<button class="large"><a href="#">Large Button</a></button><br/><br/>

				<small>Focus</small><br/>
				<button class="large"><a href="#">Large Button</a></button><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<button><a href="#">Small Button</a></button><br/><br/>

				<small>Hover</small><br/>
				<button><a href="#">Small Button</a></button><br/><br/>

				<small>Focus</small><br/>
				<button><a href="#">Small Button</a></button><br/><br/>

			</div>
			<div class="col">

				<small>Normal</small><br/>
				<a href="#" class="button-oval"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

				<small>Hover</small><br/>
				<a href="#" class="button-oval"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

				<small>Focus</small><br/>
				<a href="#" class="button-oval"><i class="fa fa-play-circle"></i> Oval Button with Icon</a><br/><br/>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>

		<b>Forms </b><br/><br/>

		<div class="wrap xl-gutter-40">
			<div class="col">


				<input class="large full" type="text" placeholder="Form Input 1"/>
				<input class="large full" type="text" placeholder="Form Input 2"/>
				<button class="large full"><a href="#">Form Group</a></button>


			</div>
			<div class="col">


				<input type="text" placeholder="Form Input 1"/>
				<input type="text" placeholder="Form Input 2"/>
				<button><a href="#">Form Group</a></button>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-40">
			<div class="col xl-center">

				<b class="left-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Left</b>

			</div>
			<div class="col xl-center">

				<b class="bottom-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Bottom</b>

			</div>
			<div class="col xl-center">

				<b class="top-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Top</b>

			</div>
			<div class="col xl-center">

				<b class="right-tooltip" data-tooltip="Tooltip Message">Hover Tooltip Right</b>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Profile Picture With Image <br/><br/>
				<picture class="profile-picture larger" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture big" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture>


			</div>
			<div class="col xl-center">


				Profile Picture Without Image <br/><br/>
				<picture class="profile-picture larger" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>


				<picture class="profile-picture big" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture" data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Standard Complete Pin <br/><br/>
				<pin class="small" data-pin-type="standard" data-pin-complete="1">1</pin> <br/><br/>
				<pin class="mid" data-pin-type="standard" data-pin-complete="1">1</pin> <br/><br/>
				<pin class="big" data-pin-type="standard" data-pin-complete="1">1</pin> <br/><br/><br/>

				Standard Pin <br/><br/>
				<pin class="big" data-pin-type="standard">1</pin> <br/><br/>
				<pin class="mid" data-pin-type="standard">1</pin> <br/><br/>
				<pin class="small" data-pin-type="standard">1</pin>

			</div>
			<div class="col xl-center">

				Standard Private Complete Pin <br/><br/>
				<pin class="small" data-pin-type="standard" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/>
				<pin class="mid" data-pin-type="standard" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/>
				<pin class="big" data-pin-type="standard" data-pin-private="1" data-pin-complete="1">2</pin> <br/><br/><br/>

				Standard Private Pin <br/><br/>
				<pin class="big" data-pin-type="standard" data-pin-private="1">2</pin> <br/><br/>
				<pin class="mid" data-pin-type="standard" data-pin-private="1">2</pin> <br/><br/>
				<pin class="small" data-pin-type="standard" data-pin-private="1">2</pin>

			</div>
			<div class="col xl-center">

				Live Complete Pin <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-complete="1">3</pin> <br/><br/><br/>

				Live Pin <br/><br/>
				<pin class="big" data-pin-type="live">3</pin> <br/><br/>
				<pin class="mid" data-pin-type="live">3</pin> <br/><br/>
				<pin class="small" data-pin-type="live">3</pin>

			</div>
			<div class="col xl-center">

				Live Private Complete Pin <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-private="1" data-pin-complete="1">4</pin> <br/><br/><br/>

				Live Private Pin <br/><br/>
				<pin class="big" data-pin-type="live" data-pin-private="1">4</pin> <br/><br/>
				<pin class="mid" data-pin-type="live" data-pin-private="1">4</pin> <br/><br/>
				<pin class="small" data-pin-type="live" data-pin-private="1">4</pin>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Mouse Cursor <br/><br/>
				<pin class="mouse-cursor big active" data-pin-type="standard" data-pin-private="0" style="position: static;">1</pin> <br/><br/>
				<pin class="mouse-cursor active" data-pin-type="standard" data-pin-private="0" style="position: static;">1</pin>

			</div>
			<div class="col xl-center">

				Mouse Cursor Existing <br/><br/>
				<pin class="mouse-cursor big active existing" data-pin-type="standard" data-pin-private="0" style="position: static;">2</pin> <br/><br/>
				<pin class="mouse-cursor active existing" data-pin-type="standard" data-pin-private="0" style="position: static;">2</pin>

			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Live Pin Window <br/><br/>


				<div id="pin-window" class="ui-draggable active" data-pin-id="244" data-pin-type="live" data-pin-private="0" data-pin-complete="0" data-pin-x="238.43552" data-pin-y="35.68798" data-pin-modification-type="html" data-revisionary-edited="1" data-changed="no" data-showing-changes="yes" data-has-comments="no" data-revisionary-showing-changes="1" data-revisionary-index="49" style="position: static;" data-pin-mine="yes" data-pin-new="no" data-new-notification="no">

		<div class="wrap xl-flexbox xl-between top-actions">
			<div class="col move-window left-tooltip ui-draggable-handle" data-tooltip="Drag &amp; Drop the pin window to detach from the pin.">
				<i class="fa fa-arrows-alt"></i>
			</div>
			<div class="col">

				<div class="wrap xl-flexbox actions">
					<div class="col action dropdown">

						<pin class="chosen-pin" data-pin-type="live" data-pin-private="0"></pin>
						<a href="#"><span class="pin-label">LIVE EDIT</span> <i class="fa fa-caret-down"></i></a>

						<ul class="xl-left type-convertor">

							<li class="convert-to-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="0" data-pin-modification-type=""></pin>
									<span>Live Edit</span>
								</a>
							</li>

							<li class="convert-to-standard">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="0" data-pin-modification-type="null"></pin>
									<span>Only View</span>
								</a>
							</li>

							<li class="convert-to-private-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="1" data-pin-modification-type=""></pin>
									<span>Private Live</span>
								</a>
							</li>

							<li class="convert-to-private">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="1" data-pin-modification-type="null"></pin>
									<span>Private View</span>
								</a>
							</li>

						</ul>

					</div>
					<div class="col action">
						<a href="#" class="center-tooltip bottom-tooltip" data-tooltip="Only For Current Device (In development...)" style="ccolor: #007acc;"><i class="fa fa-thumbtack"></i></a>
					</div>
					<div class="col action" data-tooltip="Coming soon." style="display: none !important;">

						<i class="fa fa-user-o"></i>
						<span>ASSIGNEE</span>

					</div>
				</div>

			</div>
			<div class="col">
				<a href="#" class="close-button" data-tooltip="Close this pin window when you're done here."><i class="fa fa-check"></i></a>
			</div>
		</div>

		<div class="image-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-image"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">Drag &amp; Drop or <span class="select-file">Select File</span></div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">ORIGINAL IMAGE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1">
						<div class="col">
							<div class="edit-content changes uploader">

							    <img class="new-image" src="">
							    <div class="info"><span><span style="text-decoration: underline;">Click here</span> or drag here your image for preview</span></div>
							    <input type="file" name="image" id="filePhoto" data-max-size="3145728">

							</div>
							<div class="edit-content original">
							    <img class="original-image" src="">
							</div>
						</div>
					</div>
					<div class="wrap xl-1 xl-right difference-switch-wrap">
						<a href="#" class="col switch remove-image">
							<i class="fa fa-unlink"></i> REMOVE IMAGE
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="content-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-pencil-alt"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">EDIT CONTENT:</div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">
							<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
							ORIGINAL CONTENT:
						</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap differences">
						<div class="col"><i class="fa fa-random"></i> DIFFERENCE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes xl-hidden">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1 content-boxes">
						<div class="col">
							<div class="edit-content changes" contenteditable="true">The Preferred<br data-revisionary-index="50"><u><b>Jet Selling Service</b></u></div>
							<div class="edit-content original">The Preferred<br data-revisionary-index="50">Jet Acquisition Service</div>
							<div class="edit-content differences"><span class="diff grey">The Preferred<br>Jet </span><span class="diff red">Acquisition</span><span class="diff green">Selling</span><span class="diff grey"> Service</span></div>
						</div>
					</div>

					<div class="wrap xl-2 difference-switch-wrap" style="padding-left: 10px;">
						<a href="#" class="col switch reset-content">
							<span><i class="fa fa-unlink"></i> RESET CHANGES</span>
						</a>
						<a href="#" class="col xl-right switch difference-switch">
							<i class="fa fa-pencil-alt fa-random"></i> <span class="diff-text">SHOW DIFFERENCE</span>
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="visual-editor">

			<div class="wrap xl-1">
				<div class="col section-title collapsed">

					<i class="fa fa-sliders-h"></i> STYLE <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content options">

					<ul class="no-bullet options" style="margin-bottom: 0;" data-display="block" data-opacity="1" data-font-size="72px" data-line-height="normal" data-color="rgb(38, 52, 76)" data-font-weight="400" data-font-style="normal" data-text-decoration-line="none" data-text-align="center" data-background-color="rgba(0, 0, 0, 0)" data-background-image="none" data-background-position-x="50%" data-background-position-y="50%" data-background-size="cover" data-background-repeat="no-repeat" data-top="auto" data-right="auto" data-bottom="auto" data-left="auto" data-margin-top="48.24px" data-margin-right="0px" data-margin-bottom="50px" data-margin-left="0px" data-border-top-width="0px" data-border-right-width="0px" data-border-bottom-width="0px" data-border-left-width="0px" data-border-style="none" data-border-color="rgb(38, 52, 76)" data-border-top-left-radius="0px" data-border-top-right-radius="0px" data-border-bottom-left-radius="0px" data-border-bottom-right-radius="0px" data-padding-top="0px" data-padding-right="0px" data-padding-bottom="0px" data-padding-left="0px" data-width="1350px" data-height="156px">
						<li class="current-element">

							<span class="css-selector"><b>EDIT STYLE:</b> <span class="element-tag">H1</span><span class="element-id"></span><span class="element-class">.blue-text.margin-bottom-50</span></span>

							<a href="#" class="switch show-original-css" style="position: absolute; right: 0; top: 5px; z-index: 1;">
								<span class="original"><img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt=""> SHOW ORIGINAL</span>
								<span class="changes"><img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt=""> SHOW CHANGES</span>
							</a>

							<a href="#" class="switch reset-css" style="position: absolute; right: 0; top: 22px; z-index: 1;">
								<span><i class="fa fa-unlink"></i>RESET CHANGES</span>
							</a>

						</li>
						<li class="main-option choice">

							<a href="#" data-edit-css="display" data-value="block" data-default="none" class="active"><i class="fa fa-eye"></i> Show</a> |
							<a href="#" data-edit-css="display" data-value="none" data-default="block"><i class="fa fa-eye-slash"></i> Hide</a>

						</li>
						<li class="main-option dropdown edit-opacity hide-when-hidden">

							<a href="#"><i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li>

									<input type="range" min="0" max="1" step="0.01" value="1" class="range-slider" id="edit-opacity" data-edit-css="opacity" data-default="1"> <div class="percentage">100</div>

								</li>
							</ul>

						</li>
						<li class="main-option dropdown hide-when-hidden">

							<a href="#"><i class="fa fa-font"></i> Text &amp; Item <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay">
								<li class="choice">

									<label class="main-option sub"><span class="inline"><i class="fa fa-font"></i> Size</span> <input type="text" class="increaseable" data-edit-css="font-size" data-default="initial"></label>
									<label class="main-option sub"><span class="inline"><i class="fa fa-text-height"></i> Line</span> <input type="text" class="increaseable" data-edit-css="line-height" data-default="normal"></label>

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-tint"></i> Color</span> <input type="color" data-edit-css="color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgb(38, 52, 76);"></div></div><div class="sp-dd">▼</div></div>

								</li>
								<li class="main-option sub choice selectable">

									<a href="#" data-edit-css="font-weight" data-value="bold" data-default="normal"><i class="fa fa-bold"></i> Bold</a> |
									<a href="#" data-edit-css="font-style" data-value="italic" data-default="normal"><i class="fa fa-italic"></i> Italic</a> |
									<a href="#" data-edit-css="text-decoration-line" data-value="underline" data-default="none"><i class="fa fa-underline"></i> Underline</a>

								</li>
								<li class="main-option sub choice">

									<a href="#" data-edit-css="text-align" data-value="left" data-default="right"><i class="fa fa-align-left"></i> Left</a> |
									<a href="#" data-edit-css="text-align" data-value="center" data-default="left" class="active"><i class="fa fa-align-center"></i> Center</a> |
									<a href="#" data-edit-css="text-align" data-value="justify" data-default="left"><i class="fa fa-align-justify"></i> Justify</a> |
									<a href="#" data-edit-css="text-align" data-value="right" data-default="left"><i class="fa fa-align-right"></i> Right</a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden">
							<a href="#"><i class="fa fa-layer-group"></i> Background <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-fill-drip"></i> Color:</span>
									<input type="color" data-edit-css="background-color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgba(0, 0, 0, 0);"></div></div><div class="sp-dd">▼</div></div>

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-image"></i> Image URL:</span> <input type="url" data-edit-css="background-image" data-default="none" class="full no-padding">

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-crosshairs"></i> Position:</span>

									<span class="inline">X:</span> <input type="text" class="increaseable" data-edit-css="background-position-x" data-default="initial">
									<span class="inline">Y:</span> <input type="text" class="increaseable" data-edit-css="background-position-y" data-default="initial">

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-arrows-alt-v"></i> Size: </span>

									<a href="#" data-edit-css="background-size" data-value="auto" data-default="cover">Auto</a> |
									<a href="#" data-edit-css="background-size" data-value="cover" data-default="auto" class="active">Cover</a> |
									<a href="#" data-edit-css="background-size" data-value="contain" data-default="auto">Contain</a>

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-redo"></i> Repeat: </span>

									<a href="#" data-edit-css="background-repeat" data-value="no-repeat" data-tooltip="No Repeat" data-default="repeat-x" class="active"><i class="fa fa-compress-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat" data-tooltip="Repeat X and Y" data-default="no-repeat"><i class="fa fa-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-x" data-tooltip="Repeat X" data-default="no-repeat"><i class="fa fa-long-arrow-alt-right"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-y" data-tooltip="Repeat Y" data-default="no-repeat"><i class="fa fa-long-arrow-alt-down"></i></a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden" data-tooltip="Experimental">
							<a href="#"><i class="fa fa-object-group"></i> Spacing &amp; Positions <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full" style="width: auto;">
								<li>

									<div class="css-box">

										<div class="layer positions">

<div class="main-option sub input top"><input type="text" data-edit-css="top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="left" data-default="initial"></div>


											<div class="layer margins">

<div class="main-option sub input top"><input type="text" data-edit-css="margin-top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="margin-right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="margin-bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="margin-left" data-default="initial"></div>


												<div class="layer borders">

<div class="main-option sub input top"><input type="text" data-edit-css="border-top-width" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="border-right-width" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="border-bottom-width" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="border-left-width" data-default="initial"></div>



<div class="main-option sub input top left middle"><input type="text" data-edit-css="border-style" data-default="initial"></div>
<div class="main-option sub input top right middle"><input type="color" data-edit-css="border-color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgb(38, 52, 76);"></div></div><div class="sp-dd">▼</div></div></div>

<div class="main-option sub input top left"><input type="text" data-edit-css="border-top-left-radius" data-default="initial"><span>Radius</span></div>
<div class="main-option sub input top right"><input type="text" data-edit-css="border-top-right-radius" data-default="initial"><span>Radius</span></div>
<div class="main-option sub input bottom left"><span>Radius</span><input type="text" data-edit-css="border-bottom-left-radius" data-default="initial"></div>
<div class="main-option sub input bottom right"><span>Radius</span><input type="text" data-edit-css="border-bottom-right-radius" data-default="initial"></div>


													<div class="layer paddings">

<div class="main-option sub input top"><input type="text" data-edit-css="padding-top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="padding-right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="padding-bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="padding-left" data-default="initial"></div>


														<div class="layer sizes">

<input type="text" data-edit-css="width" data-default="initial"> x
<input type="text" data-edit-css="height" data-default="initial">

														</div>

													</div>

												</div>

											</div>

										</div>

									</div>

								</li>
							</ul>
						</li>
					</ul>

				</div>
			</div>

		</div>

		<div class="comments">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-comment-dots"></i> COMMENTS <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content">

					<div class="pin-comments"><div class="no-comments xl-center">No comments yet.</div></div>
					<div class="comment-actions">

						<form action="" method="post" id="comment-sender">
							<div class="wrap xl-flexbox xl-between">
								<div class="col comment-input-col">
									<textarea class="comment-input resizeable" rows="1" placeholder="Type your comments, and hit 'Enter'..." required="" style="overflow: hidden; overflow-wrap: break-word; height: 31px;"></textarea>
								</div>
								<div class="col">
									<input type="image" src="http://inscr.revisionaryapp.com/assets/icons/comment-send.svg">
								</div>
							</div>
						</form>

					</div>

				</div>
			</div>



		</div>

		<div class="bottom-actions">

			<div class="wrap xl-flexbox xl-between">
				<div class="col action dropdown">
					<a href="#">
						<i class="fa fa-pencil-square-o"></i> MARK <i class="fa fa-caret-down"></i>
					</a>
					<ul>
						<li>
							<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon." style="padding-right: 15px;">
								<img src="http://inscr.revisionaryapp.com/assets/icons/mark-rectangle.png" width="15" height="10" alt="">
								RECTANGLE
							</a>
						</li>
						<li>
							<a href="#" class="xl-left" data-tooltip="Coming soon.">
								<img src="http://inscr.revisionaryapp.com/assets/icons/mark-ellipse.png" width="15" height="14" alt="">
								ELLIPSE
							</a>
						</li>
					</ul>
				</div>
				<div class="col action">
					<a href="#" class="remove-pin"><i class="fa fa-trash-o"></i> REMOVE</a>
				</div>
				<div class="col action pin-complete">
					<a href="#" class="complete-pin" data-tooltip="Mark as resolved">
						<pin data-pin-type="standard" data-pin-private="0" data-pin-complete="1"></pin>
						DONE
					</a>
					<a href="#" class="incomplete-pin" data-tooltip="Mark as unresolved">
						<pin data-pin-type="standard" data-pin-private="0" data-pin-complete="0"></pin>
						INCOMPLETE
					</a>
				</div>
			</div>

		</div>

	</div>


			</div>
			<div class="col xl-center">

				Live Pin Window Difference <br/><br/>


				<div id="pin-window" class="ui-draggable active show-differences" data-pin-id="244" data-pin-type="live" data-pin-private="0" data-pin-complete="0" data-pin-x="238.43552" data-pin-y="35.68798" data-pin-modification-type="html" data-revisionary-edited="1" data-changed="no" data-showing-changes="yes" data-has-comments="no" data-revisionary-showing-changes="1" data-revisionary-index="49" style="position: static;" data-pin-mine="yes" data-pin-new="no" data-new-notification="no">

		<div class="wrap xl-flexbox xl-between top-actions">
			<div class="col move-window left-tooltip ui-draggable-handle" data-tooltip="Drag &amp; Drop the pin window to detach from the pin.">
				<i class="fa fa-arrows-alt"></i>
			</div>
			<div class="col">

				<div class="wrap xl-flexbox actions">
					<div class="col action dropdown">

						<pin class="chosen-pin" data-pin-type="live" data-pin-private="0"></pin>
						<a href="#"><span class="pin-label">LIVE EDIT</span> <i class="fa fa-caret-down"></i></a>

						<ul class="xl-left type-convertor">

							<li class="convert-to-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="0" data-pin-modification-type=""></pin>
									<span>Live Edit</span>
								</a>
							</li>

							<li class="convert-to-standard">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="0" data-pin-modification-type="null"></pin>
									<span>Only View</span>
								</a>
							</li>

							<li class="convert-to-private-live">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="live" data-pin-private="1" data-pin-modification-type=""></pin>
									<span>Private Live</span>
								</a>
							</li>

							<li class="convert-to-private">
								<a href="#" class="xl-flexbox xl-middle">
									<pin data-pin-type="standard" data-pin-private="1" data-pin-modification-type="null"></pin>
									<span>Private View</span>
								</a>
							</li>

						</ul>

					</div>
					<div class="col action">
						<a href="#" class="center-tooltip bottom-tooltip" data-tooltip="Only For Current Device (In development...)" style="ccolor: #007acc;"><i class="fa fa-thumbtack"></i></a>
					</div>
					<div class="col action" data-tooltip="Coming soon." style="display: none !important;">

						<i class="fa fa-user-o"></i>
						<span>ASSIGNEE</span>

					</div>
				</div>

			</div>
			<div class="col">
				<a href="#" class="close-button" data-tooltip="Close this pin window when you're done here."><i class="fa fa-check"></i></a>
			</div>
		</div>

		<div class="image-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-image"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">Drag &amp; Drop or <span class="select-file">Select File</span></div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">ORIGINAL IMAGE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1">
						<div class="col">
							<div class="edit-content changes uploader">

							    <img class="new-image" src="">
							    <div class="info"><span><span style="text-decoration: underline;">Click here</span> or drag here your image for preview</span></div>
							    <input type="file" name="image" id="filePhoto" data-max-size="3145728">

							</div>
							<div class="edit-content original">
							    <img class="original-image" src="">
							</div>
						</div>
					</div>
					<div class="wrap xl-1 xl-right difference-switch-wrap">
						<a href="#" class="col switch remove-image">
							<i class="fa fa-unlink"></i> REMOVE IMAGE
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="content-editor">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-pencil-alt"></i> CONTENT <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content" style="padding-top: 10px;">

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap changes">
						<div class="col title">EDIT CONTENT:</div>
						<div class="col">

							<a href="#" class="switch edits-switch original">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
								SHOW ORIGINAL
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap original">
						<div class="col">
							<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt="">
							ORIGINAL CONTENT:
						</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-flexbox xl-between xl-bottom edits-switch-wrap differences">
						<div class="col"><i class="fa fa-random"></i> DIFFERENCE:</div>
						<div class="col edits-switch-wrap">

							<a href="#" class="switch edits-switch changes xl-hidden">
								<img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt="">
								SHOW CHANGED
							</a>

						</div>
					</div>

					<div class="wrap xl-1 content-boxes">
						<div class="col">
							<div class="edit-content changes" contenteditable="true">The Preferred<br data-revisionary-index="50"><u><b>Jet Selling Service</b></u></div>
							<div class="edit-content original">The Preferred<br data-revisionary-index="50">Jet Acquisition Service</div>
							<div class="edit-content differences"><span class="diff grey">The Preferred<br>Jet </span><span class="diff red">Acquisition</span><span class="diff green">Selling</span><span class="diff grey"> Service</span></div>
						</div>
					</div>

					<div class="wrap xl-2 difference-switch-wrap" style="padding-left: 10px;">
						<a href="#" class="col switch reset-content">
							<span><i class="fa fa-unlink"></i> RESET CHANGES</span>
						</a>
						<a href="#" class="col xl-right switch difference-switch">
							<i class="fa fa-pencil-alt"></i> <span class="diff-text">SHOW CHANGES</span>
						</a>
					</div>

				</div>
			</div>

		</div>

		<div class="visual-editor">

			<div class="wrap xl-1">
				<div class="col section-title collapsed">

					<i class="fa fa-sliders-h"></i> STYLE <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content options">

					<ul class="no-bullet options" style="margin-bottom: 0;" data-display="block" data-opacity="1" data-font-size="72px" data-line-height="normal" data-color="rgb(38, 52, 76)" data-font-weight="400" data-font-style="normal" data-text-decoration-line="none" data-text-align="center" data-background-color="rgba(0, 0, 0, 0)" data-background-image="none" data-background-position-x="50%" data-background-position-y="50%" data-background-size="cover" data-background-repeat="no-repeat" data-top="auto" data-right="auto" data-bottom="auto" data-left="auto" data-margin-top="48.24px" data-margin-right="0px" data-margin-bottom="50px" data-margin-left="0px" data-border-top-width="0px" data-border-right-width="0px" data-border-bottom-width="0px" data-border-left-width="0px" data-border-style="none" data-border-color="rgb(38, 52, 76)" data-border-top-left-radius="0px" data-border-top-right-radius="0px" data-border-bottom-left-radius="0px" data-border-bottom-right-radius="0px" data-padding-top="0px" data-padding-right="0px" data-padding-bottom="0px" data-padding-left="0px" data-width="1350px" data-height="156px">
						<li class="current-element">

							<span class="css-selector"><b>EDIT STYLE:</b> <span class="element-tag">H1</span><span class="element-id"></span><span class="element-class">.blue-text.margin-bottom-50</span></span>

							<a href="#" class="switch show-original-css" style="position: absolute; right: 0; top: 5px; z-index: 1;">
								<span class="original"><img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-off.svg" alt=""> SHOW ORIGINAL</span>
								<span class="changes"><img src="http://inscr.revisionaryapp.com/assets/icons/edits-switch-on.svg" alt=""> SHOW CHANGES</span>
							</a>

							<a href="#" class="switch reset-css" style="position: absolute; right: 0; top: 22px; z-index: 1;">
								<span><i class="fa fa-unlink"></i>RESET CHANGES</span>
							</a>

						</li>
						<li class="main-option choice">

							<a href="#" data-edit-css="display" data-value="block" data-default="none" class="active"><i class="fa fa-eye"></i> Show</a> |
							<a href="#" data-edit-css="display" data-value="none" data-default="block"><i class="fa fa-eye-slash"></i> Hide</a>

						</li>
						<li class="main-option dropdown edit-opacity hide-when-hidden">

							<a href="#"><i class="fa fa-low-vision"></i> Opacity <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li>

									<input type="range" min="0" max="1" step="0.01" value="1" class="range-slider" id="edit-opacity" data-edit-css="opacity" data-default="1"> <div class="percentage">100</div>

								</li>
							</ul>

						</li>
						<li class="main-option dropdown hide-when-hidden">

							<a href="#"><i class="fa fa-font"></i> Text &amp; Item <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay">
								<li class="choice">

									<label class="main-option sub"><span class="inline"><i class="fa fa-font"></i> Size</span> <input type="text" class="increaseable" data-edit-css="font-size" data-default="initial"></label>
									<label class="main-option sub"><span class="inline"><i class="fa fa-text-height"></i> Line</span> <input type="text" class="increaseable" data-edit-css="line-height" data-default="normal"></label>

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-tint"></i> Color</span> <input type="color" data-edit-css="color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgb(38, 52, 76);"></div></div><div class="sp-dd">▼</div></div>

								</li>
								<li class="main-option sub choice selectable">

									<a href="#" data-edit-css="font-weight" data-value="bold" data-default="normal"><i class="fa fa-bold"></i> Bold</a> |
									<a href="#" data-edit-css="font-style" data-value="italic" data-default="normal"><i class="fa fa-italic"></i> Italic</a> |
									<a href="#" data-edit-css="text-decoration-line" data-value="underline" data-default="none"><i class="fa fa-underline"></i> Underline</a>

								</li>
								<li class="main-option sub choice">

									<a href="#" data-edit-css="text-align" data-value="left" data-default="right"><i class="fa fa-align-left"></i> Left</a> |
									<a href="#" data-edit-css="text-align" data-value="center" data-default="left" class="active"><i class="fa fa-align-center"></i> Center</a> |
									<a href="#" data-edit-css="text-align" data-value="justify" data-default="left"><i class="fa fa-align-justify"></i> Justify</a> |
									<a href="#" data-edit-css="text-align" data-value="right" data-default="left"><i class="fa fa-align-right"></i> Right</a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden">
							<a href="#"><i class="fa fa-layer-group"></i> Background <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full">
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-fill-drip"></i> Color:</span>
									<input type="color" data-edit-css="background-color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgba(0, 0, 0, 0);"></div></div><div class="sp-dd">▼</div></div>

								</li>
								<li class="main-option sub choice">

									<span class="inline"><i class="fa fa-image"></i> Image URL:</span> <input type="url" data-edit-css="background-image" data-default="none" class="full no-padding">

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-crosshairs"></i> Position:</span>

									<span class="inline">X:</span> <input type="text" class="increaseable" data-edit-css="background-position-x" data-default="initial">
									<span class="inline">Y:</span> <input type="text" class="increaseable" data-edit-css="background-position-y" data-default="initial">

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-arrows-alt-v"></i> Size: </span>

									<a href="#" data-edit-css="background-size" data-value="auto" data-default="cover">Auto</a> |
									<a href="#" data-edit-css="background-size" data-value="cover" data-default="auto" class="active">Cover</a> |
									<a href="#" data-edit-css="background-size" data-value="contain" data-default="auto">Contain</a>

								</li>
								<li class="main-option sub choice hide-when-no-image">

									<span><i class="fa fa-redo"></i> Repeat: </span>

									<a href="#" data-edit-css="background-repeat" data-value="no-repeat" data-tooltip="No Repeat" data-default="repeat-x" class="active"><i class="fa fa-compress-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat" data-tooltip="Repeat X and Y" data-default="no-repeat"><i class="fa fa-arrows-alt"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-x" data-tooltip="Repeat X" data-default="no-repeat"><i class="fa fa-long-arrow-alt-right"></i></a> |
									<a href="#" data-edit-css="background-repeat" data-value="repeat-y" data-tooltip="Repeat Y" data-default="no-repeat"><i class="fa fa-long-arrow-alt-down"></i></a>

								</li>
							</ul>
						</li>
						<li class="main-option dropdown hide-when-hidden" data-tooltip="Experimental">
							<a href="#"><i class="fa fa-object-group"></i> Spacing &amp; Positions <i class="fa fa-angle-down"></i></a>
							<ul class="no-delay full" style="width: auto;">
								<li>

									<div class="css-box">

										<div class="layer positions">

<div class="main-option sub input top"><input type="text" data-edit-css="top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="left" data-default="initial"></div>


											<div class="layer margins">

<div class="main-option sub input top"><input type="text" data-edit-css="margin-top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="margin-right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="margin-bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="margin-left" data-default="initial"></div>


												<div class="layer borders">

<div class="main-option sub input top"><input type="text" data-edit-css="border-top-width" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="border-right-width" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="border-bottom-width" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="border-left-width" data-default="initial"></div>



<div class="main-option sub input top left middle"><input type="text" data-edit-css="border-style" data-default="initial"></div>
<div class="main-option sub input top right middle"><input type="color" data-edit-css="border-color" data-default="initial" style="display: none;"><div class="sp-replacer sp-light"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgb(38, 52, 76);"></div></div><div class="sp-dd">▼</div></div></div>

<div class="main-option sub input top left"><input type="text" data-edit-css="border-top-left-radius" data-default="initial"><span>Radius</span></div>
<div class="main-option sub input top right"><input type="text" data-edit-css="border-top-right-radius" data-default="initial"><span>Radius</span></div>
<div class="main-option sub input bottom left"><span>Radius</span><input type="text" data-edit-css="border-bottom-left-radius" data-default="initial"></div>
<div class="main-option sub input bottom right"><span>Radius</span><input type="text" data-edit-css="border-bottom-right-radius" data-default="initial"></div>


													<div class="layer paddings">

<div class="main-option sub input top"><input type="text" data-edit-css="padding-top" data-default="initial"></div>
<div class="main-option sub input right"><input type="text" data-edit-css="padding-right" data-default="initial"></div>
<div class="main-option sub input bottom"><input type="text" data-edit-css="padding-bottom" data-default="initial"></div>
<div class="main-option sub input left"><input type="text" data-edit-css="padding-left" data-default="initial"></div>


														<div class="layer sizes">

<input type="text" data-edit-css="width" data-default="initial"> x
<input type="text" data-edit-css="height" data-default="initial">

														</div>

													</div>

												</div>

											</div>

										</div>

									</div>

								</li>
							</ul>
						</li>
					</ul>

				</div>
			</div>

		</div>

		<div class="comments">

			<div class="wrap xl-1">
				<div class="col section-title">

					<i class="fa fa-comment-dots"></i> COMMENTS <i class="fa fa-circle edited-sign"></i>

				</div>
				<div class="col section-content">

					<div class="pin-comments"><div class="no-comments xl-center">No comments yet.</div></div>
					<div class="comment-actions">

						<form action="" method="post" id="comment-sender">
							<div class="wrap xl-flexbox xl-between">
								<div class="col comment-input-col">
									<textarea class="comment-input resizeable" rows="1" placeholder="Type your comments, and hit 'Enter'..." required="" style="overflow: hidden; overflow-wrap: break-word; height: 31px;"></textarea>
								</div>
								<div class="col">
									<input type="image" src="http://inscr.revisionaryapp.com/assets/icons/comment-send.svg">
								</div>
							</div>
						</form>

					</div>

				</div>
			</div>



		</div>

		<div class="bottom-actions">

			<div class="wrap xl-flexbox xl-between">
				<div class="col action dropdown">
					<a href="#">
						<i class="fa fa-pencil-square-o"></i> MARK <i class="fa fa-caret-down"></i>
					</a>
					<ul>
						<li>
							<a href="#" class="xl-left draw-rectangle" data-tooltip="Coming soon." style="padding-right: 15px;">
								<img src="http://inscr.revisionaryapp.com/assets/icons/mark-rectangle.png" width="15" height="10" alt="">
								RECTANGLE
							</a>
						</li>
						<li>
							<a href="#" class="xl-left" data-tooltip="Coming soon.">
								<img src="http://inscr.revisionaryapp.com/assets/icons/mark-ellipse.png" width="15" height="14" alt="">
								ELLIPSE
							</a>
						</li>
					</ul>
				</div>
				<div class="col action">
					<a href="#" class="remove-pin"><i class="fa fa-trash-o"></i> REMOVE</a>
				</div>
				<div class="col action pin-complete">
					<a href="#" class="complete-pin" data-tooltip="Mark as resolved">
						<pin data-pin-type="standard" data-pin-private="0" data-pin-complete="1"></pin>
						DONE
					</a>
					<a href="#" class="incomplete-pin" data-tooltip="Mark as unresolved">
						<pin data-pin-type="standard" data-pin-private="0" data-pin-complete="0"></pin>
						INCOMPLETE
					</a>
				</div>
			</div>

		</div>

	</div>


			</div>
		</div>


		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>