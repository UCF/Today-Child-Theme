<?php

/**
 * Returns markup for a story's author bio.
 * *
 * @since 1.0.12
 * @author RJ Bruneel
 * @return string Author bio markup
 */
function amp_add_author_after_content() {

    global $post;

    $author_data = today_get_post_author_data( $post );
    $author_byline     = $author_data['name'] ?? null;

    ob_start();      
	if ( $author_byline ) :
	    $author_bio  = $author_data['bio'] ?? null;
        $author_title      = $author_data['title'] ?? null;
        $author_photo_data = $author_data['photo'] ?? null;
        $author_photo      = $author_photo_data['sizes']['medium'] ?? null;
        $author_photo_w    = $author_photo_data['sizes']['medium-width'] ?? null;
        $author_photo_h    = $author_photo_data['sizes']['medium-height'] ?? null;
		$author_photo_dims = '';
		if ( $author_photo_w ) {
			$author_photo_dims .= 'width="' . $author_photo_w . '" ';
		}
		if ( $author_photo_h ) {
			$author_photo_dims .= 'height="' . $author_photo_h . '"';
		}
 
?>
        <div class="sp-athr">
            <div class="sp-rl">

                <?php if ( $author_photo ) : ?>
                    <div class="sp-lt">
                        <amp-img src="<?php echo $author_photo; ?>" <?php echo $author_photo_dims; ?>
                            layout="responsive" alt="<?php echo $author_byline; ?>">
                        </amp-img>
                    </div>
                <?php endif; ?>

                <div class="sp-rt">
                    <div class="srp">
                        <strong><?php echo $author_byline; ?></strong>
                    </div>

                    <?php if ( $author_title ) : ?>
                        <div>
                            <?php echo $author_title; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $author_bio ) : ?>
                        <div class="srp" style="font-style: italic; font-size: .9em;">
                            <?php echo $author_bio; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
<?php
	endif;
    echo ob_get_clean();
}

add_action( 'ampforwp_after_post_content', 'amp_add_author_after_content' );
