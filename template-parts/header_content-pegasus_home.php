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

<div class="container d-flex flex-column text-center mt-4">
	<div class="row align-items-center pb-4 pb-md-0 pt-md-4">
		<div class="col-12 col-md-8 col-xl-6 flex-last flex-md-unordered px-4 px-sm-5">
			<h1 class="text-default mb-3 px-4 px-md-5 mx-lg-5 mx-xl-0">
				<?php echo today_get_pegasus_logo(); ?>
			</h1>
			<?php if ( $subtitle && $about_url ) : ?>
			<a class="nav-link px-0 font-slab-serif text-uppercase text-secondary letter-spacing-2" href="<?php echo $about_url; ?>">
				<?php echo $subtitle; ?>
			</a>
			<?php endif; ?>
		</div>
		<div class="col-6 col-md-2 col-xl-3 mb-4 mb-lg-0 flex-md-first text-left text-md-center">
			<a class="nav-link px-0 text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="#TODO">
				<!-- TODO retrieve latest issue, once issues are implemented -->
				TODO latest issue
			</a>
		</div>
		<div class="col-6 col-md-2 col-xl-3 mb-4 mb-lg-0 text-right text-md-center">
			<?php if ( $archives_url ) : ?>
			<a class="nav-link px-0 text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="<?php echo $archives_url; ?>">
				Archives
			</a>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $share_links ) : ?>
	<div class="flex-md-first align-self-center align-self-md-end">
		<div class="d-flex flex-row align-items-center">
			<strong class="d-block small font-weight-bold letter-spacing-2 text-default text-uppercase mr-2">
				Share
			</strong>
			<?php echo $share_links; ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="row hidden-sm-down">
		<div class="col-12">
			<hr role="presentation" class="mt-3 mt-md-5 mb-0">
			<hr role="presentation" class="hr-black hr-3 mt-1 mb-2">
		</div>
	</div>
</div>
