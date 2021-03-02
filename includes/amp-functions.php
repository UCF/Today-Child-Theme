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
    $author_bio = trim( get_field( 'post_author_bio', $post ) );

    ob_start();

    if ($author_bio) :
        $author_byline     = get_field( 'post_author_byline', $post );
        $author_title      = get_field( 'post_author_title', $post );
        $author_photo_data = get_field( 'post_author_photo', $post );
        $author_photo      = $author_photo_data['sizes']['medium'] ?? null;        
?>
        <div class="sp-athr">
            <div class="sp-rl">

                <?php if ( $author_photo ) : ?>
                    <div class="sp-lt">
                        <amp-img src="<?php echo $author_photo; ?>" width="<?php echo $author_photo_data['sizes']['medium-width']; ?>"
                            height="<?php echo $author_photo_data['sizes']['medium-height']; ?>" layout="responsive" alt="<?php echo $author_byline; ?>"></amp-img>
                    </div>
                <?php endif; ?>

                <div class="sp-rt">
                    <?php if ( $author_byline ) : ?>
                        <div class="srp">
                            <strong><?php echo $author_byline; ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if ( $author_title ) : ?>
                        <div>
                            <?php echo $author_title; ?>
                        </div>
                    <?php endif; ?>

                    <div class="srp" style="font-style: italic; font-size: .9em;">
                        <?php echo $author_bio; ?>
                    </div>
                </div>

            </div>
        </div>
<?php
    endif;
    echo ob_get_clean();
}

add_action( 'ampforwp_after_post_content', 'amp_add_author_after_content' );
