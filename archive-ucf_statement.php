<?php get_header(); ?>

<?php
$statements_view = new Statements_View();
$filters      	 = null; // TODO $statements_view->get_statement_filters() ?? '';
?>

<div class="container mt-4 mt-md-5 pb-4 pb-md-5">
	<div class="row">
		<?php if ( $filters ) : ?>
		<div class="col-lg-4 mb-4 mb-lg-0">
			<?php // echo $filters; ?>
		</div>
		<div class="col-auto hidden-md-down pr-lg-4">
			<hr class="hr-vertical" role="presentation">
		</div>
		<?php endif; ?>

		<div class="col">
			<p class="lead mb-4">An archive of statements by University of Central Florida leadership addressing matters of importance to the university community.</p>

			<?php if ( have_posts() ) : ?>
				<ul class="mt-4 mt-sm-5 mb-5 list-unstyled">
					<?php while ( have_posts() ): the_post(); ?>
						<?php echo $statements_view->get_statements_list_item( $post ); ?>
					<?php endwhile; ?>
				</ul>
				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div>
					<p>No statements available.</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
