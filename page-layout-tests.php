<?php get_header(); the_post(); ?>

<?php $posts = get_posts( array( 'numberposts' => 15 ) ); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mb-5">
		<h2 class="mb-4">Horizontal features</h2>

		<h3 class="mb-4">Primary</h3>
		<?php echo today_display_feature_horizontal( $posts[4], 'primary' ); ?>
		<?php echo today_display_feature_horizontal( $posts[4], 'primary', false ); ?>
		<div class="row">
			<div class="col-lg-7">
				<?php echo today_display_feature_horizontal( $posts[4], 'primary' ); ?>
			</div>
		</div>
		<div class="row mb-5">
			<div class="col-lg-7">
				<?php echo today_display_feature_horizontal( $posts[4], 'primary', false ); ?>
			</div>
		</div>

		<h3 class="mb-4">Secondary</h3>
		<div class="row">
			<div class="col-md-6">
				<?php echo today_display_feature_horizontal( $posts[5] ); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<?php echo today_display_feature_horizontal( $posts[6] ); ?>
			</div>
		</div>
		<div class="row mb-5">
			<div class="col-md-10">
				<?php echo today_display_feature_horizontal( $posts[7] ); ?>
			</div>
		</div>

		<hr class="my-5">

		<h2 class="mb-4">Vertical features</h2>

		<h3 class="mb-4">Primary</h3>
		<?php echo today_display_feature_vertical( $posts[0], 'primary' ); ?>
		<div class="row mt-4 mb-5">
			<div class="col-lg-7">
				<?php echo today_display_feature_vertical( $posts[0], 'primary' ); ?>
			</div>
		</div>

		<h3 class="mb-4">Secondary</h3>
		<div class="row mb-5">
			<div class="col-md-3">
				<?php echo today_display_feature_vertical( $posts[1] ); ?>
			</div>
			<div class="col-md-4">
				<?php echo today_display_feature_vertical( $posts[2] ); ?>
			</div>
			<div class="col-md">
				<?php echo today_display_feature_vertical( $posts[3] ); ?>
			</div>
		</div>

		<hr class="my-5">

		<h2 class="mt-5 mb-4">Condensed features</h2>

		<?php echo today_display_feature_condensed( $posts[8] ); ?>
	</div>
</article>

<?php get_footer(); ?>
