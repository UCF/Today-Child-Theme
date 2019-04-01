<?php get_header(); ?>

<?php
$posts = get_posts( array(
	'numberposts' => 10,
	'tag'         => get_queried_object()->slug
) );

if ( isset( $posts ) ) {
	$first_post = array_shift( $posts );
}
?>

<div class="container mt-4 mb-5 pb-sm-4">
	<div class="row">
		<div class="col-lg-8">
			<?php if ( $first_post ): ?>
				<?php echo today_display_feature_vertical( $first_post, array( 'layout__type' => 'primary' ) ); ?>
			<?php endif; ?>

			<?php if ( $posts ): ?>
				<div class="row">
					<?php foreach ( $posts as $post ): ?>
						<div class="col-lg-4">
							<?php echo today_display_feature_vertical( $post ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! $first_post && ! $posts ): ?>
				<p>No results found.</p>
			<?php endif; ?>
		</div>
		<div class="col-lg-4">
			<?php echo today_display_sidebar_events(); ?>
			<?php echo today_display_sidebar_external_stories(); ?>
			<?php echo today_display_sidebar_menu(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
