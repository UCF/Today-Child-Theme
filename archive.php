<?php
/**
 * Template Name: Archives
 */
get_header();
?>

<div class="container">
	<div class="row">
		<div class="col-md-8">
			<?php today_archive_pagination(); ?>
			<?php if ( have_posts() ) : ?>
				<?php echo today_post_list_display_feature( '', $posts, array( 'posts_per_row' => 3, 'layout' => 'vertical' ) ); ?>
				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>There are currently no stories for <?php echo date( 'F Y' ); ?>.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
		<h2 class="h6 text-uppercase text-default-aw mb-4">Resources</h2>
		<ul class="list-unstyled">
			<li><a href="about-ucf-today/">About UCF Today</a></li>
			<li><a href="reporting-on-ucf/">Reporting on UCF</a></li>
			<li><a href="news-archive/">Recent News Stories</a></li>
			<li><a href="https://newsarchive.smca.ucf.edu/" target="_blank">Previous News Archives</a></li>
			<li><a href="https://www.ucf.edu/pegasus" target="_blank">Pegasus Magazine</a></li>
			<li><a href="https://www.ucf.edu/downtown/" target="_blank">UCF Downtown Orlando</a></li>
			<li><a href="https://www.ucf.edu/impact/" target="_blank">UCF Impact - Transforming Lives</a></li>
		</ul>
		</div>
	</div>
</div>

<?php get_footer(); ?>
