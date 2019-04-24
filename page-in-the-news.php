<?php get_header(); ?>

<?php
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$ext_stories_query = new WP_Query( array(
	'paged'     => $paged,
	'post_type' => 'ucf_resource_link',
	'tax_query' => array(
		array(
			'taxonomy' => 'resource_link_types',
			'field'    => 'slug',
			'terms'    => 'external-story'
		)
	)
) );
?>

<div class="container mt-3 mt-md-4 mb-5 pb-sm-4">
	<div class="row">
		<div class="col-lg-8">
			<?php if ( $ext_stories_query->have_posts() ) : ?>
				<?php while ( $ext_stories_query->have_posts() ): $ext_stories_query->the_post(); ?>
					<div class="pb-2">
						<?php
						echo today_display_feature_horizontal( $post, array(
							'layout'        => 'horizontal',
							'show_image'    => false,
							'show_excerpt'  => true,
							'show_subhead'  => true
						) );
						?>
					</div>
				<?php endwhile; ?>
				<?php
				ucfwp_the_posts_pagination( array(
					'total' => $ext_stories_query->max_num_pages
				) );
				?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>No recent news articles found.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-lg-4 pl-lg-5 mt-4 mt-lg-0">
			<?php echo today_display_sidebar_menu(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
