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
	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>