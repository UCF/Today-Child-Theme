<?php
/**
 * Functions related to Pegasus stories and their
 * display on the frontend
 */


/**
 * TODO
 */
function today_get_pegasus_current_issue() {
	return true;
}


/**
 * Returns featured posts markup for the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup
 */
function today_get_pegasus_home_featured( $post_id ) {
	return today_get_homepage_curated( $post_id, true );
}


/**
 * TODO
 */
function today_get_pegasus_home_in_this_issue( $post_id ) {
	$issue        = today_get_pegasus_current_issue();
	$content_type = get_field( 'issue_content_type', $post_id );
	$content      = '<div class="row">';

	if ( $issue ) {
		ob_start();
	?>
		<div class="col-8 offset-2 col-sm-6 offset-sm-3 col-md-3 offset-md-0 mb-5 mb-md-0 pr-md-4 pr-lg-5 text-center">
			<h2 class="h6 text-uppercase letter-spacing-2 mb-3">In This Issue</h2>
			<a href="#TODO">
				<img class="img-fluid" src="https://placehold.it/320x416/" alt="">
				<strong class="text-uppercase letter-spacing-2 text-secondary d-block mt-3" style="font-size: .8em;">TODO Issue Name Here</strong>
			</a>
			<hr class="hr-primary hr-3 w-25" role="presentation">
		</div>
		<div class="col-md-9">
	<?php
		$content .= ob_get_clean();
	} else {
		$content .= '<div class="col">';
	}

	switch ( $content_type ) {
		case 'curated':
			$content .= today_get_homepage_curated( $post_id, false );
			break;
		case 'latest':
		default:
			$posts = get_posts( array(
				// TODO filter by issue; remove numberposts
				'numberposts' => 12
			) );
			ob_start();
		?>
			<?php if ( $posts ): ?>
			<div class="row">
				<?php foreach ( $posts as $post ): ?>
					<div class="col-md-4 mb-4">
						<?php echo today_display_feature_vertical( $post ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php else: ?>
			<p>No results found.</p>
			<?php endif; ?>
		<?php
			$content .= ob_get_clean();
			break;
	}

	$content .= '</div></div>';

	return $content;
}
