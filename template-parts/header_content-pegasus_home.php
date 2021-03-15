<?php
/**
 * Header content template for the 'Pegasus Home' page template
 */

$subtitle     = today_get_theme_mod_or_default( 'pegasus_subtitle' );
$about_url    = today_get_theme_mod_or_default( 'pegasus_about_url' );
$archives_url = today_get_theme_mod_or_default( 'pegasus_archives_url' );
$share_links  = do_shortcode( '[ucf-social-icons size="sm"]' );
$share_links  = ucfwp_is_content_empty( $share_links ) ? '' : $share_links;
?>

<div class="container d-flex flex-column text-center mt-4 mt-md-5">
	<div class="row align-items-center">
		<div class="col-8 offset-2 col-sm-8 offset-sm-0 col-lg-7 col-xl-6 flex-last flex-sm-unordered px-4 px-sm-5">
			<h1 class="display-3 font-weight-normal text-uppercase text-default mb-3 px-md-5">
				<img class="pegasus-header-logo img-fluid" src="<?php echo TODAY_THEME_IMG_URL; ?>/pegasus-logo.svg" alt="Pegasus" width="769" height="109">
			</h1>
			<?php if ( $subtitle && $about_url ) : ?>
			<a class="font-slab-serif text-uppercase text-secondary letter-spacing-2" href="<?php echo $about_url; ?>">
				<?php echo $subtitle; ?>
			</a>
			<?php endif; ?>
		</div>
		<div class="col mb-4 mb-sm-0 flex-sm-first">
			<a class="text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="#TODO">
				<!-- TODO retrieve latest issue, once issues are implemented -->
				TODO latest issue
			</a>
		</div>
		<div class="col mb-4 mb-sm-0">
			<?php if ( $archives_url ) : ?>
			<a class="text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="<?php echo $archives_url; ?>">
				Archives
			</a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $share_links ) : ?>
	<div class="row flex-lg-first align-self-center align-self-lg-end mt-4 mb-2 mb-sm-4 mt-lg-0">
		<div class="col d-flex flex-row align-items-center">
			<strong class="small font-weight-bold letter-spacing-2 text-default text-uppercase mr-2">
				Share
			</strong>
			<?php echo do_shortcode( '[ucf-social-icons size="sm"]' ); ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="row hidden-xs-down">
		<div class="col-12">
			<hr role="presentation" class="mt-3 mt-lg-5 mb-0">
			<hr role="presentation" class="hr-black hr-3 mt-1 mb-2">
		</div>
	</div>
</div>
