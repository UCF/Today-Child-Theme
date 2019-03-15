<?php
$title_elem = ( is_home() || is_front_page() ) ? 'h1' : 'span';

$menu_container_class = 'collapse navbar-collapse w-100';

$menu = wp_nav_menu( array(
	'container'       => 'false',
	'depth'           => 2,
	'echo'            => false,
	'fallback_cb'     => 'bs4Navwalker::fallback',
	'menu_class'      => 'navbar-nav w-100 d-flex flex-lg-row justify-content-between my-3 my-lg-0 py-3',
	'menu_id'         => 'header-navigation',
	'theme_location'  => 'header-menu',
	'walker'          => new bs4Navwalker()
) );
?>

<div class="site-nav-overlay fade" id="nav-overlay"></div>
<div class="today-nav-wrapper">
	<div class="container today-nav-inner d-flex flex-row py-2">
		<div class="today-nav-info d-flex flex-row align-items-center">
			<<?php echo $title_elem; ?> class="mb-0">
				<a class="navbar-brand mr-lg-3 d-flex" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo TODAY_THEME_IMG_URL . '/ucf-today-logo.svg'; ?>" alt="<?php echo get_bloginfo( 'name' ); ?>">
				</a>
			</<?php echo $title_elem; ?>>
			<div class="today-nav-desc ml-2 text-muted letter-spacing-1 text-uppercase hidden-md-down"><?php echo wptexturize( get_bloginfo( 'description' ) ); ?></div>
		</div>
		<div class="today-nav-actions d-flex flex-row align-items-center ml-auto">
			<?php if ( disable_md_nav_toggle() ) : ?>
				<?php echo output_nav_weather_data(); ?>
			<?php endif; ?>
			<button class="navbar-toggler ml-auto align-self-start collapsed" type="button" aria-controls="header-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-text">Sections</span>
				<span class="navbar-toggler-icon"></span>
			</button>
		</div>
	</div>
	<nav class="today-site-nav navbar navbar-light d-block p-0" id="header-menu" role="navigation">
		<div class="container">
			<button type="button" class="close p-3 hidden-lg-up" aria-label="Close Menu">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="hidden-lg-up mt-4 px-4">
				<a href="<?php echo get_home_url(); ?>">
					<img src="<?php echo TODAY_THEME_IMG_URL . '/ucf-today-logo.svg'; ?>" alt="<?php echo get_bloginfo( 'name' ); ?>">
				</a>
				<div class="today-nav-desc mt-2 text-muted letter-spacing-1 text-uppercase"><?php echo wptexturize( get_bloginfo( 'description' ) ); ?></div>
			</div>
			<?php echo $menu; ?>
			<div class="hidden-lg-up w-100 px-4">
				<?php echo output_nav_weather_data(); ?>
			</div>
		</div>
	</nav>
</div>
