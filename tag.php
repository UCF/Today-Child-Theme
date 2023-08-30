<?php get_header(); ?>

<?php
$tag = get_queried_object()->slug;

$posts = get_posts( array(
	'numberposts' => 10,
	'tag'         => $tag
) );

if ( isset( $posts ) ) {
	$first_post = array_shift( $posts );
}
?>

<div class="container mt-2 mt-md-3 mb-5 pb-sm-4">
	<div class="row">
		<div class="col-lg-8">
			<?php if ( $first_post ): ?>
				<div class="pb-4">
					<?php echo today_display_feature_vertical( $first_post, array( 'layout__type' => 'primary' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $posts ): ?>
				<div class="row">
					<?php foreach ( $posts as $post ): ?>
						<div class="col-lg-4 mb-4">
							<?php echo today_display_feature_vertical( $post ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! $first_post && ! $posts ): ?>
				<p>No results found.</p>
			<?php endif; ?>
		</div>
		<div class="col-lg-4 pl-lg-5">
			<?php echo today_display_sidebar_external_stories( array( 'tag' => $tag ) ); ?>
			<?php echo today_display_sidebar_events(); ?>
			<?php echo today_display_sidebar_menu(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
