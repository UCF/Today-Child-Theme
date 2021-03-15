<?php
/**
 * Header content template for the 'Pegasus Home' page template
 * TODO
 */
?>

<?php
global $post;

$title = wptexturize( $post->post_title );
?>

<?php if ( $title ): ?>
<div class="container d-flex flex-column text-center mt-4 mt-md-5">
	<div class="row align-items-center">
		<div class="col mb-4 mb-sm-0">
			<a class="text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="#TODO">
				TODO latest issue
			</a>
		</div>
		<div class="col-12 col-sm-8 flex-last flex-sm-unordered">
			<!-- TODO svg logo instead of text here -->
			<h1 class="display-3 font-weight-normal text-uppercase text-default mb-2">
				<?php echo $title; ?>
			</h1>
			<a class="font-slab-serif text-uppercase text-secondary letter-spacing-2" href="#TODO">
				TODO about blurb
			</span>
		</div>
		<div class="col mb-4 mb-sm-0">
			<a class="text-secondary font-weight-bold text-uppercase letter-spacing-2" style="font-size: .8em;" href="#TODO">
				Archives
			</a>
		</div>
	</div>
	<div class="row flex-lg-first align-self-center align-self-lg-end mt-4 mb-2 mb-sm-4 mt-lg-0">
		<div class="col d-flex flex-row align-items-center">
			<strong class="small font-weight-bold letter-spacing-2 text-default text-uppercase mr-2">
				Share
			</strong>
			<?php echo do_shortcode( '[ucf-social-icons size="sm"]' ); ?>
		</div>
	</div>

	<div class="row hidden-xs-down">
		<div class="col-12">
			<hr role="presentation" class="mt-3 mt-lg-5 mb-0">
			<hr role="presentation" class="hr-black hr-3 mt-1 mb-2">
		</div>
	</div>
</div>
<?php endif; ?>
