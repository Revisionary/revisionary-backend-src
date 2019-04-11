<?php require view('static/header_html'); ?>
<?php require view('static/header_frontend'); ?>

	<div class="container">

		<h1 class="xl-center"><?=$page_title?></h1>

		<br/>
		<br/>
		<br/>


		<h1>Heading 1</h1>
		<h2>Heading 2</h2>
		<h3>Heading 3</h3>
		<h4>Heading 4</h4>
		<h5>Heading 5</h5>
		<h6>Heading 6</h6>


		<br/>
		<br/>
		<br/>


		<p>Pharagraph lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi aliquam, distinctio? Sequi vitae similique est facere maxime ea nulla, voluptatum vel quos esse aut dicta, sapiente sit eum dolorem libero.</p>


		<br/>
		<br/>
		<br/>


		<span class="dropdown">
			<a href="#" class="learn-more">Dropdown Link <i class="fa fa-caret-down" aria-hidden="true"></i></a>
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

		<br/>
		<br/>
		<br/>
		<br/>
		<br/>

		<input type="text" placeholder="Text Box"/>

		<br/>
		<br/>
		<br/>
		<br/>

		<button><a href="#">Button</a></button>

		<br/>
		<br/>
		<br/>
		<br/>
		<br/>

		<input type="text" placeholder="Form Input 1"/>
		<input type="text" placeholder="Form Input 2"/>
		<button><a href="#">Form Group</a></button>

		<br/>
		<br/>
		<br/>
		<br/>
		<br/>


		<div class="wrap xl-gutter-24">
			<div class="col xl-center">

				Profile Picture With Image <br/><br/>
				<picture class="profile-picture big" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture><br/><br/>

				<picture class="profile-picture" <?=getUserInfo(6)['printPicture']?> data-type="user" data-id="6">
					<span><?=getUserInfo(6)['nameAbbr']?></span>
				</picture>


			</div>
			<div class="col xl-center">


				Profile Picture Without Image <br/><br/>
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