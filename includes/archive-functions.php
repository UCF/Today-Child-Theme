<?php
/**
 * Provides functions specifically for archive pages
 */
if ( ! function_exists( 'today_archive_pagination' ) ) {
	function today_archive_pagination() {
		if ( ! is_date() ) return;

		global $wp_query;

		$current_year  = isset( $wp_query->query['year'] ) ? $wp_query->query['year'] : null;
		$current_month = isset( $wp_query->query['monthnum'] ) ? $wp_query->query['monthnum'] : null;

		$current_date = date_create_from_format( 'm/Y', "$current_month/$current_year" );

		$prev_month = $current_date->sub( new DateInterval( 'P1M' ) );
		$prev_url = get_month_link( $prev_month->format('Y'), $prev_month->format('m') );

		$args = array(
			'monthnum' => $prev_month->format('m'),
			'year'     => $prev_month->format('Y')
		);

		$posts = new WP_Query( $args );
		$prev_have_posts = $posts->post_count > 0 ? true : false;

		// Reset $current_date as it's effected by ->sub
		$current_date = date_create_from_format( 'm/Y', "$current_month/$current_year" );

		$next_month = $current_date->add( new DateInterval( 'P1M' ) );
		$next_url = get_month_link( $next_month->format('Y'), $next_month->format('m') );

		$args = array(
			'monthnum' => $next_month->format('m'),
			'year'     => $next_month->format('Y')
		);

		$posts = new WP_Query( $args );
		$next_have_posts = $posts->post_count > 0 ? true : false;

		ob_start();
	?>
		<div class="row justify-content-around mt-3 mb-5">
			<?php if ( $prev_have_posts ) : ?>
			<div class="col">
				<a href="<?php echo $prev_url; ?>" class="btn btn-primary"><span class="fa fa-arrow-left"></span> <?php echo $prev_month->format( 'M Y' ); ?></a>
			</div>
			<?php endif; ?>
			<?php if ( $next_have_posts ) : ?>
			<div class="col text-right">
				<a href="<?php echo $next_url; ?>" class="btn btn-primary"><?php echo $next_month->format( 'M Y' ); ?> <span class="fa fa-arrow-right"></span> </a>
			</div>
			<?php endif; ?>
			<hr>
		</div>
	<?php
		echo ob_get_clean();
	}
}
