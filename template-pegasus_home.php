<?php
/**
 * Template Name: Pegasus Homepage
 * Template Post Type: page
 */
?>
<?php
get_header(); the_post();

$featured      = today_get_pegasus_home_featured( $post->ID, true );
$the_feed      = today_get_pegasus_home_feed( $post->ID );
$trending      = today_get_pegasus_home_trending( $post->ID );
$in_this_issue = today_get_pegasus_home_in_this_issue( $post->ID );
$events        = today_get_pegasus_home_events( $post->ID );
?>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<?php echo $featured; ?>
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<div class="row">
			<div class="col d-md-flex flex-md-column mb-5 mb-md-0 <?php if ( $trending ) { ?>pt-md-4<?php } ?>">
				<h2 class="font-weight-black mb-4 ml-sm-2">
					The Feed<span class="fa fa-caret-right text-primary ml-2" aria-hidden="true"></span>
				</h2>
				<?php echo $the_feed; ?>
			</div>

			<?php if ( $trending ) : ?>
			<div class="col-md-6 col-lg-4">
				<div class="card border-0 bg-faded h-100">
					<div class="card-block px-4 pt-4 pb-2">
						<h2 class="h6 heading-underline letter-spacing-2 mb-4">What's Trending</h2>
						<?php echo $trending; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="jumbotron jumbotron-fluid bg-secondary py-4 mb-0">
	<div class="container">
		<?php echo $in_this_issue; ?>
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
				<h2 class="font-weight-black">Events</h2>
				<hr role="presentation">
				<?php echo $events; ?>
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
