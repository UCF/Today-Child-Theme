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
	<div class="card mb-4">
		<div class="card-block">
			<div class="row">
				<?php if ( $prev_have_posts ) : ?>
				<div class="col-12 col-sm-6">
					<a href="<?php echo $prev_url; ?>" class="btn btn-primary">Previous Month</a>
				</div>
				<?php endif; ?>
				<div class="col-12 col-sm-6">
				<?php if ( $next_have_posts ) : ?>
				<div class="col-12 col-sm-6">
					<a href="<?php echo $next_url; ?>" class="btn btn-primary">Next Month</a>
				</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
		echo ob_get_clean();
	}
}
