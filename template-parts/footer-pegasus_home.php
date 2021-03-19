<?php
if (
	is_active_sidebar( 'pegasus_home-footer-col-1' )
	|| is_active_sidebar( 'pegasus_home-footer-col-2' )
	|| is_active_sidebar( 'pegasus_home-footer-col-3' )
	|| is_active_sidebar( 'pegasus_home-footer-col-4' )
):
?>
<footer class="site-footer pegasus-footer bg-inverse pt-4 py-md-5 mt-4" aria-label="Site footer">
	<div class="container mt-4">
		<div class="row">

			<?php if ( is_active_sidebar( 'pegasus_home-footer-col-1' ) ): ?>
			<div class="col-12 col-lg">
				<div class="text-primary" style="max-width: 14rem;">
					<?php echo today_get_pegasus_logo(); ?>
				</div>
				<?php dynamic_sidebar( 'pegasus_home-footer-col-1' ); ?>
			</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'pegasus_home-footer-col-2' ) ): ?>
			<div class="col-12 col-lg">
				<?php dynamic_sidebar( 'pegasus_home-footer-col-2' ); ?>
			</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'pegasus_home-footer-col-3' ) ): ?>
			<div class="col-12 col-lg">
				<?php dynamic_sidebar( 'pegasus_home-footer-col-3' ); ?>
			</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'pegasus_home-footer-col-4' ) ): ?>
			<div class="col-12 col-lg">
				<?php dynamic_sidebar( 'pegasus_home-footer-col-4' ); ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
</footer>
<?php endif; ?>
