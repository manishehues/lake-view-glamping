<?php
/**
 * WP Hotel Booking Block Room Functions
 *
 * Define common functions for both front-end and back-end
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Block/Functions
 * @version  1.7.4
 */

if ( ! function_exists( 'hotel_block_convert_current_time' ) ) {
	/**
	 * @param null $time
	 * @param int $gmt
	 *
	 * @return float|int|null
	 */
	function hotel_block_convert_current_time( $time = null, $gmt = 0 ) {
		if ( ! $time ) {
			$time = time();
		}

		if ( ! $gmt ) {
			return $time + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		} else {
			return $time - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) - 12 * HOUR_IN_SECONDS;
		}
	}
}
