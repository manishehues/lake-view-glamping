<?php
/**
 * The template for displaying search room form.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/search-form.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.7
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$check_in_date  = hb_get_request( 'check_in_date' );
$check_out_date = hb_get_request( 'check_out_date' );
$adults         = hb_get_request( 'adults', 0 );
$max_child      = hb_get_request( 'max_child', 0 );
$uniqid         = uniqid();
?>

<div id="hotel-booking-search-<?php echo uniqid(); ?>" class="hotel-booking-search">
	<?php
	// display title widget or shortcode
	$atts = array();
	if ( $args && isset( $args['atts'] ) ) {
		$atts = $args['atts'];
	} elseif ( isset($args) ) {
		$atts = $args;
    }

	if ( ! isset( $atts['show_title'] ) || strtolower( $atts['show_title'] ) === 'true' ) { ?>
        <h3><?php _e( 'Check Availability', 'wp-hotel-booking' ); ?></h3>
	<?php } ?>

    <form name="hb-search-form" action="<?php echo hb_get_url(); ?>"
          class="hb-search-form-<?php echo esc_attr( $uniqid ) ?>">
        <ul class="hb-form-table">
            <li class="hb-form-field checkIn">				
                <div class="hb-form-field-input hb_input_field">
					<?php hb_render_label_shortcode( $atts, 'show_label', __( 'Check In', 'wp-hotel-booking' ), 'true' ); ?>
                    <input type="text" autocomplete="off" name="check_in_date"  id="check_in_date_<?php echo esc_attr( $uniqid ); ?>"
                           class="hb_input_date_check" value="<?php echo esc_attr( $check_in_date ); ?>"
                           placeholder=""/>
                </div>
            </li>

            <li class="hb-form-field checkOut">				
                <div class="hb-form-field-input hb_input_field">
					<?php hb_render_label_shortcode( $atts, 'show_label', __( 'Check Out', 'wp-hotel-booking' ), 'true' ); ?>
                    <input type="text" name="check_out_date" autocomplete="off" id="check_out_date_<?php echo esc_attr( $uniqid ) ?>"
                           class="hb_input_date_check" value="<?php echo esc_attr( $check_out_date ); ?>"
                           placeholder=""/>
                </div>
            </li>

            <li class="hb-form-field adults">
				<div class="hb-form-field-input hb_input_field">				
					<div class="hb-form-field-input">				
						<?php
						hb_dropdown_numbers(
							array(
								'id'              	=> 'adults_capacity',
								'name'              => 'adults_capacity',
								'required'			=>true,
								'min'               => 1,
								'max'               => hb_get_max_capacity_of_rooms(),
								'show_option_none'  => __( 'Adults', 'wp-hotel-booking' ),
								'selected'          => $adults,
								'option_none_value' => 0,
								'options'           => hb_get_capacity_of_rooms()
							)
						);
						?>
					</div>
				</div>
            </li>

            <!-- <li class="hb-form-field">
				<?php hb_render_label_shortcode( $atts, 'show_label', __( 'Children', 'wp-hotel-booking' ), 'true' ); ?>
                <div class="hb-form-field-input">
					<?php
					hb_dropdown_numbers(
						array(
							'name'              => 'max_child',
							'min'               => 1,
							'max'               => hb_get_max_child_of_rooms(),
							'show_option_none'  => __( 'Children', 'wp-hotel-booking' ),
							'option_none_value' => 0,
							'selected'          => $max_child,
						)
					);
					?>
                </div>
            </li> -->
			<li class="hb-form-field submitBtn">
				<?php wp_nonce_field( 'hb_search_nonce_action', 'nonce' ); ?>
				<input type="hidden" name="hotel-booking" value="results"/>
				<input type="hidden" name="widget-search"
					value="<?php echo isset( $atts['widget_search'] ) ? $atts['widget_search'] : false; ?>"/>
				<input type="hidden" name="action" value="hotel_booking_parse_search_params"/>
				<div class="hb-submit">
					<button class="btn btn-primary" type="submit"><?php _e( 'Search', 'wp-hotel-booking' ); ?></button>
				</div>
			</li>
        </ul>		
    </form>
</div>