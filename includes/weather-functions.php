<?php
/**
 * Handle all UCF Weather Shortcode related functions here
 */


/**
 * Display nav weather data.
 *
 * Adapted from Today-Bootstrap
 *
 * @since 1.0.0
 * @return string
 **/
function output_nav_weather_data() {
	return do_shortcode( '[ucf-weather feed="default" layout="today_nav"]' );
}


/**
 * Custom layout for the UCF Weather Shortcode plugin for
 * displaying weather data in the site header.
 *
 * Adapted from Today-Bootstrap
 *
 * @since 1.0.0
 * @param object $data Weather data
 * @param string $output HTML output
 * @return string HTML markup
 */
function ucf_weather_default_today_nav( $data, $output ) {
	if ( ! class_exists( 'UCF_Weather_Common' ) ) return;

	ob_start();
	$icon = UCF_Weather_Common::get_weather_icon( $data->condition );
?>
	<div class="weather weather-today-nav">
		<span class="weather-date hidden-lg-up"><?php echo date( 'l, F j, Y' ); ?></span>
		<span class="weather-status">
			<span class="weather-icon <?php echo $icon; ?>" aria-hidden="true"></span>
			<span class="weather-text">
				<span class="weather-temp"><?php echo $data->temp; ?>F</span>
				<span class="weather-condition"><?php echo $data->condition; ?></span>
			</span>
		</span>
	</div>
<?php
	return ob_get_clean();
}

add_filter( 'ucf_weather_default_today_nav', 'ucf_weather_default_today_nav', 10, 2 );
