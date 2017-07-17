<div id="add-new" class="popup-window xl-center scrollable-content">
	<h2>Add New Project</h2>
	<h5 class="to">To <b>Main Pages</b> Section</h5>

	<form action="/projects" method="post">

		<input type="hidden" name="add_new_nonce" value="<?=$_SESSION['add_new_nonce']?>"/>
		<input type="hidden" name="add_new" value="true"/>
		<input type="hidden" name="category" value="0"/>
		<input type="hidden" name="order" value="0"/>

		<input type="hidden" name="devices[]" value="4"/>
		<input type="hidden" name="devices[]" value="7"/>



		<div class="wrap xl-center">
			<div class="col xl-5-7">


				<a href="#" class="option-toggler less-options">Less Options <i class="fa fa-caret-up" aria-hidden="true"></i></a>


				<h4 class="section-title xl-left">Project Info</h4>

				<h3>Site Name <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
				<input type="text" name="project-name" placeholder="e.g. Google, BBC, ..."/>


				<a href="#" class="option-toggler more-options">More Options <i class="fa fa-caret-down" aria-hidden="true"></i></a>


				<!-- More Options -->
				<div class="more-options-wrapper">


					<h3>Project Members <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.User::ID()->userName)?>">
							<picture class="profile-picture" <?=User::ID()->printPicture()?>>
								<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							</picture>
						</a>


						<!-- Other Shared People
						<a href="http://new.revisionaryapp.com/sara">
							<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/cache/user-3/sara.png);"></picture>
						</a>-->


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>


					</div>
					<br/>


					<h4 class="section-title xl-left">Page Info</h4>

					<h3>First Page URL <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
					<input type="url" name="page-url" placeholder="https://example.com/..." disabled/>


					<h3>Page Name <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
					<input type="text" name="page-name" placeholder="e.g. Home, About, ..." disabled/>


					<h3 style="margin-bottom: 0">Devices <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
					<ul class="selected-devices">
						<li>
							<i class="fa fa-laptop" aria-hidden="true"></i> <span>Current Screen (1400 x 900)</span>
						</li>
						<li>
							<i class="fa fa-tablet" aria-hidden="true"></i> <span>Tablet (768 x 1024)</span>
						</li>
					</ul>
					<a href="#" class="add-device">ADD DEVICE <i class="fa fa-caret-down" aria-hidden="true"></i></a>


					<h3>Page Members <i class="fa fa-question-circle" aria-hidden="true"></i></h3>
					<div class="people">


						<!-- Owner -->
						<a href="<?=site_url('profile/'.User::ID()->userName)?>">
							<picture class="profile-picture" <?=User::ID()->printPicture()?>>
								<span <?=User::ID()->userPic != "" ? "class='has-pic'" : ""?>><?=substr(User::ID()->firstName, 0, 1).substr(User::ID()->lastName, 0, 1)?></span>
							</picture>
						</a>


						<!-- Other Shared People
						<a href="http://new.revisionaryapp.com/sara">
							<picture class="profile-picture" style="background-image: url(http://new.revisionaryapp.com/assets/cache/user-3/sara.png);"></picture>
						</a>-->


						<!-- Add New -->
						<a href="#" class="new-member"><span style="font-family: Arial; font-weight: bold;">+</span></a>


					</div>
					<br/>


				</div>



				<!-- Actions -->
				<div class="wrap xl-2 xl-center">
					<div class="col">


						<button class="cancel-button light small">Cancel</button>


					</div>
					<div class="col">


						<button class="dark small">Add</button>


					</div>
				</div>
				<br/>


			</div>
		</div>

	</form>


</div>