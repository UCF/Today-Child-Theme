<?php
/**
 * Template Name: Pegasus Homepage
 * Template Post Type: page
 */
?>
<?php get_header(); the_post(); ?>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<?php echo today_get_pegasus_home_featured( $post->ID, true ); ?>
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-lg-8 d-md-flex flex-md-column mb-5 mb-md-0 pt-md-4">
				<h2 class="font-weight-black mb-4 ml-sm-2">
					The Feed<span class="fa fa-caret-right text-primary ml-2" aria-hidden="true"></span>
				</h2>
				<?php echo today_get_pegasus_home_feed( $post->ID ); ?>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="card border-0 bg-faded h-100">
					<div class="card-block px-4 pt-4 pb-2">
						<h2 class="h6 heading-underline letter-spacing-2 mb-4">What's Trending</h2>
						<?php echo today_get_pegasus_home_trending( $post->ID ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<?php echo today_get_pegasus_home_in_this_issue( $post->ID ); ?>
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		TODO banner content here
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<div class="row">
			<div class="col-lg pt-lg-4 pr-lg-5 mb-5 mb-lg-0">
				<!-- TODO configuration options -->
				<h2 class="font-weight-black">Events</h2>
				<hr role="presentation">
				<?php echo do_shortcode( '[ucf-events title="" layout="classic"]' ); ?>
			</div>
			<div class="col-lg">
				<!-- TODO configuration options -->
				<div class="card border-0 bg-faded mx-auto">
					<div class="card-block p-4">
						<a href="#TODO">
							<h2 class="text-secondary">TODO Featured Gallery Name</h2>
							<span class="badge badge-primary">TODO Category Name</span>
							<img class="mt-3 img-fluid" src="https://placehold.it/767x600" alt="TODO">
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
