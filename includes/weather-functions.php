<?php
/**
 * UCF Weather Shortcode Related Functions
 */


/**
 * Display nav weather data.
 *
 * Adapted from Today-Bootstrap
 *
 * @since 1.0.0
 * @return string
 **/
function today_output_nav_weather_data() {
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
function today_weather_default_today_nav( $data, $output ) {
	if ( ! class_exists( 'UCF_Weather_Common' ) ) return;
	if ( ! is_object( $data ) || property_exists( $data, 'condition' ) ) return;

	ob_start();
	$icon = UCF_Weather_Common::get_weather_icon( $data->condition );
?>
	<div class="weather weather-today-nav py-4 mb-4 py-lg-0 my-lg-0">
		<span class="weather-date hidden-lg-up d-block mb-3 text-uppercase letter-spacing-1"><?php echo date( 'l, F j, Y' ); ?></span>
		<span class="weather-status d-flex flex-row">
			<span class="weather-icon text-primary mr-3 mr-lg-2 <?php echo $icon; ?>" aria-hidden="true"></span>
			<span class="weather-text d-flex flex-column align-items-start flex-lg-row align-items-lg-center">
				<span class="weather-temp font-weight-bold mr-0 mr-lg-2"><?php echo $data->temp; ?>F</span>
				<span class="weather-condition"><?php echo $data->condition; ?></span>
			</span>
		</span>
	</div>
<?php
	return ob_get_clean();
}

add_filter( 'ucf_weather_default_today_nav', 'today_weather_default_today_nav', 10, 2 );
