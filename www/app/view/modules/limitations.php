<div class="limit-wrapper" data-current-plan="<?=getUserInfo()['userLevelName']?>">

	<div class="wrap xl-3 xl-right xl-flexbox xl-between">
		<div class="col xl-1-1 xl-right xl-hidden">
		
			<b><?=getUserInfo()['userLevelName']?></b> Account Usage:<br>
		
		</div>
		<div class="col total projects-limit <?=$projectsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$projectsCount?></span>/<span class='max'><?=$maxProjects?></span>
				<span class='desc'>Project<?=$maxProjects > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Project Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE PROJECT LIMIT</a></li>
			</ul>

		</div>
		<div class="col total pages-limit phases-limit <?=$phasesPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$phasesCount?></span>/<span class='max'><?=$maxPhases?></span>
				<span class='desc'>Page<?=$maxPhases > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Page/Phase Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE PAGE/PHASE LIMIT</a></li>
			</ul>

		</div>
		<div class="col total screens-limit devices-limit <?=$screensPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$screensCount?></span>/<span class='max'><?=$maxScreens?></span>
				<span class='desc'>Screen<?=$maxScreens > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Screen Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE SCREEN LIMIT</a></li>
			</ul>

		</div>
		<div class="col total live-pins-limit <?=$pinsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$pinsCount?></span>/<span class='max'><?=$maxPins?></span>
				<span class='desc'>Live Pin<?=$maxPins > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Live Pin Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE LIVE PIN LIMIT</a></li>
			</ul>

		</div>
		<div class="col total comment-pins-limit <?=$commentPinsPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$commentPinsCount?></span>/<span class='max'><?=$maxCommentPins?></span>
				<span class='desc'>Comment Pin<?=$maxCommentPins > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Comment Pin Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE COMMENT PIN LIMIT</a></li>
			</ul>

		</div>
		<div class="col total load-limit <?=$loadPercentage >= 100 ? "exceed" : ""?> dropdown">

			<a href="#" class="dropdown-opener">
				<span class='current'><?=$loadCount?></span>/<span class='max'><?=$maxLoad?></span>
				<span class='desc'>MB File<?=$maxLoad > 1 ? "s" : ""?> <i class="fa fa-question-circle tooltip"></i></span>
			</a>
			<ul class="right xl-left no-delay">
				<li class="notice">
					<div>
						<h4><b><?=getUserInfo()['userLevelName']?> Account</b> Web Page Size Limits</h4>
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus sapiente ullam unde, consectetur dolorum eveniet dolore doloribus quam ipsam autem amet iure animi.
					</div>
				</li>
				<li><a href="<?=site_url('upgrade')?>" class="button">INCREASE SIZE LIMIT</a></li>
			</ul>

		</div>
	</div>

</div>


<div class="xl-hidden"><b>Usage:</b> 8 MB of 25 MB (<?=getUserInfo()['userLevelName']?> Account)</div>