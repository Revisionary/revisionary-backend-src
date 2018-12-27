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


		<span class="new_dropdown">
			<a href="#" class="learn-more">Dropdown Link <i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul>
				<li><span>No Link Item</span></li>
				<li><a href="#">Linked Item</a></li>
				<li class="selected"><a href="#">Selected Item</a></li>
				<li><a href="#"><i class="fa fa-user"></i> Item with Icon</a></li>
				<li class="selected"><a href="#"><i class="fa fa-user"></i> Selected Iconic Item</a></li>
				<li>
					<a href="#"><i class="fa fa-sign-in-alt"></i> Alt Dropdown Item</a>
					<ul>
						<li><span>Sub Item 1</span></li>
						<li>
							<a href="#"><i class="fa fa-sign-in-alt"></i> Sub Item 2</a>
							<ul>
								<li><span>Subber Item 1</span></li>
								<li>

									<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2</a>
									<ul>
										<li><span>Subber Item 1</span></li>
										<li>
											<a href="#"><i class="fa fa-sign-in-alt"></i> Subber Item 2</a>
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
					<a href="#"><i class="fa fa-sign-in-alt"></i> Selectables</a>
					<ul class="selectable">
						<li><a href="#">Selectable 1</a></li>
						<li class="selected"><a href="#">Selectable 2</a></li>
						<li><a href="#">Selectable 3</a></li>
						<li><a href="#">Selectable 4</a></li>
					</ul>
				</li>
				<li>
					<a href="#"><i class="fa fa-sign-in-alt"></i> Addables</a>
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
	</div>

</main><!-- #first-section -->

<?php require view('static/footer_frontend'); ?>
<?php require view('static/footer_html'); ?>