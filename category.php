<?php get_header(); ?>

<?php
$term = get_queried_object();

$enable_custom_page_content = get_field( 'category_customize_page_content', $term );
$custom_page_content = get_field( 'category_custom_page_content', $term );
// Ensure that enable_sidebar is true if ACF setting has not been set yet
$enable_sidebar = ( get_field( 'category_enable_sidebar', $term ) === null ) ? true : get_field( 'category_enable_sidebar', $term );
// Ensure that customize_sidebar is false if ACF setting has not been set yet
$customize_sidebar = ( get_field( 'category_customize_sidebar', $term ) === null ) ? false : get_field( 'category_customize_sidebar', $term );

$posts = get_posts( array(
	'numberposts' => 10,
	'cat'         => $term->term_id
) );

if ( isset( $posts ) ) {
	$first_post = array_shift( $posts );
}
?>

<div class="container mt-2 mt-md-3 mb-5 pb-sm-4">
	<div class="row">
		<?php if ( $enable_sidebar ) : ?>
		<div class="col-lg-8">
		<?php else : ?>
		<div class="col-12">
		<?php endif; ?>
			<?php if ( $enable_custom_page_content ) : ?>
				<?php echo $custom_page_content; ?>
			<?php else : ?>
				<?php if ( $first_post ) : ?>
					<div class="pb-4">
						<?php echo today_display_feature_vertical( $first_post, array( 'layout__type' => 'primary' ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $posts ) : ?>
					<div class="row">
						<?php foreach ( $posts as $post ) : ?>
							<div class="col-lg-4 mb-4">
								<?php echo today_display_feature_vertical( $post ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! $first_post && ! $posts ) : ?>
					<p>No results found.</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( $enable_sidebar ) : ?>
		<div class="col-lg-4 pl-lg-5">
			<?php if ( $customize_sidebar ) : ?>
				<?php echo today_get_sidebar_markup( $term ); ?>
			<?php else : ?>
				<?php echo today_display_sidebar_external_stories(); ?>
				<?php echo today_display_sidebar_events(); ?>
				<?php echo today_display_sidebar_menu(); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
