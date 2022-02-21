<?php
/**
 * Responsible for statement archive related functionality
 **/

/**
 * Class that manages listings of statements and their
 * filtering options
 */
class Statements_View {

	/**
 	 * Returns markup for a list of statements.
	 *
	 * @since 3.9.0
	 * @author Jo Dickson
	 * @return string
	 */
	public function get_statements_list_item( $post ) {

		ob_start();
	?>
		<?php if ( $post ) : ?>
			<?php
				$link         = get_permalink( $post );
				$title        = get_the_title( $post );
				$author_id    = get_field( 'post_author_term', $post );
				$author       = ( $author_id && $author_id->term_id ) ? get_term( $author_id->term_id ) : null;
				$author_name  = ( $author && $author->name ) ? $author->name : '';
				$author_title = ( $author ) ? get_field( 'author_title', $author ) : null;
				$author_title = ( $author_title ) ? ', ' . $author_title : '';
				$datetime     = get_the_date();
				$date         = date( 'F j, Y', strtotime( $datetime ) );
			?>
			<li class="mb-4 pb-md-2 d-block">
				<a href="<?php echo $link; ?>">
					<span class="h5 d-block"><?php echo $title; ?></span>
				</a>

				<?php if ( $author ) : ?>
				<cite class="d-block font-italic">
					<?php echo $author_name . $author_title; ?>
				</cite>
				<?php endif; ?>

				<time class="small text-default" datetime="<?php echo $datetime; ?>">
					<?php echo $date; ?>
				</time>
			</li>
		<?php endif; ?>
	<?php
		return trim( ob_get_clean() );
	}

}
