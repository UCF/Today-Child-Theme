<?php get_header(); the_post(); ?>

<?php
$primary   = today_get_homepage_content( $post->ID, true );
$secondary = today_get_homepage_content( $post->ID );
$resources = wp_nav_menu( array(
	'container'   => 'false',
	'depth'       => 1,
	'echo'        => false,
	'fallback_cb' => 'bs4Navwalker::fallback',
	'menu_class'  => 'nav flex-column flex-xl-row justify-content-xl-center home-footer-nav',
	'menu'        => 'Resources',
	'walker'      => new bs4Navwalker()
) );
?>

<div class="container mt-4 mt-md-5 mb-5 pb-sm-4">

	<?php echo $primary; ?>
	<?php if ( get_field( 'enable_sidebar' ) ) : ?>
	<div class="row">
		<div class="col-lg-8">
			<?php echo $secondary; ?>
		</div>
		<div class="col-lg-4 pl-lg-5 mt-5 mt-lg-0">
			<?php echo today_get_sidebar_markup( $post->ID ); ?>
		</div>
	</div>
	<?php else : ?>
		<?php echo $secondary; ?>
	<?php endif; ?>

</div>

<?php if ( $resources ): ?>
<hr class="my-0">
<div class="container-fluid p-3 text-center">
	<?php echo $resources; ?>
</div>
<?php endif; ?>

<?php get_footer(); ?>
