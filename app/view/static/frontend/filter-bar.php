			<!-- Toolbar -->
			<div class="toolbar wrap xl-flexbox xl-middle">
				<div class="col xl-3-12 xl-left">

				</div>

				<div class="col xl-6-12 xl-center filter invert-hover">

					<span class="search"><img src="<?=asset_url('icons/search.svg')?>" /></span>
<?php
	if ($_url[0] == "projects") {
?>
					<a class="<?=!isset($_url[1]) ? "selected" : ""?>" href="<?=site_url('projects')?>">ALL</a>
					<a class="<?=isset($_url[1]) && $_url[1] == "mine" ? "selected" : ""?>" href="<?=site_url('projects/mine')?>">MINE</a>
					<a class="<?=isset($_url[1]) && $_url[1] == "shared" ? "selected" : ""?>" href="<?=site_url('projects/shared')?>">SHARED WITH ME</a>
<?php
	} else {
?>
					<a class="<?=!isset($_url[2]) ? "selected" : ""?>" href="<?=site_url('pages/'.$_url[1])?>">ALL</a>
					<a class="<?=isset($_url[2]) && $_url[2] == "main-pages" ? "selected" : ""?>" href="<?=site_url('pages/'.$_url[1].'/main-pages')?>">MAIN PAGES</a>
					<a class="<?=isset($_url[2]) && $_url[2] == "portfolio-pages" ? "selected" : ""?>" href="<?=site_url('pages/'.$_url[1].'/portfolio-pages')?>">PORTFOLIO PAGES</a>
					<a class="<?=isset($_url[2]) && $_url[2] == "blog-pages" ? "selected" : ""?>" href="<?=site_url('pages/'.$_url[1].'/blog-pages')?>">BLOG PAGES</a>
					<a href="#"><span style="font-family: Arial;">+</span></a>
<?php
	}
?>


				</div>

				<div class="col xl-3-12 xl-right inline-guys">

					<?php
						// If pages shown
						if ($_url[0] == "pages") {
					?>
					<div class="dropdown-container">
						<span class="dropdown-opener bullet">DEVICE</span>
						<nav class="dropdown xl-center lower">
							<ul class="device-selector">
								<li class="selected"><a href="#" data-device="5">Desktop</a></li>
								<li><a href="#" data-device="4">iPhone</a></li>
								<li><a href="#" data-device="3">iPad</a></li>
							</ul>
						</nav>
					</div>
					<?php
						}
					?>

					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener bullet">SIZE</span>
						<nav class="dropdown xl-center lower">
							<ul class="size-selector">
								<li class="selected"><a href="#" data-column="6">6 Column</a></li>
								<li><a href="#" data-column="5">5 Column</a></li>
								<li><a href="#" data-column="4">4 Column</a></li>
								<li><a href="#" data-column="3">3 Column</a></li>
								<li><a href="#" data-column="2">2 Column</a></li>
							</ul>
						</nav>
					</div>

					<div class="dropdown-container" style="margin-left: 15px;">
						<span class="dropdown-opener bullet">SORT</span>
						<nav class="dropdown xl-center lower">
							<ul class="order-selector">
								<li <?=!isset($_GET['order']) || $_GET['order'] == "custom" ? ' class="selected"' : ""?>><a href="?" data-order="custom">Custom</a></li>
								<li <?=isset($_GET['order']) && $_GET['order'] == "name" ? ' class="selected"' : ""?>><a href="?order=name" data-order="name">By Name</a></li>
								<li <?=isset($_GET['order']) && $_GET['order'] == "date" ? ' class="selected"' : ""?>><a href="?order=date" data-order="date">By Date</a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>