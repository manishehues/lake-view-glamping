<?php
/**
 * The template for displaying search room item loop.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/loop.php.
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
$gallery         = $room->gallery;
$featured        = $gallery ? array_shift( $gallery ) : false;
$single_purchase = get_option( 'tp_hotel_booking_single_purchase' );
?>

<li class="hb-room clearfix">
	<form name="hb-search-results"
		  class="hb-search-room-results <?php echo ( $single_purchase ) ? 'single-purchase' : ''; ?>">
		<?php do_action( 'hotel_booking_loop_before_item', $room->post->ID ); ?>
		<div class="hb-room-content">
			<div class="hb-room-thumbnail col">
				<?php if ( $featured ): ?>
					<a class="hb-room-gallery"
					   
					   data-title="<?php echo esc_attr( $featured['alt'] ); ?>"
					   href="<?php echo get_the_permalink( $room->ID ) ?>?check_in_date=<?php echo hb_get_request( 'check_in_date' )?>&check_out_date=<?php echo hb_get_request( 'check_out_date' )?>&capacity=<?php echo esc_html( $room->capacity )?>">
						<?php $room->getImage( 'catalog' ); ?>
					</a>
				<?php endif; ?>
			</div>
			<div class="hb-room-info col">
				<h3 class="domeName">
					<a href="<?php echo get_the_permalink( $room->ID ) ?>?check_in_date=<?php echo hb_get_request( 'check_in_date' )?>&check_out_date=<?php echo hb_get_request( 'check_out_date' )?>&capacity=<?php echo esc_html( $room->capacity )?>">
						<?php echo esc_html( $room->name ); ?>
						<!-- <?php $room->capacity_title ? printf( '(%s)', $room->capacity_title ) : ''; ?> -->
					</a>
				</h3>
				<div class="selectedDates">
					<div class="small checkIn"><span>Check-In :</span> <strong><?php echo hb_get_request( 'check_in_date' )?></strong></div>
					<div class="small checkOut"><span>Check-Out :</span> <strong><?php echo hb_get_request( 'check_out_date' )?></strong></div>
					<div class="small adults"><span>Adults :</span> <strong><?php echo esc_html( $room->capacity ); ?></strong></div>
				</div>
				<div class="smallDescr">
					<?php echo get_post_meta($room->ID,'small_description', true); ?>
				</div>								
			</div>
			<div class="hb-room-prising col">
				<ul class="hb-room-meta">
					<!-- <li class="hb_search_capacity">
						<label><?php _e( 'Adults:', 'wp-hotel-booking' ); ?></label>
						<div class=""><?php echo esc_html( $room->capacity ); ?></div>
					</li>
					<li class="hb_search_max_child">
						<label><?php _e( 'Max Children:', 'wp-hotel-booking' ); ?></label>
						<div><?php echo esc_html( $room->max_child ); ?></div>
					</li> -->
					<li class="hb_search_price">
						<p><?php _e( 'Price:', 'wp-hotel-booking' ); ?></p>
						<h2
							class="hb_search_item_price"><?php echo hb_format_price( $room->amount_singular_exclude_tax ); ?></h2>

							<?php //print_r($room->amount_singular_exclude_tax);?>
						<div class="hb_view_price">
							<a href=""
							class="hb-view-booking-room-details"><?php _e( 'Price Detail', 'wp-hotel-booking' ); ?></a>
							<?php hb_get_template( 'search/booking-room-details.php', array( 'room' => $room ) ); ?>
						</div>
					</li>
					<?php if ( ! $single_purchase ) { ?>
						<li class="hb_search_quantity">
							<label><?php _e( 'Quantity: ', 'wp-hotel-booking' ); ?></label>
							<div>
								<?php
								hb_dropdown_numbers(
									array(
										'name'             => 'hb-num-of-rooms',
										'min'              => 1,
										'show_option_none' => __( 'Select', 'wp-hotel-booking' ),
										'max'              => $room->post->available_rooms,
										'class'            => 'number_room_select'
									)
								);
								?>
							</div>
						</li>
					<?php } else { ?>
						<select name="hb-num-of-rooms" class="number_room_select" style="display: none;">
							<option value="1">1</option>
						</select>
					<?php } ?>
					<?php do_action( 'hotel_booking_loop_before_btn_select_room', $room->post->ID ); ?>
					<li class="hb_search_add_to_cart">
						<button class="hb_add_to_cart btn btn-primary"><?php _e( 'Book Now', 'wp-hotel-booking' ) ?></button>
					</li>
				</ul>
			</div>
		</div>

		<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
		<input type="hidden" name="check_in_date"
			   value="<?php echo hb_get_request( 'check_in_date' ); ?>"/>
		<input type="hidden" name="check_out_date"
			   value="<?php echo hb_get_request( 'check_out_date' ); ?>">
		<input type="hidden" name="room-id" value="<?php echo esc_attr( $room->post->ID ); ?>">
		<input type="hidden" name="hotel-booking" value="cart">
		<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>

		<?php do_action( 'hotel_booking_loop_after_item', $room->post->ID ); ?>
	</form>

	<?php if ( ( isset( $atts['gallery'] ) && $atts['gallery'] === 'true' ) || $hb_settings->get( 'enable_gallery_lightbox' ) ) { ?>
		<?php hb_get_template( 'loop/gallery-lightbox.php', array( 'room' => $room ) ) ?>
	<?php } ?>
</li>