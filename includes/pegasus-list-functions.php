<?php
/**
 * Functions that define the Pegasus list Today Sidebar layout
 */

function today_sidebar_pegasus_list_before( $content, $items, $args ) {
	ob_start();
?>
	<div class="ucf-pegasus-list ucf-pegasus-list-today-sidebar">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_pegasus_list_display_today_sidebar_before', 'today_sidebar_pegasus_list_before', 10, 3 );

function today_sidebar_pegasus_list_content( $content, $items, $args, $fallback_message='' ) {
	if ( $items && ! is_array( $items ) ) { $items = array( $items ); }

	ob_start();
?>
	<?php if ( $items ) : ?>
		<?php
		foreach ( $items as $item ) :
			$issue_url   = $item->link;
			$issue_title = $item->title->rendered;
			$cover_story = $item->_embedded->issue_cover_story[0];
			$cover_story_url = $cover_story->link;
			$cover_story_title = $cover_story->title->rendered;
			$cover_story_subtitle = $cover_story->story_subtitle;
			$cover_story_description = $cover_story->story_description;
			$cover_story_blurb = null;
			$thumbnail_id = $item->featured_media;
			$thumbnail = null;
			$thumbnail_url = null;

			if ( $thumbnail_id !== 0 ) {
				$thumbnail = $item->_embedded->{"wp:featuredmedia"}[0];
				$thumbnail_url = $thumbnail->media_details->sizes->full->source_url;
			}
			if ( $cover_story_description ) {
				$cover_story_blurb = $cover_story_description;
			} else if ( $cover_story_subtitle ) {
				$cover_story_blurb = $cover_story_subtitle;
			}
		?>
		<div class="ucf-pegasus-list-issue position-relative text-center mb-2 mb-md-3">
			<img class="img-fluid" src="<?php echo $thumbnail_url; ?>" alt="<?php echo $issue_title; ?>">
			<div class="ucf-pegasus-list-issue-title">
				<a class="d-inline-block text-secondary h5 stretched-link my-2" href="<?php echo $issue_url; ?>" target="_blank">
					<?php echo $issue_title; ?>
				</a>
			</div>
		</div>

		<div class="ucf-pegasus-list-featured-story position-relative d-flex flex-column mb-5">
			<p class="mb-2 text-muted text-uppercase small">Featured Story</p>
			<a class="h4 text-secondary stretched-link mb-2" href="<?php echo $cover_story_url; ?>" target="_blank">
				<?php echo $cover_story_title; ?>
			</a>
			<p class="mb-3"><?php echo $cover_story_blurb; ?></p>
			<span class="btn btn-sm btn-primary ml-auto">
				Read More
			</span>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>

<?php
	return ob_get_clean();
}

add_filter( 'ucf_pegasus_list_display_today_sidebar_content', 'today_sidebar_pegasus_list_content', 10, 4 );

function today_sidebar_pegasus_list_after( $content, $items, $args ) {
	ob_start();
?>
	</div>
<?php
	return ob_get_clean();
}

add_filter( 'ucf_pegasus_list_display_today_sidebar_after', 'today_sidebar_pegasus_list_after', 10, 3 );

function today_sidebar_pegasus_add_layout( $layouts ) {
	if ( ! isset( $layouts['today_sidebar'] ) ) {
		$layouts['today_sidebar'] = 'Today Sidebar Layout';
	}

	return $layouts;
}

add_filter( 'ucf_pegasus_list_get_layouts', 'today_sidebar_pegasus_add_layout', 10, 1 );
