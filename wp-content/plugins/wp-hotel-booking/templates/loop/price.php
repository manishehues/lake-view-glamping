<?php
/**
 * The template for displaying loop room price in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/price.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_settings;
/**
 * @var $hb_settings WPHB_Settings
 */
$price_display = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get( 'price_display' ) );
$prices        = hb_room_get_selected_plan( get_the_ID() );
$prices        = isset( $prices->prices ) ? $prices->prices : array();
?>


<?php if(hb_get_request( 'check_in_date') !="" && hb_get_request( 'check_out_date' )): ?>
	<div class="selectedDates">
		<div class="small checkIn"><span>Check-In :</span> <strong><?php echo hb_get_request( 'check_in_date')?></strong></div>
		<div class="small checkOut"><span>Check-Out :</span> <strong><?php echo hb_get_request( 'check_out_date' )?></strong></div>
		<div class="small adults"><span>Adults :</span> <strong><?php echo hb_get_request( 'capacity' ); ?></strong></div>
	</div>
<?php endif; ?>

<?php if ( $prices ) {
	$min_price = is_numeric( min( $prices ) ) ? min( $prices ) : 0;
	$max_price = is_numeric( max( $prices ) ) ? max( $prices ) : 0;
	$min = $min_price;// + ( hb_price_including_tax() ? ( $min_price * hb_get_tax_settings() ) : 0 );
	$max = $max_price + ( hb_price_including_tax() ? ( $max_price * hb_get_tax_settings() ) : 0 );
	?>
	<form name="hb-search-results" class="hb-search-room-results single-purchase">
		<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>

		<select name="hb-num-of-rooms" class="number_room_select" style="display: none;">
			<option value="1">1</option>
		</select>
		<input type="hidden" name="check_in_date" value="<?php echo hb_get_request( 'check_in_date' );?>">
		<input type="hidden" name="check_out_date" value="<?php echo hb_get_request( 'check_out_date' );?>">
		<input type="hidden" name="room-id" value="<?php echo get_the_ID();?>">
		<input type="hidden" name="hotel-booking" value="cart">
		<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart">

		<div class="price">
			<!-- <span class="title-price"><?php _e( 'From', 'wp-hotel-booking' ); ?></span> -->

			<?php if ( $price_display === 'max' ) { ?>
				<h2 class="price_value price_max"><?php echo hb_format_price( $max ) ?></h2>

			<?php } elseif ( $price_display === 'min_to_max' && $min !== $max ) { ?>
				<h2 class="price_value price_min_to_max">
					<?php echo hb_format_price( $min ) ?> - <?php echo hb_format_price( $max ) ?>
				</h2>

			<?php } else { ?>
				<h2 class="price_value price_min"><?php echo hb_format_price( $min ) ?></h2>
			<?php } ?>

			<span class="unit"><?php _e( 'Per Night', 'wp-hotel-booking' ); ?></span>
		</div>

		<div class="btnArea">

			<?php if(hb_get_request( 'check_in_date') ==NULL || hb_get_request( 'check_out_date' ) == NULL): ?>
				<a href="<?php echo site_url('/booking');?>" class="hb_add_to_cart btn btn-primary">Book <?php the_title(); ?></a href="">
			
			<?php else : ?>
				
				<button class="hb_add_to_cart btn btn-primary">Book <?php the_title(); ?></button>
			<?php endif; ?>

		</div>
	</form>


<?php } ?>